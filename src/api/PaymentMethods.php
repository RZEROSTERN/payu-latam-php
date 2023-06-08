<?php

namespace Rzerostern\Payu\Api;

class PaymentMethods
{
    const VISA = 'VISA';
    const AMEX = 'AMEX';
    const DINERS = 'DINERS';
    const MASTERCARD = 'MASTERCARD';
    const DISCOVER = 'DISCOVER';
    const ELO = 'ELO';
    const PSE = 'PSE';
    const BALOTO = 'BALOTO';
    const EFECTY = 'EFECTY';
    const BCP = 'BCP';
    const SEVEN_ELEVEN = 'SEVEN_ELEVEN';
    const OXXO = 'OXXO';
    const BOLETO_BANCARIO = 'BOLETO_BANCARIO';
    const RAPIPAGO = 'RAPIPAGO';
    const PAGOFACIL = 'PAGOFACIL';
    const BAPRO = 'BAPRO';
    const COBRO_EXPRESS = 'COBRO_EXPRESS';
    const SERVIPAG = 'SERVIPAG';
    const BANK_REFERENCED = 'BANK_REFERENCED';
    const VISANET = 'VISANET';
    const RIPSA = 'RIPSA';


    /**
     * payment methods availables in payu including its payment method type
     * 
     */
    private static $methods = array(
        PaymentMethods::VISA => array('name' => PaymentMethods::VISA, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::AMEX => array('name' => PaymentMethods::AMEX, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::DINERS => array('name' => PaymentMethods::DINERS, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::MASTERCARD => array('name' => PaymentMethods::MASTERCARD, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::DISCOVER => array('name' => PaymentMethods::DISCOVER, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::ELO => array('name' => PaymentMethods::ELO, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::PSE => array('name' => PaymentMethods::PSE, 'type' => PayuPaymentMethodType::PSE),

        PaymentMethods::BALOTO => array('name' => PaymentMethods::BALOTO, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::EFECTY => array('name' => PaymentMethods::EFECTY, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::BCP => array('name' => PaymentMethods::BCP, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::SEVEN_ELEVEN => array('name' => PaymentMethods::SEVEN_ELEVEN, 'type' => PayuPaymentMethodType::REFERENCED),

        PaymentMethods::OXXO => array('name' => PaymentMethods::OXXO, 'type' => PayuPaymentMethodType::REFERENCED),

        PaymentMethods::BOLETO_BANCARIO => array('name' => PaymentMethods::BOLETO_BANCARIO, 'type' => PayuPaymentMethodType::BOLETO_BANCARIO),

        PaymentMethods::RAPIPAGO => array('name' => PaymentMethods::RAPIPAGO, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::PAGOFACIL => array('name' => PaymentMethods::PAGOFACIL, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::BAPRO => array('name' => PaymentMethods::BAPRO, 'type' => PayuPaymentMethodType::CASH), 'BAPRO',

        PaymentMethods::COBRO_EXPRESS => array('name' => PaymentMethods::COBRO_EXPRESS, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::SERVIPAG => array('name' => PaymentMethods::SERVIPAG, 'type' => PayuPaymentMethodType::CASH),

        PaymentMethods::BANK_REFERENCED => array('name' => PaymentMethods::BANK_REFERENCED, 'type' => PayuPaymentMethodType::BANK_REFERENCED),

        PaymentMethods::VISANET => array('name' => PaymentMethods::VISANET, 'type' => PayuPaymentMethodType::CREDIT_CARD),

        PaymentMethods::RIPSA => array('name' => PaymentMethods::RIPSA, 'type' => PayuPaymentMethodType::CASH),
    );

    /**
     * Allowed cash payment methods available in the api
     */
    private static $allowedCashPaymentMethods = array(
        PaymentMethods::EFECTY,
        PaymentMethods::BALOTO,
        PaymentMethods::BCP,
        PaymentMethods::OXXO,
        PaymentMethods::RIPSA
    );


    /**
     * validates if a payment method exist in payu platform 
     * @param string $paymentMethod
     * @return true if the payment method exist false the otherwise
     */
    static function isValidPaymentMethod($paymentMethod)
    {
        return array_key_exists($paymentMethod, PaymentMethods::$methods);
    }

    /**
     * Returns the payment method info
     * @param string $paymentMethod
     * @return paymentMethod
     */
    static function getPaymentMethod($paymentMethod)
    {
        return PaymentMethods::$methods[$paymentMethod];
    }

    /**
     * verify if the cash payment method is valid to process payments
     * by api
     * @param string $paymentMethod
     * @return boolean
     */
    static function isAllowedCashPaymentMethod($paymentMethod)
    {
        return in_array($paymentMethod, PaymentMethods::$allowedCashPaymentMethods);
    }
}
