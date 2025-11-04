<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\GroupQuestionResource;
use App\Http\Resources\Api\V1\GroupResource;
use App\Services\Api\V1\GroupService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    public function __construct(private readonly GroupService $groupService)
    {
    }

    public function index(): JsonResponse
    {
        $groups = $this->groupService->getAllgroups();

        return response()->json([
            'exam_groups' => GroupResource::collection($groups),
            'status' => 'success'
        ]);
    }


    public function questions($lang, int $groupId): JsonResponse
    {
         try {


            $questions = $this->groupService->getQuestionsByGroupId($groupId);

            return response()->json([
                'questions' => GroupQuestionResource::collection($questions),
                'pagination' => [
                    'current_page' => $questions->currentPage(),
                    'per_page' => $questions->perPage(),
                    'total' => $questions->total(),
                    'last_page' => $questions->lastPage(),
                ],
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching questions for group ID ' . $groupId . ': ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
