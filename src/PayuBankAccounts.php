<?php

namespace Rzerostern\Payu;

use InvalidArgumentException;
use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuSubscriptionsRequestUtil;
use Rzerostern\Payu\Util\PayuSubscriptionsUrlResolver;
use Rzerostern\Payu\Util\RequestPaymentsUtil;
use stdClass;

/**
 * Manages all Payu Bank Accounts operations over payment plans
 *
 * @author Payu Latam
 * @version 1.0.0, 16/09/2014
 *
 */
class PayuBankAccounts
{


    /**
     * Creates a bank account to payments
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function create($parameters, $lang = null)
    {

        $customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);
        if (!isset($customerId)) {
            throw new InvalidArgumentException(" The parameter customer id is mandatory ");
        }

        $request = RequestPaymentsUtil::buildBankAccountRequest($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(PayuSubscriptionsUrlResolver::BANK_ACCOUNT_ENTITY, PayuSubscriptionsUrlResolver::ADD_OPERATION, array(
            $parameters[PayuParameters::CUSTOMER_ID]
        ));

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Deletes a bank account
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function delete($parameters, $lang = null)
    {

        $required = array(PayuParameters::CUSTOMER_ID, PayuParameters::BANK_ACCOUNT_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);
        $bankAccountId = CommonRequestUtil::getParameter($parameters, PayuParameters::BANK_ACCOUNT_ID);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::BANK_ACCOUNT_ENTITY,
            PayuSubscriptionsUrlResolver::DELETE_OPERATION,
            array($customerId, $bankAccountId)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::DELETE);

        return PayuApiServiceUtil::sendRequest(null, $payUHttpRequestInfo);
    }

    /**
     * Updates a bank account
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function update($parameters, $lang = null)
    {

        $required = array(PayuParameters::BANK_ACCOUNT_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $request = RequestPaymentsUtil::buildBankAccountRequest($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::BANK_ACCOUNT_ENTITY,
            PayuSubscriptionsUrlResolver::EDIT_OPERATION,
            array($request->id)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::PUT);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Return a bank account with the given id
     *
     * @param parameters The parameters to be sent to the server
     * @return the find bank account
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $required = array(PayuParameters::BANK_ACCOUNT_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $bankAccountRequest = RequestPaymentsUtil::buildBankAccountRequest($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::BANK_ACCOUNT_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($bankAccountRequest->id)
        );
        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($bankAccountRequest, $payUHttpRequestInfo);
    }

    /**
     * Finds the bank accounts associated to a customer by customer id
     * 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * 
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function findListByCustomer($parameters, $lang = null)
    {
        $request = new stdClass();
        $request->customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::BANK_ACCOUNT_ENTITY,
            PayuSubscriptionsUrlResolver::GET_LIST_OPERATION,
            array($request->customerId)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Returns all parameter names of Bank Account
     * @return list of parameter names
     */
    public static function getParameterNames()
    {

        $parameterNames = array(
            PayuParameters::BANK_ACCOUNT_ID,
            PayuParameters::BANK_ACCOUNT_DOCUMENT_NUMBER,
            PayuParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE,
            PayuParameters::BANK_ACCOUNT_CUSTOMER_NAME,
            PayuParameters::BANK_ACCOUNT_AGENCY_NUMBER,
            PayuParameters::BANK_ACCOUNT_AGENCY_DIGIT,
            PayuParameters::BANK_ACCOUNT_ACCOUNT_DIGIT,
            PayuParameters::BANK_ACCOUNT_NUMBER,
            PayuParameters::BANK_ACCOUNT_BANK_NAME,
            PayuParameters::BANK_ACCOUNT_TYPE,
            PayuParameters::BANK_ACCOUNT_STATE
        );
        return $parameterNames;
    }

    /**
     * Indicates whether any of the parameters for Bank Account is within the parameters list
     * @param parameters The parametrs to evaluate
     * @return <boolean> returns true if the parameter is in the set
     */
    public static function existParametersBankAccount($parameters)
    {
        $keyNamesSet = self::getParameterNames();
        return CommonRequestUtil::isParameterInSet($parameters, $keyNamesSet);
    }
}
