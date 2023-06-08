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
 * Manages all Payu recurring bill item operations
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 22/12/2013
 *
 */
class PayuRecurringBillItem
{

    /**
     * Creates a recurring bill item 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function create($parameters, $lang = null)
    {

        $required = array(
            PayuParameters::SUBSCRIPTION_ID,
            PayuParameters::DESCRIPTION,
            PayuParameters::ITEM_VALUE,
            PayuParameters::CURRENCY
        );

        CommonRequestUtil::validateParameters($parameters, $required);
        $request = PayuSubscriptionsRequestUtil::buildRecurringBillItem($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::RECURRING_BILL_ITEM_ENTITY,
            PayuSubscriptionsUrlResolver::ADD_OPERATION,
            array($parameters[PayuParameters::SUBSCRIPTION_ID])
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::POST);

        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Finds recurring bill items by id
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $required = array(PayuParameters::RECURRING_BILL_ITEM_ID);
        CommonRequestUtil::validateParameters($parameters, $required);
        $recurringBillItemId = CommonRequestUtil::getParameter($parameters, PayuParameters::RECURRING_BILL_ITEM_ID);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::RECURRING_BILL_ITEM_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($recurringBillItemId)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(NULL, $payUHttpRequestInfo);
    }

    /**
     * Returns the recurring bill items with the query params
     *
     * @param parameters
     *            The parameters to be sent to the server
     * @return the recurring bill items found
     * @throws PayuException
     * @throws InvalidParametersException
     * @throws ConnectionException
     */
    public static function findList($parameters, $lang = null)
    {

        $subscriptionId = CommonRequestUtil::getParameter($parameters, PayuParameters::SUBSCRIPTION_ID);
        $description = CommonRequestUtil::getParameter($parameters, PayuParameters::DESCRIPTION);

        $request = new stdClass();
        $request->subscriptionId = $subscriptionId;
        $request->description = $description;

        if (isset($subscriptionId) || isset($description)) {

            $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
                PayuSubscriptionsUrlResolver::RECURRING_BILL_ITEM_ENTITY,
                PayuSubscriptionsUrlResolver::GET_LIST_OPERATION,
                null
            );

            $urlSegment = CommonRequestUtil::addQueryParamsToUrl($urlSegment, $request);
        } else {
            throw new InvalidArgumentException('You must send ' . PayuParameters::SUBSCRIPTION_ID . ' or ' . PayuParameters::DESCRIPTION . ' parameters');
        }

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(null, $payUHttpRequestInfo);
    }

    /**
     * Updates a recurring bill item
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function update($parameters, $lang = null)
    {
        $required = array(PayuParameters::RECURRING_BILL_ITEM_ID);

        CommonRequestUtil::validateParameters($parameters, $required);

        $recurrinbBillItem = PayuSubscriptionsRequestUtil::buildRecurringBillItem($parameters);
        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::RECURRING_BILL_ITEM_ENTITY,
            PayuSubscriptionsUrlResolver::EDIT_OPERATION,
            array($recurrinbBillItem->id)
        );
        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::PUT);
        return PayuApiServiceUtil::sendRequest($recurrinbBillItem, $payUHttpRequestInfo);
    }

    /**
     * Deletes a recurring bill item
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function delete($parameters, $lang = null)
    {
        $required = array(PayuParameters::RECURRING_BILL_ITEM_ID);
        CommonRequestUtil::validateParameters($parameters, $required);

        $recurrinbBillItem = PayuSubscriptionsRequestUtil::buildRecurringBillItem($parameters);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::RECURRING_BILL_ITEM_ENTITY,
            PayuSubscriptionsUrlResolver::DELETE_OPERATION,
            array($recurrinbBillItem->id)
        );
        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::DELETE);
        return PayuApiServiceUtil::sendRequest(NULL, $payUHttpRequestInfo);
    }
}
