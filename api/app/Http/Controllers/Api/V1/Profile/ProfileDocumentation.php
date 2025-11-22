<?php

namespace App\Http\Controllers\Api\V1\Profile;

/**
 * @OA\Get(
 *     path="/v1/profile",
 *     operationId="getUserProfile",
 *     tags={"Profile"},
 *     summary="Get user profile",
 *     description="Retrieve the current authenticated user's full profile information",
 *     security={{"bearerAuth": {}}},
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
 *     )
 * )
 *
 * @OA\Put(
 *     path="/v1/profile",
 *     operationId="updateUserProfile",
 *     tags={"Profile"},
 *     summary="Update user profile",
 *     description="Update the current authenticated user's profile information",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Updated profile data",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
 *                 @OA\Property(property="age", type="integer", minimum=18, maximum=100, example=28, description="User's age"),
 *                 @OA\Property(property="location", type="string", example="San Francisco, CA", description="User's location"),
 *                 @OA\Property(property="bio", type="string", nullable=true, example="Love hiking and coffee", description="User's biography/description")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile successfully updated",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Profile successfully updated"),
 *                 @OA\Property(property="data", ref="#/components/schemas/User")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Validation failed"),
 *                 @OA\Property(property="errors", type="object", example={
 *                     "age": {"Age must be between 18 and 100"},
 *                     "location": {"Location is required"}
 *                 })
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
