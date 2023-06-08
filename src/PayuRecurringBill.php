<?php

namespace Rzerostern\Payu;

use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\PayuSubscriptionsRequestUtil;
use Rzerostern\Payu\Util\PayuSubscriptionsUrlResolver;
use stdClass;

/**
 * Manages all Payu recurring bill operations
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 25/09/2014
 *
 */
class PayuRecurringBill
{



    /**
     * Finds a recurring bill by id
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function find($parameters, $lang = null)
    {

        $required = array(PayuParameters::RECURRING_BILL_ID);
        CommonRequestUtil::validateParameters($parameters, $required);
        $recurringBillId = CommonRequestUtil::getParameter($parameters, PayuParameters::RECURRING_BILL_ID);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::RECURRING_BILL_ENTITY,
            PayuSubscriptionsUrlResolver::GET_OPERATION,
            array($recurringBillId)
        );

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(NULL, $payUHttpRequestInfo);
    }


    /**
     * Finds all bill filtered by 
     * - customer id
     * - date begin
     * - date final
     * - payment method
     * - subscription Id
     * 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return the subscription plan list
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function listRecurringBills($parameters, $lang = null)
    {

        $request = new stdClass();
        $request->customerId = CommonRequestUtil::getParameter($parameters, PayuParameters::CUSTOMER_ID);
        $request->dateBegin = CommonRequestUtil::getParameter($parameters, PayuParameters::RECURRING_BILL_DATE_BEGIN);
        $request->dateFinal = CommonRequestUtil::getParameter($parameters, PayuParameters::RECURRING_BILL_DATE_FINAL);
        $request->paymentMethod = CommonRequestUtil::getParameter($parameters, PayuParameters::RECURRING_BILL_PAYMENT_METHOD_TYPE);
        $request->state = CommonRequestUtil::getParameter($parameters, PayuParameters::RECURRING_BILL_STATE);
        $request->subscriptionId = CommonRequestUtil::getParameter($parameters, PayuParameters::SUBSCRIPTION_ID);
        $request->limit = CommonRequestUtil::getParameter($parameters, PayuParameters::LIMIT);
        $request->offset = CommonRequestUtil::getParameter($parameters, PayuParameters::OFFSET);

        $urlSegment = PayuSubscriptionsUrlResolver::getInstance()->getUrlSegment(
            PayuSubscriptionsUrlResolver::RECURRING_BILL_ENTITY,
            PayuSubscriptionsUrlResolver::QUERY_OPERATION
        );

        $urlSegment = CommonRequestUtil::addQueryParamsToUrl($urlSegment, $request);

        $payUHttpRequestInfo = PayuSubscriptionsRequestUtil::buildHttpRequestInfo($urlSegment, $lang, RequestMethod::GET);
        return PayuApiServiceUtil::sendRequest(null, $payUHttpRequestInfo);
    }
}
