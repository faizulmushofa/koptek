<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(): JsonResponse
    {
        return response()->json(['message' => 'success', 'data' => ['users' => $this->userService->all()]]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->userService->create($request->validate([
            ...$this->userService->rules(),
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]));

        return response()->json(['message' => 'success', 'data' => ['user' => $user->toArray()]], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['message' => 'success', 'data' => ['user' => $this->userService->dto($user)->toArray()]]);
    }

    // UPDATE
    public function update(Request $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validate($this->userService->rules($user)));

        return response()->json(['message' => 'success', 'data' => ['user' => $user->toArray()]]);
    }

    // DELETE
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return response()->json(['message' => 'success']);
    }
}
