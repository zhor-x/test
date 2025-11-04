<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\LoginRequst;
use App\Http\Requests\Api\V1\User\RegistrationRequst;
use App\Mail\OtpMail;
use App\Models\User;
use App\Repositories\Api\V1\UserRepository;
use App\Traits\AuthTrait;
use Carbon\Carbon;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        $response = $this->generateTokenResponse($user, false);

        return response()->json($response);
    }

    private function generateAndSendOtp(User $user)
    {
        $otp = rand(100000, 999999); // 6-digit OTP

        $user->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);


        // Send OTP via email
        Mail::to($user->email)->queue(new OtpMail($otp));

        return $otp;
    }

    public function login(LoginRequst $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Սխալ էլ․ փոստ կամ գաղտնաբառ'], 401);
        }

        $response = $this->generateTokenResponse($user, false);

        return response()->json($response);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|digits:6',
            'remember' => 'boolean|nullable',
        ]);

        $user = User::find($request->user_id);

        if (!$user->otp ||
            !Hash::check($request->otp, $user->otp) ||
            Carbon::now()->gt($user->otp_expires_at)
        ) {
            return response()->json(['message' => 'Սխալ կամ ժամկետն անց 6 թվանիշ կոդը'], 401);
        }

        $remember = $request->boolean('remember', false);
        $response = $this->generateTokenResponse($user, $remember);

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

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
            return response()->json(['message' => 'Սխալ կամ ժամկետն անց հղում'], 401);
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
        if ($user) {
             $token = $user->token();
             $token->revoke();
        }

        $user->update([
            'refresh_token' => null,
            'refresh_token_expires_at' => null,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'Logged out']);
    }

    public function socialLogin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => 'required|string',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'google_id' => 'required|string',
        ]);

        $user = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'google_id' => $data['google_id'],
                'password' => bcrypt(Str::random(16)),
            ]
        );

         $response = $this->generateTokenResponse($user, true);

        return response()->json($response);
    }
}
