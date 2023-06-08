<?php

use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuSubscriptionsRequestUtil;
use Rzerostern\Payu\Util\PayuSubscriptionsUrlResolver;

/**
 * Manages all Payu Subscription plans operations
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 22/12/2013
 *
 */
class PayuSubscriptionPlans
{

    /**
     * Creates a subscription plans
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function create($parameters, $lang = null)
    {
        PayuSubscriptionsRequestUtil::validateSubscriptionPlan($parameters);

        $request = PayuSubscriptionsRequestUtil::buildSubscriptionPlan($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::PLAN_ENTITY, PayuSubscriptionsUrlResolver::ADD_OPERATION);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Find a subscription plan by plan code
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {
        $required = array(PayuParameters::PLAN_CODE);
        CommonRequestUtil::validateParameters($parameters, $required);
        $plan = PayuSubscriptionsRequestUtil::buildSubscriptionPlan($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::PLAN_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($plan->planCode)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($plan, $payUHttpRequestInfo);
    }


    /**
     * Updates a subscription plan
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function update($parameters, $lang = null)
    {
        $required = array(PayuParameters::PLAN_CODE);
        $invalid = array(
            PayuParameters::PLAN_INTERVAL_COUNT,
            PayuParameters::ACCOUNT_ID, PayuParameters::PLAN_MAX_PAYMENTS,
            PayuParameters::PLAN_INTERVAL
        );

        CommonRequestUtil::validateParameters($parameters, $required, $invalid);

        $plan = PayuSubscriptionsRequestUtil::buildSubscriptionPlan($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::PLAN_ENTITY,
            PayuSubscriptionsUrlResolver::EDIT_OPERATION,
            array($plan->planCode)
        );
        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::PUT);
        return PayuApiServiceUtil::sendRequest($plan, $payUHttpRequestInfo);
    }

    /**
     * Deletes a subscription plan
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function delete($parameters, $lang = null)
    {
        $required = array(PayuParameters::PLAN_CODE);
        CommonRequestUtil::validateParameters($parameters, $required);

        $plan = PayuSubscriptionsRequestUtil::buildSubscriptionPlan($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::PLAN_ENTITY,
            PayuSubscriptionsUrlResolver::DELETE_OPERATION,
            array($plan->planCode)
        );
        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::DELETE);
        return PayuApiServiceUtil::sendRequest($plan, $payUHttpRequestInfo);
    }



    /**
     * Finds all subscription plans filtered by merchant or account
     * by default the function filter by merchant if you need filter by account
     * you can send in the parameters the account id
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class 
     * @return the subscription plan list
     * @throws PayuException
     * @throws InvalidParametersException
     * @throws InvalidArgumentException
     */
    public static function listPlans($parameters, $lang = null)
    {

        $request = new stdClass();
        $request->accountId = CommonRequestUtil::getParameter($parameters, PayuParameters::ACCOUNT_ID);
        $request->limit = CommonRequestUtil::getParameter($parameters, PayuParameters::LIMIT);
        $request->offset = CommonRequestUtil::getParameter($parameters, PayuParameters::OFFSET);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::PLAN_ENTITY,
            PayuSubscriptionsUrlResolver::QUERY_OPERATION
        );

        $urlSegment = CommonRequestUtil::addQueryParamsToUrl($urlSegment, $request);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(null, $payUHttpRequestInfo);
    }
}
