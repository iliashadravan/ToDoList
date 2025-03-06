<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthController\ForgetPasswordRequest;
use App\Http\Requests\AuthController\LoginRequest;
use App\Http\Requests\AuthController\RegisterRequest;
use App\Http\Requests\AuthController\UpdateProfileRequest;
use App\Models\User;
use App\Service\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'firstname' => $request->get('firstname'),
            'lastname'  => $request->get('lastname'),
            'email'     => $request->get('email'),
            'password'  => Hash::make($request->get('password')),
            'phone'     => $request->get('phone'),
        ]);
        return response()->json([
            'success' => true,
            'data'    => $user,
            'message' => 'Registration successful'
        ]);
    }
    public function login(LoginRequest $request , SmsService $smsService)
    {
        $user = User::where('phone', $request->get('phone'))->first();

        if (!$user) {
            return response()->json([
                'success' => false,
            ]);
        }
        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'success' => false,
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        $smsService->sendSms(
            $user->phone,
            "سلام {$user->firstname}، شما در تاریخ " . now()->format('Y-m-d') . " ساعت " . now()->format('H:i') . " وارد شدید."
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = $request->only(['firstname', 'lastname', 'phone']);

        $user->update($data);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Update successful'
        ]);
    }
    public function forgetPassword(ForgetPasswordRequest $request , SmsService $smsService)
    {
        $user = User::where('phone', $request->get('phone'))->first();
        if (!$user) {
            return response()->json([
                'success' => false,
            ]);
        }
        $newPassword = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        $smsService->sendSms($user->phone, "رمز عبور جدید شما: {$newPassword}");

        return response()->json([
            'success' => true,
            'message' => 'New password has been sent via SMS!'
        ]);
    }

}
