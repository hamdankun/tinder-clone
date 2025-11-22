<?php

namespace App\Http\Controllers\Api\V1\Like;

use App\Http\Controllers\Controller;
use App\Http\Requests\LikeRequest;
use App\Services\LikeService;
use App\Http\Resources\LikeResource;
use App\Exceptions\UserAlreadyLikedException;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function __construct(
        private LikeService $likeService,
    ) {}

    /**
     * Get list of liked people
     *
     * @OA\Get(
     *     path="/v1/likes",
     *     operationId="getLikedPeople",
     *     tags={"Interactions"},
     *     summary="Get list of liked people",
     *     description="Retrieve a paginated list of people the current user has liked",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page (max 50)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, minimum=1, maximum=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved liked people list",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     items={
     *                         "type": "object",
     *                         "properties": {
     *                             "id": {"type": "integer"},
     *                             "to_user": {"$ref": "#/components/schemas/User"},
     *                             "is_matched": {"type": "boolean"},
     *                             "liked_at": {"type": "string", "format": "date-time"}
     *                         }
     *                     }
     *                 ),
     *                 @OA\Property(property="pagination", type="object")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400, description="Failed to fetch liked people")
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $page = request('page', 1);
            $perPage = min(request('per_page', 10), 50);

            $likes = $this->likeService->getLikedPeople(
                auth()->id(),
                $page,
                $perPage
            );

            return response()->json([
                'success' => true,
                'data' => LikeResource::collection($likes['data'] ?? []),
                'pagination' => [
                    'total' => $likes['total'] ?? 0,
                    'count' => count($likes['data'] ?? []),
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $likes['last_page'] ?? 1,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch liked people',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Like a person
     *
     * @OA\Post(
     *     path="/v1/likes/{userId}",
     *     operationId="likeUser",
     *     tags={"Interactions"},
     *     summary="Like a person",
     *     description="Create a like interaction with another user. Returns whether a mutual match occurred.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user to like",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully liked user",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User liked successfully"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     properties={
     *                         @OA\Property(property="matched", type="boolean", description="Whether a mutual like occurred")
     *                     }
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request (e.g., cannot like yourself)",
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
     *         description="Conflict: Already liked this user",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string", example="User already liked")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @param int $userId
     * @param LikeRequest $request
     * @return JsonResponse
     */
    public function store(int $userId, LikeRequest $request): JsonResponse
    {
        try {
            if ($userId === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot like yourself',
                ], 400);
            }

            info("User " . auth()->id() . " is liking user " . $userId);

            $result = $this->likeService->likeUser(auth()->id(), $userId);

            return response()->json([
                'success' => true,
                'message' => 'User liked successfully',
                'data' => [
                    'matched' => $result['matched'] ?? false,
                ],
            ], 201);
        } catch (UserAlreadyLikedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Unlike a person
     *
     * @OA\Delete(
     *     path="/v1/likes/{userId}",
     *     operationId="unlikeUser",
     *     tags={"Interactions"},
     *     summary="Unlike a person",
     *     description="Remove a like interaction with another user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user to unlike",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully unliked user",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User unliked successfully")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400, description="Failed to unlike user")
     * )
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function destroy(int $userId): JsonResponse
    {
        try {
            $this->likeService->unlike(auth()->id(), $userId);

            return response()->json([
                'success' => true,
                'message' => 'User unliked successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlike user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
