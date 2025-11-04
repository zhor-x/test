<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Api\V1\ForgotPasswordDTO;
use App\DTO\Api\V1\ResetPasswordDTO;
use App\Http\Controllers\Controller;
use App\Services\Api\V1\PasswordResetService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class PasswordResetController extends Controller
{
    public function __construct(protected readonly PasswordResetService $passwordResetService)
    {
    }

    public function forgotPassword(ForgotPasswordDTO $request): JsonResponse
    {
        try {
            $this->passwordResetService->initiatePasswordReset($request->email);

            return response()->json([
                'status' => 'success',
                'message' => 'Գաղտնաբառի վերականգնման հղումը ուղարկվել է Ձեր էլ․ փոստին'
            ], 200);
        } catch (Throwable $e) {
            Log::error('Forgot password error: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);


            $message = ($e instanceof InvalidArgumentException)
                ? $e->getMessage()
                : 'Հայցի մշակման ժամանակ սխալ է տեղի ունեցել';

            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordDTO $request): JsonResponse
    {

        try {
            $this->passwordResetService->resetPassword(
                 $request->token,
                $request->password
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Գաղտնաբառը հաջողությամբ վերականգնվել է'
            ], 200);
        } catch (Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage(), [

                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
