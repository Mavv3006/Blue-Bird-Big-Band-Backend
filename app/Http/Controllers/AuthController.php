<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'name' => 'required|string',
                'password' => 'required|string'
            ]);

            $user = User::where('name', $credentials['name'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $token = $user->createToken('api_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {
            $content = [
                'error' => 'Bad Request',
                'message' => $e->getMessage()
            ];
            return response()->json($content, 400);
        }
    }
}
