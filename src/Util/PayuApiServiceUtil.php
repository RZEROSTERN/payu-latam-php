<?php

namespace Rzerostern\Payu\Util;

use Rzerostern\Payu\Api\Environment;
use Rzerostern\Payu\Api\PayuConfig;
use Rzerostern\Payu\Api\PayuHttpRequestInfo;
use Rzerostern\Payu\Api\PayuResponseCode;
use Rzerostern\Payu\Exceptions\PayuErrorCodes;
use Rzerostern\Payu\Exceptions\PayuException;
use Rzerostern\Payu\Payu;

/**
 * 
 * Util class to send request and process the response 
 *
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0 
 *
 */
class PayuApiServiceUtil
{


    /**
     * Sends a request type json 
     * 
     * @param Object $request this object is encode to json is used to request data
     * @param PayuHttpRequestInfo $payUHttpRequestInfo object with info to send an api request
     * @param bool $removeNullValues if remove null values in request and response object 
     * @return string response
     * @throws RuntimeException
     */
    public static function sendRequest($request, PayuHttpRequestInfo $payUHttpRequestInfo, $removeNullValues = NULL)
    {
        if (!isset($removeNullValues)) {
            $removeNullValues = PayuConfig::REMOVE_NULL_OVER_REQUEST;
        }

        if ($removeNullValues && $request != null) {
            $request = PayuRequestObjectUtil::removeNullValues($request);
        }

        if ($request != NULL) {
            $request = PayuRequestObjectUtil::encodeStringUtf8($request);
        }


        if (isset($request->transaction->order->signature)) {
            $request->transaction->order->signature =
                SignatureUtil::buildSignature($request->transaction->order, Payu::$merchantId, Payu::$apiKey, SignatureUtil::MD5_ALGORITHM);
        }

        $requestJson = json_encode($request);

        $responseJson = HttpClientUtil::sendRequest($requestJson, $payUHttpRequestInfo);

        if ($responseJson == 200 || $responseJson == 204) {
            return true;
        } else {
            $response = json_decode($responseJson);
            if (!isset($response)) {
                throw new PayuException(PayuErrorCodes::JSON_DESERIALIZATION_ERROR, sprintf(' Error decoding json. Please verify the json structure received. the json isn\'t added in this message ' .
                    ' for security reasons please verify the variable $responseJson on class PayuApiServiceUtil'));
            }

            if ($removeNullValues) {
                $response = PayuRequestObjectUtil::removeNullValues($response);
            }

            $response = PayuRequestObjectUtil::formatDates($response);

            if ($payUHttpRequestInfo->environment === Environment::PAYMENTS_API || $payUHttpRequestInfo->environment === Environment::REPORTS_API) {
                if (PayuResponseCode::SUCCESS == $response->code) {
                    return $response;
                } else {
                    throw new PayuException(PayuErrorCodes::API_ERROR, $response->error);
                }
            } else if ($payUHttpRequestInfo->environment === Environment::SUBSCRIPTIONS_API) {
                if (!isset($response->type) || ($response->type != 'BAD_REQUEST' && $response->type != 'NOT_FOUND' && $response->type != 'MALFORMED_REQUEST')) {
                    return $response;
                } else {
                    throw new PayuException(PayuErrorCodes::API_ERROR, $response->description);
                }
            }
        }
    }
}
