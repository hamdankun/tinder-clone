<?php

namespace App\Http\Controllers\Api\V1\Auth;

/**
 * @OA\Post(
 *     path="/v1/auth/register",
 *     operationId="registerUser",
 *     tags={"Authentication"},
 *     summary="Register a new user",
 *     description="Create a new user account with email and password",
 *     @OA\RequestBody(
 *         required=true,
 *         description="User registration data",
 *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User successfully registered",
 *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
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
 *                     "email": {"Email already exists"},
 *                     "password": {"Password must be at least 8 characters"}
 *                 })
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v1/auth/login",
 *     operationId="loginUser",
 *     tags={"Authentication"},
 *     summary="Login user",
 *     description="Authenticate user and return access token",
 *     @OA\RequestBody(
 *         required=true,
 *         description="User credentials",
 *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully authenticated",
 *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=false),
 *                 @OA\Property(property="message", type="string", example="Invalid credentials")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v1/auth/me",
 *     operationId="getCurrentUser",
 *     tags={"Authentication"},
 *     summary="Get current user profile",
 *     description="Retrieve the authenticated user's profile information",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Current user profile retrieved",
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
 * @OA\Post(
 *     path="/v1/auth/logout",
 *     operationId="logoutUser",
 *     tags={"Authentication"},
 *     summary="Logout user",
 *     description="Revoke the user's access token and logout",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successfully logged out",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="success", type="boolean", example=true),
 *                 @OA\Property(property="message", type="string", example="Successfully logged out")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
