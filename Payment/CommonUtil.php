<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 9/7/15
 * Time: 4:56 PM
 */

namespace WatcherHangzhouPayment\Payment;


class CommonUtil {
    static function signParams($params, $secret) {
        unset($params['sign_type']);
        unset($params['sign']);

        ksort($params);

        $sign = '';
        foreach ($params as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $sign .= $key . '=' . $value . '&';
        }
        $sign = substr($sign, 0, - 1);
        $sign .= $secret;

        return md5($sign);
    }
}