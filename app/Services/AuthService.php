<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:30',
        ])->validate();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
        ]);

        return [
            'user' => UserDto::fromModel($user)->toArray(),
        ];
    }

    public function login(array $data): array
    {
        Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string',
        ])->validate();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Email atau password salah']]);
        }

        return [
            'user' => UserDto::fromModel($user)->toArray(),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
