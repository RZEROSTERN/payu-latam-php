<?php

namespace Rzerostern\Payu;

use InvalidArgumentException;
use Rzerostern\Payu\Api\Environment;
use Rzerostern\Payu\Api\PayuHttpRequestInfo;
use Rzerostern\Payu\Api\PayuPaymentMethodType;
use Rzerostern\Payu\Api\RequestMethod;
use Rzerostern\Payu\Api\TransactionType;
use Rzerostern\Payu\Util\CommonRequestUtil;
use Rzerostern\Payu\Util\PayuApiServiceUtil;
use Rzerostern\Payu\Util\PayuParameters;
use Rzerostern\Payu\Util\RequestPaymentsUtil;

/**
 * Manages all Payu payments operations
 *
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0.0, 17/10/2013
 * 
 */
class PayuPayments
{


    /**
     * Makes a ping request
     * @param string $lang language of request see SupportedLanguages class
     * @throws PayuException 
     * @return The response to the ping request sent
     */
    static function doPing($lang = null)
    {
        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest(RequestPaymentsUtil::buildPingRequest($lang), $payUHttpRequestInfo);
    }


    /**
     * Makes a get payment methods request
     * @param string $lang language of request see SupportedLanguages class
     * @return The payment method list
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function getPaymentMethods($lang = null)
    {
        $request = RequestPaymentsUtil::buildPaymentMethodsListRequest($lang);
        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Evaluate if a payment method is available in Payments API
     * @param string $paymentMethodParameter the payment method to evaluate
     * @param string $lang language of request see SupportedLanguages class
     * @return The payment method information 
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function getPaymentMethodAvailability($paymentMethodParameter, $lang = null)
    {
        $request = RequestPaymentsUtil::buildPaymentMethodAvailabilityRequest($paymentMethodParameter, $lang);
        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * list PSE Banks 
     *
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * 
     * @return The bank list information
     * @throws PayuException
     * @throws InvalidArgumentException
     * 
     */
    public static function getPSEBanks($parameters, $lang = null)
    {
        CommonRequestUtil::validateParameters($parameters, array(PayuParameters::COUNTRY));
        $paymentCountry = CommonRequestUtil::getParameter($parameters, PayuParameters::COUNTRY);
        $request = RequestPaymentsUtil::buildBankListRequest($paymentCountry);
        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }


    /**
     * Do an authorization and capture transaction 
     * @param parameters The parameters to be sent to the server
     * @param string $lang language of request see SupportedLanguages class
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     * 
     */
    public static function doAuthorizationAndCapture($parameters, $lang = null)
    {
        return PayuPayments::doPayment($parameters, TransactionType::AUTHORIZATION_AND_CAPTURE, $lang);
    }



