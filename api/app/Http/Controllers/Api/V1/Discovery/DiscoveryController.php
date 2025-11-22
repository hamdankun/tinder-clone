<?php

namespace App\Http\Controllers\Api\V1\Discovery;

use App\Http\Controllers\Controller;
use App\Services\DiscoveryService;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class DiscoveryController extends Controller
{
    public function __construct(
        private DiscoveryService $discoveryService,
    ) {}

    /**
     * Get recommended people list (paginated)
     *
     * @OA\Get(
     *     path="/v1/people",
     *     operationId="getRecommendedPeople",
     *     tags={"Discovery"},
     *     summary="Get recommended people list",
     *     description="Retrieve a paginated list of recommended user profiles. Excludes current user, already liked users, and disliked users.",
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
     *         description="Successfully retrieved recommended people",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     items={"$ref": "#/components/schemas/User"}
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     properties={
     *                         @OA\Property(property="total", type="integer", example=150),
     *                         @OA\Property(property="count", type="integer", example=10),
     *                         @OA\Property(property="per_page", type="integer", example=10),
     *                         @OA\Property(property="current_page", type="integer", example=1),
     *                         @OA\Property(property="last_page", type="integer", example=15)
     *                     }
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400, description="Failed to fetch people")
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $page = request('page', 1);
            $perPage = min(request('per_page', 10), 50); // Max 50 per page

            $people = $this->discoveryService->getRecommendedPeople(
                auth()->id(),
                $page,
                $perPage
            );

            return response()->json([
                'success' => true,
                'data' => UserResource::collection($people['data']),
                'pagination' => [
                    'total' => $people['total'] ?? 0,
                    'count' => count($people['data']),
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $people['last_page'] ?? 1,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch people',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get a single person by ID
     *
     * @OA\Get(
     *     path="/v1/people/{userId}",
     *     operationId="getSinglePerson",
     *     tags={"Discovery"},
     *     summary="Get a single person profile",
     *     description="Retrieve detailed profile information for a specific user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved user profile",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="data", ref="#/components/schemas/User")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=400, description="Failed to fetch user")
     * )
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        try {
            $user = $this->discoveryService->getUserById($userId);

            if (!$user || $user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
