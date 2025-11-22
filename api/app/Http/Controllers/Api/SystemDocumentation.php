<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Get(
 *     path="/health",
 *     operationId="getHealth",
 *     tags={"System"},
 *     summary="API health check",
 *     description="Check if the API is running and responsive",
 *     @OA\Response(
 *         response=200,
 *         description="API is healthy",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="status", type="string", example="ok"),
 *                 @OA\Property(property="timestamp", type="string", format="date-time"),
 *                 @OA\Property(property="version", type="string", example="1.0")
 *             }
 *         )
 *     )
 * )
 */
