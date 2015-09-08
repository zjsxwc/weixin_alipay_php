<?php
namespace WatcherHangzhouPayment\Payment\Wxpay;

use WatcherHangzhouPayment\Payment\Response;

class WxpayResponse extends Response
{
    public function getPayData()
    {
        $params = $this->params;
        if (!$this->isRightSign($params)) {
            throw new \RuntimeException('微信支付签名校验失败。');
        }
        //TODO 需要向微信再次询问此回调是否正确
        $data = array();
        $data['payment'] = 'wxpay';
        $data['sn'] = $params['out_trade_no'];
        if (in_array($params['result_code'], array('SUCCESS','ORDERPAID'))) {
            $data['status'] = 'success';
        } else if (in_array($params['result_code'], array('ORDERCLOSED'))) {
            $data['status'] = 'closed';
        } else {
            $data['status'] = 'unknown';
        }
        $data['amount'] = ((float) $params['total_fee']) / 100;

        if (!empty($params['time_end'])) {
            $data['paidTime'] = strtotime($params['time_end']);
        } else {
            $data['paidTime'] = time();
        }
        return $data;
    }

    private function isRightSign($params)
    {
        $signPars = "";
        ksort($params);
        foreach ($params as $k => $v) {
            if ("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->options['secret'];
        
        $sign = strtolower(md5($signPars));
        
        $tenpaySign = strtolower($params("sign"));
        return $sign == $tenpaySign;
        
    }
}
