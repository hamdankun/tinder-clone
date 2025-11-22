<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use App\DTOs\UserDTO;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Tinder Clone API",
 *     version="1.0.0",
 *     description="RESTful API for the Tinder Clone Application - Discover, Like, and Match with other users",
 *     contact={
 *         "name": "API Support",
 *         "email": "support@tinder-clone.local"
 *     },
 *     license={
 *         "name": "MIT License"
 *     }
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     in="header",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Bearer token authentication. Use the token received from login endpoint."
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User registration, login, and authentication management"
 * )
 * @OA\Tag(
 *     name="Discovery",
 *     description="Discover and browse recommended user profiles"
 * )
 * @OA\Tag(
 *     name="Interactions",
 *     description="Like and dislike user profiles"
 * )
 * @OA\Tag(
 *     name="Profile",
 *     description="User profile management and updates"
 * )
 * @OA\Tag(
 *     name="System",
 *     description="System health and status endpoints"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "email", "age", "location"},
 *     properties={
 *         @OA\Property(property="id", type="integer", format="int64", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *         @OA\Property(property="age", type="integer", format="int32", example=28),
 *         @OA\Property(property="location", type="string", example="San Francisco, CA"),
 *         @OA\Property(property="bio", type="string", nullable=true, example="Love hiking and coffee"),
 *         @OA\Property(property="pictures", type="array", items={"$ref": "#/components/schemas/Picture"}),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="Picture",
 *     type="object",
 *     required={"id", "user_id", "url"},
 *     properties={
 *         @OA\Property(property="id", type="integer", format="int64", example=1),
 *         @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *         @OA\Property(property="url", type="string", format="uri", example="https://api.tinder-clone.com/storage/pictures/1.jpg"),
 *         @OA\Property(property="is_primary", type="boolean", example=true),
 *         @OA\Property(property="order", type="integer", format="int32", example=1),
 *         @OA\Property(property="created_at", type="string", format="date-time")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="Like",
 *     type="object",
 *     required={"id", "from_user_id", "to_user_id"},
 *     properties={
 *         @OA\Property(property="id", type="integer", format="int64", example=1),
 *         @OA\Property(property="from_user_id", type="integer", format="int64", example=1),
 *         @OA\Property(property="to_user_id", type="integer", format="int64", example=2),
 *         @OA\Property(property="is_mutual", type="boolean", example=false),
 *         @OA\Property(property="liked_at", type="string", format="date-time")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name", "email", "password", "password_confirmation", "age", "location"},
 *     properties={
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *         @OA\Property(property="password", type="string", format="password", minLength=8, example="SecurePassword123!"),
 *         @OA\Property(property="password_confirmation", type="string", format="password", minLength=8, example="SecurePassword123!"),
 *         @OA\Property(property="age", type="integer", format="int32", minimum=18, maximum=100, example=28),
 *         @OA\Property(property="location", type="string", example="San Francisco, CA"),
 *         @OA\Property(property="bio", type="string", nullable=true, example="Love hiking and coffee")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     properties={
 *         @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *         @OA\Property(property="password", type="string", format="password", example="SecurePassword123!")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     properties={
 *         @OA\Property(property="success", type="boolean", example=true),
 *         @OA\Property(property="message", type="string", example="Successfully registered"),
 *         @OA\Property(property="data", type="object", properties={
 *             @OA\Property(property="user", ref="#/components/schemas/User"),
 *             @OA\Property(property="token", type="string", example="1|abc123def456ghi789jkl012mno345pqr678stu901")
 *         })
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="PeopleListResponse",
 *     type="object",
 *     properties={
 *         @OA\Property(property="success", type="boolean", example=true),
 *         @OA\Property(property="data", type="array", items={"$ref": "#/components/schemas/User"}),
 *         @OA\Property(property="pagination", type="object", properties={
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="per_page", type="integer", example=10),
 *             @OA\Property(property="total", type="integer", example=150),
 *             @OA\Property(property="last_page", type="integer", example=15)
 *         })
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="LikeResponse",
 *     type="object",
 *     properties={
 *         @OA\Property(property="success", type="boolean", example=true),
 *         @OA\Property(property="message", type="string", example="Successfully liked user"),
 *         @OA\Property(property="data", type="object", properties={
 *             @OA\Property(property="liked", type="boolean", example=true),
 *             @OA\Property(property="matched", type="boolean", example=false),
 *             @OA\Property(property="like", ref="#/components/schemas/Like")
 *         })
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     properties={
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="Validation failed"),
 *         @OA\Property(property="errors", type="object", example={"email": {"Email is required"}})
 *     }
 * )
 */

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/v1/auth/register",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Create a new user account with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userDTO = UserDTO::fromArray($request->validated());
            $user = $this->userService->registerUser($userDTO);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/v1/auth/login",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Authenticate user and return access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully authenticated",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->getUserByEmail($request->email);

            if (!$user || !password_verify($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get current authenticated user
     *
     * @return JsonResponse
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
     *         @OA\JsonContent(type="object", properties={
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         })
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource(auth()->user()),
        ], 200);
    }

    /**
     * Logout user
     *
     * @return JsonResponse
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
     *         @OA\JsonContent(type="object", properties={
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         })
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(): JsonResponse
    {
        auth()->user()?->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
