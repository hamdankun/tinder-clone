<?php

namespace App\Http\Controllers\Api\V1\Discovery;

/**
 * @OA\Get(
 *     path="/v1/people",
 *     operationId="getPeoplelist",
 *     tags={"Discovery"},
 *     summary="Get recommended people list",
 *     description="Retrieve a paginated list of recommended user profiles for discovery. Returns users excluding the current user and those already liked/disliked.",
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
 *     @OA\Parameter(
 *         name="age_min",
 *         in="query",
 *         description="Minimum age filter (optional)",
 *         required=false,
 *         @OA\Schema(type="integer", minimum=18, example=20)
 *     ),
 *     @OA\Parameter(
 *         name="age_max",
 *         in="query",
 *         description="Maximum age filter (optional)",
 *         required=false,
 *         @OA\Schema(type="integer", maximum=100, example=40)
 *     ),
 *     @OA\Parameter(
 *         name="location",
 *         in="query",
 *         description="Location filter (optional, partial match)",
 *         required=false,
 *         @OA\Schema(type="string", example="San Francisco")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Recommended people list retrieved successfully",
 *         @OA\JsonContent(ref="#/components/schemas/PeopleListResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid pagination parameters"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v1/people/{userId}",
 *     operationId="getPerson",
 *     tags={"Discovery"},
 *     summary="Get single person profile",
 *     description="Retrieve detailed information about a specific user profile",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         description="ID of the user to retrieve",
 *         required=true,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User profile retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="data", ref="#/components/schemas/User")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="User not found")
 *             }
 *         )
 *     )
 * )
 */
