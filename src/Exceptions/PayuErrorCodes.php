<?php

namespace Rzerostern\Payu\Exceptions;

/**
 * 
 * Contains the error codes  used in exceptions
 * @author Payu Latam
 * @since 1.0.0
 * @version 1.0
 * 
 */
class PayuErrorCodes
{

    /** json serialization error */
    const JSON_SERIALIZATION_ERROR = 'JSON_SERIALIZATION_ERROR';

    /** json deserialization error */
    const JSON_DESERIALIZATION_ERROR = 'JSON_DESERIALIZATION_ERROR';

    /** invalid parameters for build request */
    const INVALID_PARAMETERS = 'INVALID_PARAMETERS';

    /** connection error */
    const CONNECTION_EXCEPTION = 'CONNECTION_EXCEPTION';

    /** general api error */
    const API_ERROR = 'API_ERROR';
}
