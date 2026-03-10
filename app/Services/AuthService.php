<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
}
