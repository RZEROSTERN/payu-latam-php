<?php

namespace Rzerostern\PayU\Api;

class TransactionType
{
    /** Only authorization transaction. */
    const AUTHORIZATION = "AUTHORIZATION";

    /** Authorization and capture transaction. */
    const AUTHORIZATION_AND_CAPTURE = "AUTHORIZATION_AND_CAPTURE";

    /** Only capture transaction. */
    const CAPTURE = "CAPTURE";

    /** Cancel transaction. */
    const CANCELLATION = "CANCELLATION";

    /** Void transaction. */
    const VOID = "VOID";

    /** Refund transaction. */
    const REFUND = "REFUND";

    /** Credit transaction. */
    const CREDIT = "CREDIT";
}
