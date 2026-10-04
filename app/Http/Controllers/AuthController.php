<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(Request $request)
    {
        $result = $this->authService->register(
            $request->all());

        return response()->json([
            'message' => 'success',
            'path' => $request->path(),
            'dateTime' => now(),
            'data' => $result,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $result = $this->authService->login($request->all());

        return response()->json([
            'dateTime' => now(),
            'path' => $request->path(),
            'message' => 'success',
            'data' => $result,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'dateTime' => now(),
            'message' => 'success',
        ], Response::HTTP_OK);
    }
}
