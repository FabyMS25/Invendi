<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Backend API Documentation",
 *     version="1.0.0",
 *     description="API documentation for the system",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * ),
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="API Server"
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
