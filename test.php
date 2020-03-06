<?php

$url = 'https://open.cmbchina.com/AccessGateway/transIn/releaseTagForQRPay.json';
$paramStr = 'mid=a1eabca4ef6443c6a15def95ab4bf6c0&aid=0cb30543eaf24ce6873327969f0a944c&random=5e61f1b3b2bcc&amount=5e61f1b3b0d83&date=20200306144611&protocol=cmblife%3A%2F%2Fpay%3Faid%3D0cb30543eaf24ce6873327969f0a944c%26amount%3D1800%26billNo%3Dfac23a95210c4c80a1fa8e903f9ff822%26bonus%3D0%26date%3D20200306144611%26mid%3Da1eabca4ef6443c6a15def95ab4bf6c0%26notifyUrl%3Dhttps%253A%252F%252Ftest.com%252Fmerchant%252FpayNotify.json%26productName%3D%25E6%25B5%258B%25E8%25AF%2595%25E5%2595%2586%25E5%2593%2581%26random%3D5e61f1b3b0d99%26returnUrl%3Dhttps%253A%252F%252Ftest.com%252Fmerchant%252FpayCallback.html%26sign%3Ddpf%252BOr5CyDKFoSiK4ovGQYYmp4Mac6W8N%252FkKW1YyAvJJJZHWSigPNSFAtskBDn4xnXrDX%252F%252FAhVvyojdQgo%252BhD3a55ndw7Ko6NVPrFu9MgsGJcT5EwLwqOd51DHTsp4DGcwUY0WV7SNJijPRLTWxvmAu1tVldGL2326%252Bo3tsgMY3tb8fFnlQ7f8QXZHWj40zmtuHXivz7aAx60QyFkmKL2zgpiRdx3z6ba2VOHWuUJtxprfnzs0DoB1CRDdcJFFV1qT9CNdsDnK21y3AtRJJbvcjx6Droi2iiujIckz6B7PGynpo1n9luDKNHQxWo%252BwXrZG3aPmQh0uQf%252B5J48gTiFQ%253D%253D&respCode=1000&respMsg=%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F%EF%BC%81&sign=QayET%2FdNwIBlwnAub%2B3yTMS%2BrR8vD7qOdxt5rhC%2F5VAE%2FKdkasVkZgWZb3FdRphGZ3GdKeWfHQqfaMRBP4fYf8Ye2OL9vVAnamoXXjUDFjGQzt%2FuHa4qAL7oDooSPyNwzdaH2n%2BSVTduC0oKkBYuatECoL%2B4kktG8bKwMrkZ2UNEwWealFYXCvVU3nNy1EvbVTfqShury7LJkMc1MDRPtTei%2FqzKJj4VcA2p%2FdpmnKhVyNI9epr4Go1Ji5aziNJFx9LQcESd8pdQnTDR%2BVluPyTRj6128emgsCrcy7Nk0QynAuu3eOneEEGDtyYQT%2FVRfkGYKyZmRWyTb3jDpT5mFw%3D%3D';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $paramStr,
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/x-www-form-urlencoded"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

var_dump($response);

if (!$err)
{
    var_dump($response);
}else {
    //var_dump($err);
}

die();