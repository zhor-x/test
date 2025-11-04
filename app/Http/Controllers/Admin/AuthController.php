<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\LoginRequst;
use App\Http\Requests\Api\V1\User\RegistrationRequst;
use App\Models\User;
use App\Repositories\Api\V1\UserRepository;
use App\Traits\AuthTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use AuthTrait;
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function register(RegistrationRequst $request): JsonResponse
    {
        $payload = $request->validated();
        $user = $this->userRepository->create($payload);

        $response = $this->generateTokenResponse($user);

        return response()->json($response);
    }

    public function login(LoginRequst $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where(['email' => $data['email'], 'role' => UserRole::ADMIN])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Սխալ էլ․ փոստ կամ գաղտնաբառ'], 401);
        }

        $response = $this->generateTokenResponse($user);

        return response()->json($response);
    }


    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $user = User::where('id', $request->user()->id)->first();

        if (!$user ||
            !$user->refresh_token ||
            !Hash::check($request->refresh_token, $user->refresh_token) ||
            Carbon::now()->gt($user->refresh_token_expires_at)
        ) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        $user->currentAccessToken()->delete();
        $remember = $user->refresh_token_expires_at->diffInDays(Carbon::now()) > 7;
        $response = $this->generateTokenResponse($user, $remember);

        return response()->json($response);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $user->update([
            'refresh_token' => null,
            'refresh_token_expires_at' => null,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'Logged out']);
    }
}
