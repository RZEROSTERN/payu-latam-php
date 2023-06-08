<?php

namespace Rzerostern\Payu;

use InvalidArgumentException;
use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuSubscriptionsRequestUtil;
use Rzerostern\Payu\Util\PayuSubscriptionsUrlResolver;
use stdClass;

/**
 * Manages all Payu credit card operations
 * over subscriptions
 *
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 22/12/2013
 *
 */
class PayuCreditCards
{

    /**
     * Creates a credit card 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function create($parameters, $lang = null)
    {

        PayuSubscriptionsRequestUtil::validateCreditCard($parameters);

        $customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);
        if (!isset($customerId)) {
            throw new InvalidArgumentException(" The parameter customer id is mandatory ");
        }


        $request = PayuSubscriptionsRequestUtil::buildCreditCard($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CREDIT_CARD_ENTITY,
            PayuSubscriptionsUrlResolver::ADD_OPERATION,
            array($parameters[PayuParameters::CUSTOMER_ID])
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * finds a credit card
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $required = array(PayuParameters::TOKEN_ID);
        CommonRequestUtil::validateParameters($parameters, $required);
        $creditCard = PayuSubscriptionsRequestUtil::buildCreditCard($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CREDIT_CARD_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($creditCard->token)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($creditCard, $payUHttpRequestInfo);
    }

    /**
     * Returns the credit card list with the query params
     *
     * @param parameters
     *            The parameters to be sent to the server
     * @return the credit card list
     * @throws PayuException
     * @throws InvalidParametersException
     * @throws ConnectionException
     */
    public static function findList($parameters, $lang = null)
    {

        $required = array(PayuParameters::CUSTOMER_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $request = new stdClass();
        $request->customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);
        $creditCard = PayuSubscriptionsRequestUtil::buildCreditCard($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CREDIT_CARD_ENTITY,
            PayuSubscriptionsUrlResolver::GET_LIST_OPERATION,
            array($creditCard->customerId)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($creditCard, $payUHttpRequestInfo);
    }

    /**
     * Updates a credit card
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function update($parameters, $lang = null)
    {

        $required = array(PayuParameters::TOKEN_ID);
        $invalid = array(
            PayuParameters::CUSTOMER_ID,
            PayuParameters::CREDIT_CARD_NUMBER,
            PayuParameters::PAYMENT_METHOD
        );

        CommonRequestUtil::validateParameters($parameters, $required,  $invalid);
        $creditCard = PayuSubscriptionsRequestUtil::buildCreditCard($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CREDIT_CARD_ENTITY,
            PayuSubscriptionsUrlResolver::EDIT_OPERATION,
            array($creditCard->token)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::PUT);
        return PayuApiServiceUtil::sendRequest($creditCard, $payUHttpRequestInfo);
    }

    /**
     * Deletes a credit card
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function delete($parameters, $lang = null)
    {

        $required = array(PayuParameters::TOKEN_ID, PayuParameters::CUSTOMER_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $creditCard = PayuSubscriptionsRequestUtil::buildCreditCard($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CREDIT_CARD_ENTITY,
            PayuSubscriptionsUrlResolver::DELETE_OPERATION,
            array($creditCard->customerId, $creditCard->token)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::DELETE);

        return PayuApiServiceUtil::sendRequest($creditCard, $payUHttpRequestInfo);
    }

    /**
     * Returns all parameter names of Credit Card
     * @return list of parameter names
     */
    public static function getParameterNames()
    {

        $parameterNames = array(
            PayuParameters::TOKEN_ID,
            PayuParameters::CREDIT_CARD_NUMBER,
            PayuParameters::CREDIT_CARD_EXPIRATION_DATE,
            PayuParameters::PAYMENT_METHOD,
            PayuParameters::PAYER_NAME,
            PayuParameters::PAYER_STREET,
            PayuParameters::PAYER_STREET_2,
            PayuParameters::PAYER_STREET_3,
            PayuParameters::PAYER_CITY,
            PayuParameters::PAYER_STATE,
            PayuParameters::PAYER_COUNTRY,
            PayuParameters::PAYER_POSTAL_CODE,
            PayuParameters::PAYER_PHONE
        );
        return $parameterNames;
    }

    /**
     * Indicates whether any of the parameters of CRedit Card is within the parameters list
     * @param parameters The parametrs to evaluate
     * @return <boolean> returns true if the parameter is in the set
     */
    public static function existParametersCreditCard($parameters)
    {
        $keyNamesSet = self::getParameterNames();
        return CommonRequestUtil::isParameterInSet($parameters, $keyNamesSet);
    }
}
