<?php

namespace App;

use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Cache;

class Declaration
{
    use ApiTrait;

    /**
     * Set a constant with expression in value, in a static content
     *
     * @return string
     */
    public static function API_DECLARATION_URL() {
        return env('COVID19_DSP_API') . 'declaration';
    }

    /**
     * Get all Declarations
     *
     * @param string $url
     * @param array  $params
     *
     * @return array|string
     */
    public static function all(string $url, array $params)
    {
        return Cache::untilUpdated('declarations', env('CACHE_DECLARATIONS_PERSISTENCE'), function() use ($url, $params) {
            try {
                $apiRequest = self::connectApi()
                    ->get($url, $params);

                if (!$apiRequest->successful()) {
                    throw new Exception(self::returnStatus($apiRequest->status()));
                }

                if ($apiRequest['data']) {
                    return $apiRequest['data'];
                } else {
                    return $apiRequest['message'];
                }

            } catch(Exception $exception) {
                return $exception->getMessage();
            }
        });
    }

    /**
     * Get a specific Declaration
     *
     * @param string $url
     * @param string $code
     *
     * @return array|string
     */
    public static function find(string $url, string $code)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url . DIRECTORY_SEPARATOR . $code);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $apiRequest['declaration'];
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get a specific signature from Declaration
     *
     * @param string $url
     * @param string $code
     *
     * @return array|string
     */
    public static function getSignature(string $url, string $code)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url . DIRECTORY_SEPARATOR . $code . DIRECTORY_SEPARATOR . 'signature');

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $apiRequest['signature'];
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }
}