    /**
     * Makes payment petition
     *
     * @param parameters The parameters to be sent to the server
     * @param transactionType
     *            The type of the payment transaction
     * @param string $lang language of request see SupportedLanguages class            
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function doPayment($parameters, $transactionType, $lang)
    {

        $requiredAll = array(
            PayuParameters::REFERENCE_CODE,
            PayuParameters::DESCRIPTION,
            PayuParameters::CURRENCY,
            PayuParameters::VALUE,
        );

        $paymentMethodParameter = CommonRequestUtil::getParameter($parameters, PayuParameters::PAYMENT_METHOD);

        if ($paymentMethodParameter != null) {

            $responseAvailability = PayuPayments::getPaymentMethodAvailability($paymentMethodParameter, $lang);
            $paymentMethod = $responseAvailability->paymentMethod;

            if (array_key_exists(PayuParameters::TOKEN_ID, $parameters)) {

                $requiredTokenId = array(
                    PayuParameters::INSTALLMENTS_NUMBER,
                    PayuParameters::TOKEN_ID
                );

                $required = array_merge($requiredAll, $requiredTokenId);
            } else if (array_key_exists(PayuParameters::CREDIT_CARD_NUMBER, $parameters)) {

                $requiredCreditCard = array(
                    PayuParameters::INSTALLMENTS_NUMBER,
                    PayuParameters::CREDIT_CARD_NUMBER,
                    PayuParameters::PAYER_NAME,
                    PayuParameters::CREDIT_CARD_EXPIRATION_DATE,
                    PayuParameters::PAYMENT_METHOD
                );


                $processWithoutCvv2 = PayuPayments::isProcessWithoutCvv2Param($parameters);
                if (!$processWithoutCvv2) {
                    $requiredCreditCard[] = PayuParameters::CREDIT_CARD_SECURITY_CODE;
                }

                $required = array_merge($requiredAll, $requiredCreditCard);
            } else if ($paymentMethod != null && (PayuPaymentMethodType::CASH == $paymentMethod->type)) {
                $requiredCash = array(
                    PayuParameters::PAYER_NAME,
                    PayuParameters::PAYER_DNI,
                    PayuParameters::PAYMENT_METHOD
                );

                $required = array_merge($requiredAll, $requiredCash);
            } else if ("BOLETO_BANCARIO" == $paymentMethodParameter) {
                $requiredBoletoBancario = array(
                    PayuParameters::PAYER_NAME,
                    PayuParameters::PAYER_DNI,
                    PayuParameters::PAYMENT_METHOD,
                    PayuParameters::PAYER_STREET,
                    PayuParameters::PAYER_STREET_2,
                    PayuParameters::PAYER_CITY,
                    PayuParameters::PAYER_STATE,
                    PayuParameters::PAYER_POSTAL_CODE
                );

                $required = array_merge($requiredAll, $requiredBoletoBancario);
            } else if ("PSE" == $paymentMethodParameter) {
                $requiredPSE = array(
                    PayuParameters::REFERENCE_CODE,
                    PayuParameters::DESCRIPTION,
                    PayuParameters::CURRENCY,
                    PayuParameters::VALUE,
                    PayuParameters::PAYMENT_METHOD,
                    PayuParameters::PAYER_NAME,
                    PayuParameters::PAYER_DOCUMENT_TYPE,
                    PayuParameters::PAYER_DNI,
                    PayuParameters::PAYER_EMAIL,
                    PayuParameters::PAYER_CONTACT_PHONE,
                    PayuParameters::PSE_FINANCIAL_INSTITUTION_CODE,
                    PayuParameters::PAYER_PERSON_TYPE,
                    PayuParameters::IP_ADDRESS,
                    PayuParameters::PAYER_COOKIE,
                    PayuParameters::USER_AGENT
                );
                $required = array_merge($requiredAll, $requiredPSE);
            } else if ($paymentMethod != null && ($paymentMethod->type == PayuPaymentMethodType::CREDIT_CARD)) {
                throw new InvalidArgumentException("Payment method credit card require at least one of two parameters ["
                    . PayuParameters::CREDIT_CARD_NUMBER . '] or [' . PayuParameters::TOKEN_ID . ']');
            } else {
                $required = $requiredAll;
            }
        } else {
            throw new InvalidArgumentException(sprintf("The payment method value is invalid"));
        }

        CommonRequestUtil::validateParameters($parameters, $required);
        $request = RequestPaymentsUtil::buildPaymentRequest($parameters, $transactionType, $lang);

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Process a transaction already authorizated
     *
     * @param parameters The parameters to be sent to the server
     * @param transactionType
     *            The type of the payment transaction
     * @param string $lang language of request see SupportedLanguages class            
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    private static function processTransactionAlreadyAuthorizated($parameters, $transactionType, $lang)
    {
        $required = array(
            PayuParameters::TRANSACTION_ID,
            PayuParameters::ORDER_ID
        );

        CommonRequestUtil::validateParameters($parameters, $required);
        $request = RequestPaymentsUtil::buildPaymentRequest($parameters, $transactionType, $lang);

        $payUHttpRequestInfo = new PayuHttpRequestInfo(Environment::PAYMENTS_API, RequestMethod::POST);
        return PayuApiServiceUtil::sendRequest($request, $payUHttpRequestInfo);
    }

    /**
     * Do an authorization transaction
     *
     * @param parameters to build the request
     * @param string $lang language of request see SupportedLanguages class 
     * @return The request response
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function  doAuthorization($parameters, $lang = null)
    {
        return PayuPayments::doPayment($parameters, TransactionType::AUTHORIZATION, $lang);
    }


    /**
     * Do a capture transaction
     *
     * @param parameters to build the request
     * @param string $lang language of request see SupportedLanguages class 
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function doCapture($parameters, $lang = NULL)
    {
        return PayuPayments::processTransactionAlreadyAuthorizated($parameters, TransactionType::CAPTURE, $lang);
    }

    /**
     * Do a void (Cancel) transaction
     *
     * @param parameters to build the request
     * @param string $lang language of request see SupportedLanguages class 
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function doVoid($parameters, $lang = NULL)
    {
        return PayuPayments::processTransactionAlreadyAuthorizated($parameters, TransactionType::VOID, $lang);
    }

    /**
     * Do a refund transaction
     *
     * @param parameters to build the request
     * @param string $lang language of request see SupportedLanguages class
     * @return The transaction response to the request sent
     * @throws PayuException
     * @throws InvalidArgumentException
     */
    public static function doRefund($parameters, $lang = NULL)
    {
        return PayuPayments::processTransactionAlreadyAuthorizated($parameters, TransactionType::REFUND, $lang);
    }

    /**
     * Get the value for parameter processWithoutCvv2 if the parameter doesn't exist
     * in the parameters array or the parameter value isn't valid boolean representation return false
     * the otherwise return the parameter value
     * @param array $parameters
     * @return boolean whith the value for processWithoutCvv2 parameter, if the parameter doesn't exist in the array or 
     * it has a invalid boolean value returs false;
     */
    private static function isProcessWithoutCvv2Param($parameters)
    {
        $processWithoutCvv2 =
            CommonRequestUtil::getParameter($parameters, PayuParameters::PROCESS_WITHOUT_CVV2);

        if (is_bool($processWithoutCvv2)) {
            return $processWithoutCvv2;
        } else {
            return false;
        }
    }
}
