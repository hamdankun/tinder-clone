<?php

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
 */

/**
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
