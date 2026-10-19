<?php

namespace App\DTOs;

use App\Models\User;

class UserDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self($user->id, $user->name, $user->email, $user->phone);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
