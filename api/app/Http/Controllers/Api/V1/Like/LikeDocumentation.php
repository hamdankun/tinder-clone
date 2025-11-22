<?php

namespace App\Http\Controllers\Api\V1\Like;

/**
 * @OA\Get(
 *     path="/v1/likes",
 *     operationId="getLikedPeople",
 *     tags={"Interactions"},
 *     summary="Get list of liked people",
 *     description="Retrieve a paginated list of profiles the current user has liked",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination (starts at 1)",
 *         required=false,
 *         @OA\Schema(type="integer", default=1, example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of items per page (max 50)",
 *         required=false,
 *         @OA\Schema(type="integer", default=10, example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liked people list retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="data", type="array", items={"$ref": "#/components/schemas/User"}),
 *                 @OA\Property(property="pagination", type="object", properties={
 *                     @OA\Property(property="current_page", type="integer", example=1),
 *                     @OA\Property(property="per_page", type="integer", example=10),
 *                     @OA\Property(property="total", type="integer", example=25),
 *                     @OA\Property(property="last_page", type="integer", example=3)
 *                 })
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v1/likes/{userId}",
 *     operationId="likeUser",
 *     tags={"Interactions"},
 *     summary="Like a user",
 *     description="Record a like interaction with another user. Returns match status if mutual like.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         description="ID of the user to like",
 *         required=true,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         description="Optional like metadata",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="super_like", type="boolean", nullable=true, example=false, description="Whether this is a super like (Phase 2)")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Successfully liked user",
 *         @OA\JsonContent(ref="#/components/schemas/LikeResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user or cannot like yourself",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Cannot like yourself")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="User already liked or conflict",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="User already liked")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/v1/likes/{userId}",
 *     operationId="unlikeUser",
 *     tags={"Interactions"},
 *     summary="Unlike a user",
 *     description="Remove a like interaction with another user",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         description="ID of the user to unlike",
 *         required=true,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully unliked user",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Successfully unliked user")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Like not found"
 *     )
 * )
 */
