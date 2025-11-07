<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\GoogleRequest;
use App\Http\Requests\RegisterNormalRequest;
use App\Http\Resources\failResource;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // تسجيل الحساب العادي
    public function register(GoogleRequest $request)
    {
        $user = $this->authService->register($request->validated());
        return response()->json(['message' => 'User registered', 'user' => $user], 201);
    }

    // إعادة التوجيه لتسجيل الدخول عبر Google
    public function redirect()
    {
        return $this->authService->redirect();
    }

    // callback بعد تسجيل الدخول عبر Google
public function callBack()
{
    $result = $this->authService->callBack();

    return response()->json([
        'message' => 'Login successful',
        'user' => $result['user'],
        'token' => $result['token'],
    ], 200);
}


    // تسجيل الخروج من Google
    public function logout_google(Request $request)
    {
if ($request->google_token) {
    $success = $this->authService->logout_google($request->google_token);
} else {
    return new failResource('ادخل التوكين');
}

        if ($success) {
            return response()->json(['status' => 200, 'message' => 'Logout successful']);
        }

        return response()->json(['status' => 400, 'message' => 'Invalid Google token']);
    }


public function registerNormal(RegisterNormalRequest $request)
{
    $result = $this->authService->registerNormal($request->validated());

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $result['user'],
        'token' => $result['token'],
    ], 201);
}

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        return  $this->authService->login($credentials, $request);
    }
    // التحقق من وجود التوكن في الكوكيز
    public function checkSession(Request $request)
    {
        return $this->authService->checkSession($request);
    }

    //  تسجيل الخروج
    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    // رفريش للتوكن
    public function refresh(Request $request)
    {

        $token = $this->authService->refresh($request);

        return response()->json($token);
    }

    public function updateProfile(Request $request)
{
    $result = $this->authService->updateProfile($request);

    return response()->json($result, 200);
}

}
