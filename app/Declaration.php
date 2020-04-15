<?php

namespace App;

use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use PeterColes\Countries\CountriesFacade;

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
     * @param string      $url
     * @param array       $params
     * @param string|null $format
     *
     * @return array|string
     */
    public static function all(string $url, array $params, string $format = null)
    {
        return Cache::untilUpdated('declarations', env('CACHE_DECLARATIONS_PERSISTENCE'), function() use ($url, $params, $format) {
            try {
                $apiRequest = self::connectApi()
                    ->get($url, $params);

                if (!$apiRequest->successful()) {
                    throw new Exception(self::returnStatus($apiRequest->status()));
                }

                if ($apiRequest['data']) {
                    if ($format === 'datatables') {
                        return self::dataTablesFormat($apiRequest['data']);
                    }
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
        return Cache::untilUpdated('declaration-' . $code, env('CACHE_DECLARATIONS_PERSISTENCE'), function() use ($url,
            $code) {
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
        });
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

            return $apiRequest->json();

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Format declarations collection for datatables
     *
     * @param array $data
     *
     * @return array
     */
    private static function dataTablesFormat(array $data) : array
    {
        $countries = CountriesFacade::lookup('ro_RO');
        $formattedDeclarations = [];

        foreach ($data as $key => $declaration) {
            $formattedDeclarations[$key]['code'] = $declaration['code'];
            $formattedDeclarations[$key]['name'] = $declaration['name'] . ' ' . $declaration['surname'];
            $formattedDeclarations[$key]['country'] = $countries[$declaration['travelling_from_country_code']];
            $formattedDeclarations[$key]['checkpoint'] = $declaration['border_checkpoint']['name'];
            $formattedDeclarations[$key]['auto'] = $declaration['vehicle_registration_no'];
            $formattedDeclarations[$key]['signed'] = $declaration['signed'];
            $formattedDeclarations[$key]['url'] = '/declaratie/' . $declaration['code'];
            $formattedDeclarations[$key]['phone'] = $declaration['phone'];
            $formattedDeclarations[$key]['travelling_from_date'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
                ->format('d m Y');
            $formattedDeclarations[$key]['travelling_from_city'] = $declaration['travelling_from_city'] . ', ' . $countries[$declaration['travelling_from_country_code']];
            $formattedDeclarations[$key]['itinerary_country_list'] = '';
            if ($declaration['itinerary_country_list'] && count($declaration['itinerary_country_list']) > 0) {
                foreach ($declaration['itinerary_country_list'] as $country) {
                    $formattedDeclarations[$key]['itinerary_country_list'] .= $countries[$country] . ', ';
                }
                $formattedDeclarations[$key]['itinerary_country_list'] = substr(trim($formattedDeclarations[$key]['itinerary_country_list']), 0, -1);
            }
        }

        return $formattedDeclarations;
    }
}
