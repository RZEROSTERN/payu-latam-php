<?php

namespace Rzerostern\Payu;

use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Exceptions\PayuErrorCodes;
use Rzerostern\Payu\Exceptions\PayuException;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuSubscriptionsRequestUtil;
use Rzerostern\Payu\Util\PayuSubscriptionsUrlResolver;
use stdClass;

/**
 * Manages all Payu subscriptions operations
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 17/12/2013
 *
 */
class PayuSubscriptions
{

    /**
     * Creates a subscription
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function createSubscription($parameters, $lang = null)
    {

        $planCode = CommonRequestUtil::getParameter($parameters, PayuParameters::PLAN_CODE);
        $tokenId = CommonRequestUtil::getParameter($parameters, PayuParameters::TOKEN_ID);
        if (!isset($planCode)) {
            PayuSubscriptionsRequestUtil::validateSubscriptionPlan($parameters);
        }

        PayuSubscriptionsRequestUtil::validateCustomerToSubscription($parameters);

        $existParamBankAccount = PayuBankAccounts::existParametersBankAccount($parameters);
        $existParamCreditCard = PayuCreditCards::existParametersCreditCard($parameters);

        self::validatePaymentMethod($parameters, $existParamBankAccount, $existParamCreditCard);

        $request = PayuSubscriptionsRequestUtil::buildSubscription($parameters, $existParamBankAccount, $existParamCreditCard);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::SUBSCRIPTIONS_ENTITY, PayuSubscriptionsUrlResolver::ADD_OPERATION);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }


    /**
     * Update a subscription
     * @param parameters The parameters to be sent to the server
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function update($parameters, $lang = null)
    {

        $required = array(PayuParameters::SUBSCRIPTION_ID);
        CommonRequestUtil::validateParameters($parameters, $required);
        $subscriptionId = CommonRequestUtil::getParameter($parameters, PayuParameters::SUBSCRIPTION_ID);

        //validates in edition mode
        PayuSubscriptionsRequestUtil::validateCustomerToSubscription($parameters, TRUE);

        $existParamBankAccount = PayuBankAccounts::existParametersBankAccount($parameters);
        $existParamCreditCard = PayuCreditCards::existParametersCreditCard($parameters);

        //Validate in edition mode
        self::validatePaymentMethod($parameters, $existParamBankAccount, $existParamCreditCard, TRUE);

        //Build request in edition mode
        $request = PayuSubscriptionsRequestUtil::buildSubscription($parameters, $existParamBankAccount, $existParamCreditCard, TRUE);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::SUBSCRIPTIONS_ENTITY,
            PayuSubscriptionsUrlResolver::EDIT_OPERATION,
            array($subscriptionId)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::PUT);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }


    /**
     * Cancels a subscription
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function cancel($parameters, $lang = null)
    {
        $required = array(PayuParameters::SUBSCRIPTION_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $request = PayuSubscriptionsRequestUtil::buildSubscription($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::SUBSCRIPTIONS_ENTITY,
            PayuSubscriptionsUrlResolver::DELETE_OPERATION,
            array($parameters[PayuParameters::SUBSCRIPTION_ID])
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::DELETE);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }


    /**
     * Find the subscription with the given id
     *
     * @param parameters The parameters to be sent to the server
     * @return the finded Subscription
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $required = array(PayuParameters::SUBSCRIPTION_ID);
        CommonRequestUtil::validateParameters($parameters, $required);
        $subscriptionId = CommonRequestUtil::getParameter($parameters, PayuParameters::SUBSCRIPTION_ID);

        $request = PayuSubscriptionsRequestUtil::buildSubscription($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::SUBSCRIPTIONS_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($subscriptionId)
        );
        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Finds the subscriptions associated to a customer by either
     * payer id, plan id, plan code, accoun id and account status
     * using an offset and a limit 
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     *
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function findSubscriptionsByPlanOrCustomerOrAccount($parameters, $lang = null)
    {
        $request = new stdClass();
        $request->planId = CommonRequestUtil::getParameter($parameters, PayuParameters::PLAN_ID);
        $request->planCode = CommonRequestUtil::getParameter($parameters, PayuParameters::PLAN_CODE);
        $request->state = CommonRequestUtil::getParameter($parameters, PayuParameters::ACCOUNT_STATE);
        $request->customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);
        $request->accountId = CommonRequestUtil::getParameter($parameters, PayuParameters::ACCOUNT_ID);
        $request->limit = CommonRequestUtil::getParameter($parameters, PayuParameters::LIMIT);
        $request->offset = CommonRequestUtil::getParameter($parameters, PayuParameters::OFFSET);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::SUBSCRIPTIONS_ENTITY, UrlResolver::GET_LIST_OPERATION, null);

        $urlSegment = CommonRequestUtil::addQueryParamsToUrl($urlSegment, $request);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(null, $payUHttpRequestInfo);
    }

    /**
     * validate the Payment Method parameters. Only one payment methos is permitted
     * @param $parameters
     * @param $existParamBankAccount
     * @param $existParamCreditCard
     * @throws PayuException
     */
    public static function validatePaymentMethod($parameters, $existParamBankAccount, $existParamCreditCard, $edit = FALSE)
    {
        if ($existParamBankAccount == TRUE && $existParamCreditCard == TRUE) {
            throw new PayuException(PayuErrorCodes::INVALID_PARAMETERS, "The subscription must have only one payment method");
        } else if ($existParamBankAccount == TRUE) {
            PayuSubscriptionsRequestUtil::validateBankAccount($parameters);
            if ($edit == FALSE) {
                //The TERMS_AND_CONDITIONS_ACEPTED Parameter is required for Bank Account
                $required = array(PayuParameters::TERMS_AND_CONDITIONS_ACEPTED);
                CommonRequestUtil::validateParameters($parameters, $required);
            }
        } else if ($existParamCreditCard == TRUE) {
            PayuSubscriptionsRequestUtil::validateCreditCard($parameters);
        } else {
            throw new PayuException(PayuErrorCodes::INVALID_PARAMETERS, "The subscription must have one payment method");
        }
    }
}
