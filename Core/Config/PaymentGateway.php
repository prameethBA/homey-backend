<?php

namespace Core\Config;

class PaymentGateway
{
    protected static $merchantId = "1213639";
    protected static $returnUrl = "https:://homey.lk/payment/done";
    protected static $cancelUrl = "https://homey.lk/payment/cancel";
    protected static $notifyUrl = "https://homey.lk/payment/notify";
    protected static $currency = "LKR";
}
