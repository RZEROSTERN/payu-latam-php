<?php

namespace Rzerostern\Payu\Util;

use Rzerostern\Payu\Api\PayuCommands;
use Rzerostern\Payu\Api\PayuConfig;
use Rzerostern\Payu\Payu;
use stdClass;

/**
 *
 * Utility class to process parameters and send token requests
 *
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 31/10/2013
 *
 */

class PayuTokensRequestUtil extends CommonRequestUtil
{


    /**
     * Builds a create credit card token request
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return the request built
     * 
     */
    public static function buildCreateTokenRequest($parameters, $lang = null)
    {

        if (!isset($lang)) {
            $lang = Payu::$language;
        }

        $request = CommonRequestUtil::buildCommonRequest(
            $lang,
            PayuCommands::CREATE_TOKEN
        );

        $request->creditCardToken = PayuTokensRequestUtil::buildCreditCardToken($parameters);

        return $request;
    }


    /**
     * Builds a create credit card token request
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return the request built
     */
    public static function buildGetCreditCardTokensRequest($parameters, $lang)
    {

        if (!isset($lang)) {
            $lang = Payu::$language;
        }

        $request = CommonRequestUtil::buildCommonRequest(
            $lang,
            PayuCommands::GET_TOKENS
        );

        $creditCardTokenInformation = new stdClass();
        $creditCardTokenInformation->creditCardTokenId = CommonRequestUtil::getParameter($parameters, PayuParameters::TOKEN_ID);
        $creditCardTokenInformation->payerId = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYER_ID);


        $startDate = CommonRequestUtil::getParameter($parameters, PayuParameters::START_DATE);
        if ($startDate != null && CommonRequestUtil::isValidDate($startDate, PayuConfig::PAYU_DATE_FORMAT, PayuParameters::EXPIRATION_DATE)) {
            $creditCardTokenInformation->startDate = $startDate;
        }

        $endDate = CommonRequestUtil::getParameter($parameters, PayuParameters::END_DATE);
        if ($endDate != null && CommonRequestUtil::isValidDate($endDate, PayuConfig::PAYU_DATE_FORMAT, PayuParameters::EXPIRATION_DATE)) {
            $creditCardTokenInformation->endDate = $endDate;
        }

        $request->creditCardTokenInformation =  $creditCardTokenInformation;

        return $request;
    }


    /**
     * Builds a create credit card token remove request
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return the request built
     */
    public static function buildRemoveTokenRequest($parameters, $lang)
    {

        if (!isset($lang)) {
            $lang = Payu::$language;
        }

        $request = CommonRequestUtil::buildCommonRequest(
            $lang,
            PayuCommands::REMOVE_TOKEN
        );

        $removeCreditCardToken = new stdClass();

        $removeCreditCardToken->creditCardTokenId = CommonRequestUtil::getParameter($parameters, PayuParameters::TOKEN_ID);
        $removeCreditCardToken->payerId = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYER_ID);

        $request->removeCreditCardToken = $removeCreditCardToken;

        return $request;
    }


    /**
     * Builds a credit card token to be added to request
     * @param array $parameters
     * @return the credit card token built
     */
    private static function buildCreditCardToken($parameters)
    {

        $creditCardToken = new stdClass();

        $creditCardToken->name = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYER_NAME);
        $creditCardToken->payerId = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYER_ID);
        $creditCardToken->identificationNumber = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYER_DNI);
        $creditCardToken->paymentMethod = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYMENT_METHOD);
        $creditCardToken->expirationDate = CommonRequestUtil::getParameter($parameters, PayuParameters::CREDIT_CARD_EXPIRATION_DATE);
        $creditCardToken->number = CommonRequestUtil::getParameter($parameters, PayuParameters::CREDIT_CARD_NUMBER);
        $creditCardToken->document = CommonRequestUtil::getParameter($parameters, PayuParameters::CREDIT_CARD_DOCUMENT);

        return $creditCardToken;
    }
}
