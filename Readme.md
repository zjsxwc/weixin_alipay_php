

微信支付依赖composer的Symfony\Component\DependencyInjection\SimpleXMLElement


#使用阿里支付
```
<?php
namespace WatcherHangzhouPayment\Payment;

$payRequestParams = array(
    'returnUrl' => $this->generateUrl('pay_return', array('name' => 'Alipay'), true),
    'notifyUrl' => $this->generateUrl('pay_notify', array('name' => 'Alipay'), true),
    'showUrl' => $this->generateUrl('show_goods', array('id' => $goods['id']), true),
);

$paymentRequest = $this->createPaymentRequest($order, $requestParams);

function createPaymentRequest($order, $requestParams)
{
    $requestParams = array_merge($requestParams, array(
            'orderSn' => $order['sn'],
            'title' => $order['title'],
            'summary' => '',
            'amount' => $order['amount'],
    ));
    return Payment::createRequest($order['payment'], $requestParams);
}

$htmlForm = $request->form();
$inputHtml = '';
foreach ($htmlForm['params'] as $key => $value) {
    $inputHtml .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\">";
}
$html = <<<EOF
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Jumping to alipay gateway...</title>
<body>

  <form action="{$htmlForm['action']}"  method="{$htmlForm['method']}" name="form">
    {$inputHtml}
  </form>

  <script>
    document.all.form.submit();
  </script>

</body>
</html>
EOF;

echo $html;
die;

```

#使用微信支付
```
<?php
namespace WatcherHangzhouPayment\Payment;

<?php
namespace WatcherHangzhouPayment\Payment;

$payRequestParams = array(
    'returnUrl' => $this->generateUrl('pay_return', array('name' => 'Wxpay'), true),
    'notifyUrl' => $this->generateUrl('pay_notify', array('name' => 'Wxpay'), true),
    'showUrl' => $this->generateUrl('show_goods', array('id' => $goods['id']), true),
);

$paymentRequest = $this->createPaymentRequest($order, $requestParams);

function createPaymentRequest($order, $requestParams)
{
    $requestParams = array_merge($requestParams, array(
            'orderSn' => $order['sn'],
            'title' => $order['title'],
            'summary' => '',
            'amount' => $order['amount'],
    ));
    return Payment::createRequest($order['payment'], $requestParams);
}


$returnXml = $paymentRequest->unifiedOrder();
$returnArray = $paymentRequest->fromXml($returnXml);
if ($returnArray['return_code'] == 'SUCCESS') {
    $url = $returnArray['code_url'];

    $html = <<<EOF
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>使用微信二维码支付</title>
<body>

  <img src="{{ path('generate_qrcode_image',{url:url}) }}" >
          <div class="text-qrcode hidden-xs">
            请使用微信扫一扫<br>扫描二维码支付
          </div>

</body>
</html>
EOF;
    echo $html;
    die;
}


```