<?php

use Rzerostern\Payu\Api\Environment;
use Rzerostern\Payu\Api\PayuHttpRequestInfo;
use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Exceptions\PayuErrorCodes;
use Rzerostern\Payu\Exceptions\PayuException;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuReportsRequestUtil;

/**
 * Manages all Payu reports operations
 *
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 17/10/2013
 * 
 */
class PayuReports
{


    /**
     * Makes a ping request
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the ping request sent
     *@throws PayuException
     */
    public static function doPing($lang = null)
    {

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::REPORTS_API, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest(PayuReportsRequestUtil::buildPingRequest(), $payUHttpRequestInfo);
    }


    /**
     * Makes an order details reporting petition by the id
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return order found
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function getOrderDetail($parameters, $lang = null)
    {

        CommonRequestUtil::validateParameters($parameters, array(PayuParameters::ORDER_ID));

        $request = PayuReportsRequestUtil::buildOrderReportingDetails($parameters, $lang);

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::REPORTS_API, RequestMethod::POST);

        $response = PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);

        if (isset($response) && isset($response->result)) {
            return $response->result->payload;
        }

        return null;
    }

    /**
     * Makes an order details reporting petition by reference code
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The order list corresponding whit the given reference code
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function getOrderDetailByReferenceCode($parameters, $lang = null)
    {

        CommonRequestUtil::validateParameters($parameters, array(PayuParameters::REFERENCE_CODE));

        $request = PayuReportsRequestUtil::buildOrderReportingByReferenceCode($parameters, $lang);

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::REPORTS_API, RequestMethod::POST);

        $response = PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);

        if (isset($response) && isset($response->result)) {
            return $response->result->payload;
        } else {
            throw new PayuException(PayuErrorCodes::INVALID_PARAMETERS, "the reference code doesn't exist ");
        }
    }

    /**
     * Makes a transaction reporting petition by the id
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function getTransactionResponse($parameters, $lang = null)
    {

        CommonRequestUtil::validateParameters($parameters, array(PayuParameters::TRANSACTION_ID));

        $request = PayuReportsRequestUtil::buildTransactionResponse($parameters, $lang);

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::REPORTS_API, RequestMethod::POST);

        $response = PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);

        if (isset($response) && isset($response->result)) {
            return $response->result->payload;
        }

        return null;
    }
}
