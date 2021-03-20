<?php

namespace Core\Config;

class PaymentGateway
{
    public static $merchantId = "1213639";
    public static $returnUrl = "https:://homey.lk/payment/done";
    public static $cancelUrl = "https://homey.lk/payment/cancel";
    public static $notifyUrl = "https://homey.lk/payment/notify";
    public static $currency = "LKR";
}
