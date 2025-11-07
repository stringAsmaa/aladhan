<?php

namespace App\Http\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\failResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Interfaces\AuthInterface;
use App\Http\Resources\successResource;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthInterface
{
    // تسجيل الحساب العادي
    public function register(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // إعادة التوجيه لتسجيل الدخول عبر Google
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // معالجة callback من Google
    public function callBack()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $email = $googleUser->email;

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $googleUser->name,
                'google_token' => $googleUser->token,
            ]
        );

        $remember_token = Hash::make(Str::random(60));
        session(['remember_token' => $remember_token]);

        $jwt = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $jwt,
        ];
    }


    // تسجيل الخروج من Google
    public function logout_google(string $googleToken)
    {
        $token = DB::table('users')->where('google_token', $googleToken)->first();

        if ($token) {
            DB::table('users')->where('google_token', $googleToken)->delete();
            return true;
        }

        return false;
    }

    public function registerNormal(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }


    public function login(array $credentials, $request)
    {
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return new failResource("الايميل او كلمة المرور غير صحيحين");
        }

        $user = User::where('email', $credentials['email'])->first();
        $userData = [
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ];

        $token = JWTAuth::fromUser($user);

        // فحص هل يجب إرسال التوكن في كوكي
        $useCookie = $request->header('X-Use-Cookie') === 'true';

        $responseData = [
            'user' => $userData,
        ];

        if ($useCookie) {
            return response()->json(new successResource($responseData))
                ->cookie(
                    'jwt_token',
                    $token,
                    60 * 24, // دقيقة
                    '/',
                    null, // النطاق (يمكنكِ تغييره حسب الدومين)
                    true,    // Secure (HTTPS)
                    true,  // HttpOnly
                    false, // raw
                    'None' // SameSite
                );
        }

        // الوضع العادي: نرجع التوكن ضمن JSON
        return new successResource(array_merge($responseData, [
            'access_token' => $token,
        ]));
    }

    public function checkSession(Request $request)
    {
        try {
            // هل التوكن موجود في الكوكي أم في الهيدر؟
            $useCookie = $request->header('X-Use-Cookie') === 'true';

            // جلب التوكن
            $token = $useCookie
                ? $request->cookie('jwt_token')
                : $request->bearerToken();
            // التوكن غير موجود
            if (!$token) {
                return response()->json([
                    'authenticated' => false,
                    'error' => 'لم يتم إرسال التوكن'
                ], 401);
            }

            // محاولة التحقق من التوكن وجلب المستخدم
            $user = JWTAuth::setToken($token)->authenticate();

            // التوكن غير صالح أو المستخدم غير موجود
            if (!$user) {
                return response()->json([
                    'authenticated' => false,
                    'error' => 'المستخدم غير موجود أو التوكن غير صالح'
                ], 401);
            }

            // تحضير بيانات المستخدم
            $userData = [
                'name'   => $user->name,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ];

            // تم التحقق بنجاح
            return response()->json([
                'authenticated' => true,
                'user' => $userData
            ], 200);
        } catch (Exception $e) {
            // تخصيص رسالة الخطأ إذا كانت بسبب انتهاء صلاحية التوكن
            $message = str_contains($e->getMessage(), 'expired')
                ? 'انتهت صلاحية التوكن الخاص بك'
                : 'حدث خطأ أثناء التحقق من الجلسة';

            return response()->json([
                'authenticated' => false,
                'error' => $message
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $useCookie = $request->header('X-Use-Cookie') === 'true';

            // جلب التوكن يدويًا
            $token = $useCookie
                ? $request->cookie('jwt_token')
                : $request->bearerToken();
            //dd(  JWTAuth::setToken($token)->invalidate());
            if (!$token) {
                return response()->json([
                    'message' => 'لا يوجد توكن لتسجيل الخروج'
                ], 400);
            }

            // تمرير التوكن يدويًا لأننا ما عدنا نستخدم auth:api
            JWTAuth::setToken($token)->invalidate();

            // حذف الكوكي إذا كان مستخدم
            if ($useCookie) {
                return response()->json(['message' => 'تم تسجيل الخروج'])
                    ->cookie('jwt_token', '', -1);
            }

            return response()->json(['message' => 'تم تسجيل الخروج']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل تسجيل الخروج',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function refresh($request)
    {
        try {
            $useCookie = $request->header('X-Use-Cookie') === 'true';

            // استخراج التوكن سواء من الكوكي أو الهيدر
            $token = $useCookie
                ? $request->cookie('jwt_token')
                : $request->bearerToken(); // Authorization: Bearer token

            if (!$token) {
                return response()->json(['message' => 'لا يوجد توكن'], 401);
            }

            // مررنا التوكن يدويًا إلى JWTAuth
            $newToken = JWTAuth::setToken($token)->refresh();


            if ($useCookie) {
                return response()->json([
                    'message' => 'refreshed'
                ])
                    ->cookie(
                        'jwt_token',
                        $newToken,
                        60 * 24,
                        '/',
                        null,
                        false,  // ⚠️ خليها false للتجريب محلياً (لو على localhost)
                        true,
                        false,
                        'Lax'   // بدل Strict لتجريب أسهل
                    );
            }

            return response()->json([
                'access_token' => $newToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'انتهت الجلسة أو التوكن غير صالح',
                'error' => $e->getMessage(), // هذا يكشف لكِ الخطأ الحقيقي
            ], 401);
        }
    }

    public function updateProfile(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return [
            'success' => false,
            'message' => 'المستخدم غير مصدق',
        ];
    }
    $validated = $request->validate([
        'name'   => 'nullable|string|max:255',
        'email'  => 'nullable|email|unique:users,email,' . $user->id,
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);
    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $validated['avatar'] = $path;
    }
    $user->update($validated);

    return [
        'success' => true,
        'message' => 'تم تحديث الملف الشخصي بنجاح',
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ],
    ];
}

}
