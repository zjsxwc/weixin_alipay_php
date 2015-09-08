<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 9/7/15
 * Time: 4:49 PM
 */

namespace WatcherHangzhouPayment\Payment\Wxpay;

class AlipayConfig
{
    public static function getSettings()
    {
        return ["secret" => "xxxx", 'key' => "ccc", "type" => "direct"];
    }
}
