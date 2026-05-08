<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class AuthService
{
    /**
     * Attempt to log the user in via email/employee ID and password.
     *
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])
                    ->orWhere('employee_id', $credentials['email'])
                    ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $user->update(['last_login_at' => now()]);

        return [
            'user'  => $user,
            'token' => $user->createToken('mobile_app')->plainTextToken
        ];
    }

    /**
     * Generate and send an OTP to the given phone number.
     *
     * @param string $phone
     * @return bool
     * @throws Exception
     */
    public function sendOtp(string $phone): bool
    {
        // Generate a 6 digit mock OTP
        $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Cache the OTP for 5 minutes
        Cache::put('otp_' . $phone, $otp, now()->addMinutes(5));

        // Third-party SMS integration placeholder (e.g., Twilio, Msg91)
        // $twilio->messages->create($phone, ['from' => $from, 'body' => "Your OTP is {$otp}"]);
        
        // Log it for local testing
        Log::info("OTP for {$phone} is: {$otp}");

        return true;
    }

    /**
     * Verify the provided OTP for the given phone number.
     *
     * @param string $phone
     * @param string $otp
     * @return array|null
     */
    public function verifyOtp(string $phone, string $otp): ?array
    {
        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            return null;
        }

        // OTP is valid. Clear it from cache.
        Cache::forget('otp_' . $phone);

        // Find or Create user based on phone
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name'     => 'User ' . substr($phone, -4),
                'password' => Hash::make(str_random(16)), // Random secure password for phone users
            ]
        );

        $user->update(['last_login_at' => now()]);

        return [
            'user'  => $user,
            'token' => $user->createToken('mobile_app')->plainTextToken
        ];
    }
}