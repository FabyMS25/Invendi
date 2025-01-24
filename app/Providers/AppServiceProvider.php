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
    /**
     * Standardized API Response Helper
     * @param int    $status
     * @param string $message
     * @param mixed  $data
     * @param array|null $pagination
     * @param string|null $error
     * @return \Illuminate\Http\JsonResponse
     */
    public function boot(): void
    {
        if (!function_exists('apiResponse')) {
            // function apiResponse($status = 200, $data = null, $pagination = null, $message = null,  $error = null)
            function apiResponse($content = [
                'message' => null,
                'data' => null,
                'pagination' => null,
                'error'=>null],$status = 200)
            {
                $response = [
                    'success' => $status >= 200 && $status < 300,
                    'message' => $content['message'] ?? null,
                    'data' => $content['data'] ?? null,
                ];

                if (!empty($content['pagination'])) {
                    $response['pagination'] = AppServiceProvider::getPagination($content['pagination']);
                }

                if (!empty($content['error'])) {
                    $response['error'] = $content['error'];
                }
                return response()->json($response, $status);
            };
        }
        if (!function_exists('pagination')) {
            function pagination($content = null)
            {
                return response()->json(AppServiceProvider::getPagination($content));
            };
        }
    }

    public static function getPagination($data): array
    {
        if (!$data) {
            return [];
        }

        return [
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'last_page' => $data->lastPage(),
            'has_more_pages' => $data->hasMorePages()?:false,
            'from' => $data->firstItem() ?: 0,
            'to' => $data->lastItem() ?: 0,
            'links' => [
                'next' => $data->nextPageUrl()?:null,
                'prev' => $data->previousPageUrl()?:null,
                'first' => $data->url(1)?:null,
                'last' => $data->url($data->lastPage())?:null,
            ]
            // 'path' => $data->path() ?: 0,
            // 'next_page_url' => $data->nextPageUrl(),
            // 'prev_page_url' => $data->previousPageUrl(),
            // 'first_page_url' => $data->url(1),
            // 'last_page_url' => $data->url($data->lastPage()),
            // 'links' => $data->linkCollection()->toArray()
        ];
    }
}
