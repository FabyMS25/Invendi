<?php

/**
 * @OA\Info(
 *     title="Your API",
 *     version="1.0.0"
 * )
 *
 * @OA\PathItem(
 *     path="/usuarios"
 * )
 *
 * @OA\Components(
 *     @OA\Schema(
 *         schema="User",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="email", type="string")
 *     )
 * )
 */
