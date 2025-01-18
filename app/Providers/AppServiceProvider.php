<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Response::macro('apiResponse', function ($status, $message, $data = null, $pagination = null, $error = null) {
        //     $response = [
        //         'status' => $status,
        //         'message' => $message,
        //         'data' => $data ?? [],
        //         'pagination' => $pagination ?? new \stdClass(),
        //     ];

        //     if ($error) {
        //         $response['error'] = $error;
        //     }

        //     return response()->json($response, $status);
        // });

        if (!function_exists('apiResponse')) {
            function apiResponse($status, $message, $data = [], $pagination = [], $error = null)
            {
                return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'pagination' => $pagination,
                    'error' => $error,
                ], $status);
            }
        }
    }
}
