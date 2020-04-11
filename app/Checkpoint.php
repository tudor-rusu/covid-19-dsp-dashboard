<?php

namespace App;

use App\Traits\ApiTrait;
use Exception;

class Checkpoint
{
    use ApiTrait;

    /**
     * Set a constant with expression in value, in a static content
     *
     * @return string
     */
    public static function API_BORDER_URL() {
        return env('COVID19_DSP_API') . 'border/checkpoint';
    }

    /**
     * Get all Border Checkpoints
     *
     * @param string $url
     * @param array  $params
     *
     * @return array|string
     */
    public static function all(string $url, array $params)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url, $params);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $apiRequest['data'];
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get a specific Border Checkpoint
     *
     * @param string $url
     * @param int    $id
     *
     * @return string
     */
    public static function find(string $url, int $id)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url . DIRECTORY_SEPARATOR . $id);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $apiRequest['data'];
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }
}
