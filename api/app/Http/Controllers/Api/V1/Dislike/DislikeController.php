<?php

namespace App\Http\Controllers\Api\V1\Dislike;

use App\Http\Controllers\Controller;
use App\Services\DislikeService;
use App\Exceptions\UserAlreadyDislikedException;
use Illuminate\Http\JsonResponse;

class DislikeController extends Controller
{
    public function __construct(
        private DislikeService $dislikeService,
    ) {}

    /**
     * Dislike/pass on a person
     *
     * @OA\Post(
     *     path="/v1/dislikes/{userId}",
     *     operationId="dislikeUser",
     *     tags={"Interactions"},
     *     summary="Dislike or pass on a person",
     *     description="Mark a user as disliked/passed. They will be excluded from future recommendations.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user to dislike",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully disliked user",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User disliked successfully")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request (e.g., cannot dislike yourself)",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict: Already disliked this user",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string", example="User already disliked")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function store(int $userId): JsonResponse
    {
        try {
            if ($userId === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot dislike yourself',
                ], 400);
            }

            $this->dislikeService->dislikeUser(auth()->id(), $userId);

            return response()->json([
                'success' => true,
                'message' => 'User disliked successfully',
            ], 201);
        } catch (UserAlreadyDislikedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to dislike user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
