<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    /**
     * Get authenticated user profile
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        try {
            $user = auth()->user();

            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'age' => 'sometimes|integer|min:18|max:120',
                'location' => 'sometimes|string|max:255',
                'bio' => 'sometimes|nullable|string|max:500',
            ]);

            $user = $this->userService->updateUserProfile(auth()->id(), $validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
