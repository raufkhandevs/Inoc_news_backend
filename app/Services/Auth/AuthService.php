<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $userData
     * @return User
     */
    public function register(array $userData): User
    {
        return User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);
    }

    /**
     * Attempt to authenticate a user and create a token.
     *
     * @param array $credentials
     * @param string $deviceName
     * @return array
     * 
     * @throws ValidationException
     */
    public function login(array $credentials, string $deviceName = 'api_token'): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens for this device if they exist
        $user->tokens()->where('name', $deviceName)->delete();

        // Create new token
        $token = $user->createToken($deviceName);

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Logout user by revoking tokens.
     *
     * @param User $user
     * @param string|null $deviceName
     * @return bool
     */
    public function logout(User $user, ?string $deviceName = null): bool
    {
        if ($deviceName) {
            // Revoke specific device token
            return (bool) $user->tokens()->where('name', $deviceName)->delete();
        }

        // Revoke all tokens
        return (bool) $user->tokens()->delete();
    }
}