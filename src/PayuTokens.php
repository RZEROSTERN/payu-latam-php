<?php

namespace Rzerostern\Payu;

use Rzerostern\Payu\Api\Environment;
use Rzerostern\Payu\Api\PayuHttpRequestInfo;
use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuTokensRequestUtil;

/**
 * Manages all Payu tokens operations
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 31/10/2013
 *
 */
class PayuTokens
{

    /**
     * Creates a credit card token
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function create($parameters, $lang = null)
    {

        $required = array(
            PayuParameters::CREDIT_CARD_NUMBER,
            PayuParameters::PAYER_NAME,
            PayuParameters::PAYMENT_METHOD,
            PayuParameters::PAYER_ID,
            PayuParameters::CREDIT_CARD_EXPIRATION_DATE
        );

        CommonRequestUtil::validateParameters($parameters, $required);

        $request = PayuTokensRequestUtil::buildCreateTokenRequest($parameters, $lang);
        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }


    /**
     * Finds a credit card token
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $tokenId = CommonRequestUtil::getParameter($parameters, PayuParameters::TOKEN_ID);
        $required = null;

        if ($tokenId == null) {
            $required = array(PayuParameters::START_DATE, PayuParameters::END_DATE);
        } else {
            $required = array(PayuParameters::TOKEN_ID);
        }

        CommonRequestUtil::validateParameters($parameters, $required);

        $request = PayuTokensRequestUtil::buildGetCreditCardTokensRequest($parameters, $lang);
        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Removes a credit card token
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function remove($parameters, $lang = null)
    {

        $required = array(
            PayuParameters::TOKEN_ID,
            PayuParameters::PAYER_ID
        );

        CommonRequestUtil::validateParameters($parameters, $required);

        $request = PayuTokensRequestUtil::buildRemoveTokenRequest($parameters, $lang);

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }
}
