<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $credentials
     * @return User
     */
    public function register(array $credentials): User
    {
        return User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);
    }

    /**
     * Attempt to login a user.
     *
     * @param array $credentials
     * @return User|null
     */
    public function login(array $credentials): ?User
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Create an API token for a user.
     *
     * @param User $user
     * @param string $deviceName
     * @return string
     */
    public function createToken(User $user, string $deviceName = 'API Token'): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    /**
     * Logout user by revoking all tokens.
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        return (bool) $user->tokens()->delete();
    }

    /**
     * Logout user from all devices.
     *
     * @param User $user
     * @return bool
     */
    public function logoutFromAllDevices(User $user): bool
    {
        return (bool) $user->tokens()->delete();
    }

    /**
     * Send password reset token to user email.
     *
     * @param string $email
     * @return bool
     */
    public function sendPasswordResetToken(string $email): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Create new reset token
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // In a real app, send email here
        // Mail::send('emails.password-reset', ['token' => $token, 'email' => $email], ...);

        // Store token in session or return it (for development/API testing)
        // In production, this would be sent via email
        return true;
    }

    /**
     * Reset user password with token.
     *
     * @param string $email
     * @param string $token
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        // Find reset token record
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return false;
        }

        // Verify token (check if token matches)
        if (!Hash::check($token, $resetRecord->token)) {
            return false;
        }

        // Check if token has expired (24 hours)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        // Update user password
        User::where('email', $email)->update([
            'password' => Hash::make($newPassword),
        ]);

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }

    /**
     * Get password reset token for testing (returns plaintext token).
     * In production, the token should be sent via email.
     *
     * @param string $email
     * @return string|null
     */
    public function getResetTokenForTesting(string $email): ?string
    {
        // This is only for API testing/development
        // In production, tokens are sent via email
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$resetRecord) {
            return null;
        }

        // You would need to store the plaintext token somehow for this to work
        // For now, this returns null in production and should use email instead
        return null;
    }
}
