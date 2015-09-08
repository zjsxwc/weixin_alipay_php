<?php
namespace WatcherHangzhouPayment\Payment\Wxpay;

use WatcherHangzhouPayment\Payment\Request;
use WatcherHangzhouPayment\Payment\CommonUtil;

class WxpayRequest extends Request
{
    protected $unifiedOrderUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    protected $orderQueryUrl = 'https://api.mch.weixin.qq.com/pay/orderquery';

    public function form()
    {
        $params = array();
        $form['action'] = $this->unifiedOrderUrl . '?_input_charset=utf-8';
        $form['method'] = 'post';
        $form['params'] = $this->convertParams($this->params);
        return $form;
    }

    public function unifiedOrder()
    {
        $params = $this->convertParams($this->params);
        $xml = $this->toXml($params);
        $response = CommonUtil::postRequest($this->unifiedOrderUrl, $xml);
        return $response;
    }

    public function orderQuery()
    {
        $params = $this->params;
        $converted = array();
        $converted['appid'] = $this->options['key'];
        $converted['mch_id'] = $this->options["wxpay_account"];
        $converted['nonce_str'] = $this->getNonceStr();
        $converted['out_trade_no'] = $params['orderSn'];
        $converted['sign'] = strtoupper(CommonUtil::signParams($converted, '&key=' . $this->options['secret']));

        $xml = $this->toXml($converted);
        $response = CommonUtil::postRequest($this->orderQueryUrl, $xml);
        return $response;
    }

    public function fromXml($xml)
    {
        $array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array;
    }

    protected function convertParams($params)
    {

        $converted = array();

        $converted['appid'] = $this->options['key'];
        $converted['attach'] = '支付';
        $converted['body'] = mb_substr($this->filterText($params['title']), 0, 49, 'utf-8');
        $converted['mch_id'] = $this->options["wxpay_account"];
        $converted['nonce_str'] = $this->getNonceStr();
        $converted['notify_url'] = $params['notifyUrl'];
        $converted['out_trade_no'] = $params['orderSn'];
        $converted['spbill_create_ip'] = $this->getClientIp();
        $converted['total_fee'] = intval($params['amount'] * 100);
        $converted['trade_type'] = 'NATIVE';
        $converted['product_id'] = $params['orderSn'];
        $converted['sign'] = strtoupper(CommonUtil::signParams($converted, '&key=' . $this->options['secret']));

        return $converted;
    }

    private function toXml($array, $xml = false)
    {
        $simxml = new simpleXMLElement('<!--?xml version="1.0" encoding="utf-8"?--><root></root>');
 
        foreach ($array as $k => $v) {
            $simxml->addChild($k, $v);
        }
     
        return $simxml->saveXML();
    }

    private function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    private function getClientIp()
    {
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "unknown";
        }
            return $cip;
    }

    protected function filterText($text)
    {
        return str_replace(array('#', '%', '&', '+'), array('＃', '％', '＆', '＋'), $text);
    }
}
