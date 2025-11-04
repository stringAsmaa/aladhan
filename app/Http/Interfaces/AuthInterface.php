<?php
namespace App\Http\Interfaces;

interface AuthInterface
{
    // تسجيل الحساب العادي
    public function registerNormal(array $data);

    // تسجيل أو تسجيل الدخول عبر Google
    public function register(array $data);
}
