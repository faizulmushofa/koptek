<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserService
{
    public function all(): array
    {
        return User::all()->map(fn (User $user): array => UserDto::fromModel($user)->toArray())->all();
    }

    public function create(array $data): UserDto
    {
        $data['password'] = Hash::make($data['password']);

        return UserDto::fromModel(User::create($data));
    }

    public function dto(User $user): UserDto
    {
        return UserDto::fromModel($user);
    }

    public function update(User $user, array $data): UserDto
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return UserDto::fromModel($user->fresh());
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function rules(?User $user = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
