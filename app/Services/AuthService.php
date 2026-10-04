<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'email' => $validator->errors()->first('email'),
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $userDto = [
            'name' => $user['name'],
            'email' => $user['email'],
        ];

        return [
            'user' => $userDto,
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
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $userDto = [
            'name' => $user['name'],
            'email' => $user['email'],
        ];

        return [
            'user' => $userDto,
            'token' => $token,
        ];
    }
    
}
