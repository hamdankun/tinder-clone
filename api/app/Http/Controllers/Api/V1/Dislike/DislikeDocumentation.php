<?php

namespace App\Http\Controllers\Api\V1\Dislike;

/**
 * @OA\Post(
 *     path="/v1/dislikes/{userId}",
 *     operationId="dislikeUser",
 *     tags={"Interactions"},
 *     summary="Dislike/pass on a user",
 *     description="Record a dislike or pass interaction with another user. This user will not appear in future discovery results.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         description="ID of the user to dislike/pass",
 *         required=true,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         description="Optional dislike metadata",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="reason", type="string", nullable=true, example="not interested", description="Optional reason for dislike (not stored)")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Successfully disliked/passed on user",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Successfully disliked user")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user or cannot dislike yourself",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Cannot dislike yourself")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="User already disliked",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="User already disliked")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
