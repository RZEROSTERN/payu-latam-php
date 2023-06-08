<?php

namespace Rzerostern\Payu;

use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuSubscriptionsRequestUtil;
use Rzerostern\Payu\Util\PayuSubscriptionsUrlResolver;
use Rzerostern\Payu\Util\RequestPaymentsUtil;
use stdClass;

/**
 * Manages all Payu customers  operations
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 22/12/2013
 *
 */
class PayuCustomers
{

    /**
     * Creates a customer 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function create($parameters, $lang = null)
    {

        PayuSubscriptionsRequestUtil::validateCustomer($parameters);

        $request = PayuSubscriptionsRequestUtil::buildCustomer($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY, PayuSubscriptionsUrlResolver::ADD_OPERATION);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Creates a customer with bank account information
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function createCustomerWithBankAccount($parameters, $lang = null)
    {

        PayuSubscriptionsRequestUtil::validateCustomer($parameters);

        $customer = PayuSubscriptionsRequestUtil::buildCustomer($parameters);
        $bankAccount = RequestPaymentsUtil::buildBankAccountRequest($parameters);

        $customer->bankAccounts = array($bankAccount);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY, PayuSubscriptionsUrlResolver::ADD_OPERATION);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($customer, $payUHttpRequestInfo);
    }


    /**
     * Creates a customer with credit card information
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function createCustomerWithCreditCard($parameters, $lang = null)
    {

        PayuSubscriptionsRequestUtil::validateCustomer($parameters);
        PayuSubscriptionsRequestUtil::validateCreditCard($parameters);

        $customer = PayuSubscriptionsRequestUtil::buildCustomer($parameters);
        $creditCard = PayuSubscriptionsRequestUtil::buildCreditCard($parameters);

        $creditCards =  array($creditCard);
        $customer->creditCards = $creditCards;


        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY, PayuSubscriptionsUrlResolver::ADD_OPERATION);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($customer, $payUHttpRequestInfo);
    }

    /**
     * Finds a customer by id
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $required = array(PayuParameters::CUSTOMER_ID);
        CommonRequestUtil::validateParameters($parameters, $required);
        $customer = PayuSubscriptionsRequestUtil::buildCustomer($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($customer->id)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($customer, $payUHttpRequestInfo);
    }


    /**
     * Updates a customer
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function update($parameters, $lang = null)
    {
        $required = array(PayuParameters::CUSTOMER_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        PayuSubscriptionsRequestUtil::validateCustomer($parameters);
        $customer = PayuSubscriptionsRequestUtil::buildCustomer($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY,
            PayuSubscriptionsUrlResolver::EDIT_OPERATION,
            array($customer->id)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::PUT);

        return PayuApiServiceUtil::sendRequest($customer, $payUHttpRequestInfo);
    }

    /**
     * Deletes a customer
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function delete($parameters, $lang = null)
    {
        $required = array(PayuParameters::CUSTOMER_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $customer = PayuSubscriptionsRequestUtil::buildCustomer($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY,
            PayuSubscriptionsUrlResolver::DELETE_OPERATION,
            array($customer->id)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::DELETE);

        return PayuApiServiceUtil::sendRequest($customer, $payUHttpRequestInfo);
    }

    /**
     * Finds the customers associated to a plan by plan id or by plan code
     * 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * 
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function findCustomerListByPlanIdOrPlanCode($parameters, $lang = null)
    {
        $request = new stdClass();
        $request->planId = CommonRequestUtil::getParameter($parameters, PayuParameters::PLAN_ID);
        $request->planCode = CommonRequestUtil::getParameter($parameters, PayuParameters::PLAN_CODE);
        $request->limit = CommonRequestUtil::getParameter($parameters, PayuParameters::LIMIT);
        $request->offset = CommonRequestUtil::getParameter($parameters, PayuParameters::OFFSET);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::CUSTOMER_ENTITY,
            PayuSubscriptionsUrlResolver::CUSTOMERS_PARAM_SEARCH,
            null
        );

        $urlSegment = CommonRequestUtil::addQueryParamsToUrl($urlSegment, $request);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(null, $payUHttpRequestInfo);
    }
}
