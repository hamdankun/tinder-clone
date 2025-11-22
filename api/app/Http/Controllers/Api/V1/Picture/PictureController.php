<?php

namespace App\Http\Controllers\Api\V1\Picture;

use App\Http\Controllers\Controller;
use App\Http\Resources\PictureResource;
use App\Services\PictureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Picture Controller
 * 
 * @OA\Tag(
 *     name="Pictures",
 *     description="Manage user profile pictures"
 * )
 */
class PictureController extends Controller
{
    public function __construct(
        private PictureService $pictureService
    ) {}

    /**
     * Get all pictures for the authenticated user
     *
     * @OA\Get(
     *     path="/v1/pictures",
     *     operationId="getUserPictures",
     *     tags={"Pictures"},
     *     summary="Get user's pictures",
     *     description="Retrieve all pictures uploaded by the authenticated user, ordered by display order",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Pictures retrieved successfully"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     items={"$ref": "#/components/schemas/Picture"}
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $pictures = $this->pictureService->getUserPictures($userId);

        return response()->json([
            'success' => true,
            'message' => 'Pictures retrieved successfully',
            'data' => PictureResource::collection($pictures),
        ]);
    }

    /**
     * Upload a new picture
     *
     * @OA\Post(
     *     path="/v1/pictures",
     *     operationId="uploadPicture",
     *     tags={"Pictures"},
     *     summary="Upload a new picture",
     *     description="Upload a new profile picture. Maximum 5 pictures per user, max file size 5MB",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Picture file and options",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"picture"},
     *                 properties={
     *                     @OA\Property(
     *                         property="picture",
     *                         description="Picture file (JPEG, PNG, GIF, WebP)",
     *                         type="string",
     *                         format="binary"
     *                     ),
     *                     @OA\Property(
     *                         property="is_primary",
     *                         type="boolean",
     *                         example=false,
     *                         description="Set as primary picture (used in profile)"
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Picture uploaded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Picture uploaded successfully"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Picture")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid file or validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string", example="File size must not exceed 5MB")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        // Validate request with comprehensive file validation
        $validated = $request->validate([
            'picture' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,gif,webp',
                'max:5120', // 5MB in KB
                'dimensions:min_width=100,min_height=100,ratio=0.5/2', // Min size & aspect ratio
            ],
            'is_primary' => 'nullable|in:true,false',
        ], [
            'picture.required' => 'Please select a picture to upload',
            'picture.file' => 'The picture must be a valid file',
            'picture.image' => 'The picture must be an image file',
            'picture.mimes' => 'The picture must be a JPEG, PNG, GIF, or WebP image',
            'picture.max' => 'The picture size must not exceed 5MB',
            'picture.dimensions' => 'The picture must be at least 100x100 pixels with proper aspect ratio',
        ]);

        try {
            $picture = $this->pictureService->uploadPicture(
                userId: $request->user()->id,
                file: $request->file('picture'),
                isPrimary: $validated['is_primary'] === "true"
            );

            return response()->json([
                'success' => true,
                'message' => 'Picture uploaded successfully',
                'data' => new PictureResource($picture),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete a picture
     *
     * @OA\Delete(
     *     path="/v1/pictures/{pictureId}",
     *     operationId="deletePicture",
     *     tags={"Pictures"},
     *     summary="Delete a picture",
     *     description="Delete a picture belonging to the authenticated user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="pictureId",
     *         in="path",
     *         required=true,
     *         description="Picture ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Picture deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Picture deleted successfully")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Picture not found or unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string", example="Picture not found")
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(Request $request, int $pictureId): JsonResponse
    {
        try {
            $this->pictureService->deletePicture($pictureId, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Picture deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Set a picture as primary
     *
     * @OA\Patch(
     *     path="/v1/pictures/{pictureId}/primary",
     *     operationId="setPrimaryPicture",
     *     tags={"Pictures"},
     *     summary="Set picture as primary",
     *     description="Set a picture as the primary profile picture. Previous primary picture will be unset",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="pictureId",
     *         in="path",
     *         required=true,
     *         description="Picture ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Picture set as primary",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Picture set as primary"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Picture")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Picture not found or unauthorized"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function setPrimary(Request $request, int $pictureId): JsonResponse
    {
        try {
            $picture = $this->pictureService->setPrimaryPicture($pictureId, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Picture set as primary',
                'data' => new PictureResource($picture),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Reorder pictures
     *
     * @OA\Post(
     *     path="/v1/pictures/reorder",
     *     operationId="reorderPictures",
     *     tags={"Pictures"},
     *     summary="Reorder user's pictures",
     *     description="Update the display order of user's pictures",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Picture IDs in new order",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"picture_ids"},
     *             properties={
     *                 @OA\Property(
     *                     property="picture_ids",
     *                     type="array",
     *                     items={"type": "integer", "format": "int64"},
     *                     example={1, 2, 3}
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pictures reordered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Pictures reordered successfully")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid picture IDs or validation error"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'picture_ids' => 'required|array|min:1',
            'picture_ids.*' => 'required|integer',
        ]);

        try {
            $this->pictureService->reorderPictures($request->user()->id, $validated['picture_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Pictures reordered successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
