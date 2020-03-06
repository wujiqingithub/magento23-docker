<?php
namespace Isobar\CmbPaymentGateway\Helper;

use phpseclib\Crypt\RSA;

class CmblifeUtils extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $rsa;

    /**
     * X constructor.
     */
    public function __construct()
    {

        $curl = curl_init();

        $paramstr = 'amount=5e61c29d34ad2&date=20200306112517&protocol=cmblife%3A%2F%2Fpay%3Faid%3D0cb30543eaf24ce6873327969f0a944c%26amount%3D1800%26billNo%3Dfac23a95210c4c80a1fa8e903f9ff822%26bonus%3D0%26date%3D20200306112517%26mid%3Da1eabca4ef6443c6a15def95ab4bf6c0%26notifyUrl%3Dhttps%253A%252F%252Ftest.com%252Fmerchant%252FpayNotify.json%26productName%3D%25E6%25B5%258B%25E8%25AF%2595%25E5%2595%2586%25E5%2593%2581%26random%3D5e61c29d34ae8%26returnUrl%3Dhttps%253A%252F%252Ftest.com%252Fmerchant%252FpayCallback.html%26sign%3DVKiLbvEfT4fu%252B0DtG6lWC7CEmDbTCfhqxjASEstE39BdODWdfd1C4D93jW8iY4JyRtD1Mixc7Mbt5LbTcb3Z2F9e%252Ba32u2tylLdrMDAxny8mMwYAZuIiLX859hTN3eCxQP9w3wGAHNggKjTK0EDw8eeKmg9iTqGhE%252FyNQbMwhsbA7FXWNM83vwcgretDWcWSDPR0UBS7cMVryVD6QB5PXr6mFP78IzP4zX5nknrGQT4O%252F1U%252Fl7jYIleKzAdBYgcNQKBREtWfK1n2bShIqYlI%252FFsPH0XV61bxfGKSQ8FV53xD%252B1MWv%252FDYWovufWGmQmnY1sw7bDRzXpaLkyza0ZZKsA%253D%253D&respCode=1000&respMsg=%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F%EF%BC%81&sign=c%2BR03W8S8AE83Pp7NOtFB3079WZVWGxnsF6r1I3W7E3z0hAoiBTUCb6M7wABJRVb1jqZHqv0JbW2pQI908gehbOAW%2FGIOISZskg8VaiTCWP0qMTaHeYzChdLh5DBUlIbrx9Ds3F0ILZS5nrEExNxAKvSnxTbzO3hK6RQFO8A%2B6GGZuv7qI1DfkpD4xDGksqDtq1WUoRa9hXRsIFRG0YpSv7CZjFKbWHHG%2FwqLPQlkV8uzsYXdwo0kakdfmWYNhYEyk%2BuVPhX%2Fznk5Maiu1OU76QD0JwRerLoSXtQ7NCHOU%2FZztRR4pG2Y10155cd6%2BNdg4bKax7ekVGmrGd9x0fceQ%3D%3D:';
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://open.cmbchina.com/AccessGateway/transIn/releaseTagForQRPay.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $paramstr,
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT:!DH',
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
        {
            var_dump($response);
        }else{
            var_dump($$err);
        }

        die();


        $this->rsa = new RSA();

        $this->aesUtils = new AesUtils();

        $this->rsaUtils = new RsaUtils();

        $this->urlUtils = new URLUtils();

        $this->jsonUtils = new JsonUtils();

        $this->rsa->setHash('sha256');
        //设置签名加密方式
        $this->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        //设置加密方式
        $this->rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    }

    /**
     * 生成签名
     *
     * @param $plaintext
     * @return string
     */
    public function createSign($plaintext, $priKey, $signAlgorithm)
    {
        $this->rsa->setHash($signAlgorithm);
        $this->rsa->loadKey($priKey);
        $signature = $this->rsa->sign($plaintext);
        return base64_encode($signature);
    }

    /**
     * 验签,使用商户的公钥对返回的参数验签
     *
     * @param $ciphertext
     * @return string
     */
    public function verifySign($ciphertext, $signature, $pubKey, $signAlgorithm)
    {
        $this->rsa->setHash($signAlgorithm);
        $this->rsa->loadKey($pubKey);
        $signature = $this->rsa->verify($ciphertext, base64_decode($signature));
        return $signature;
    }

    /**
     * 拼接掌上生活协议
     * @param funcName 功能名
     * @param queryString 参数
     * @return 拼接后的字符串
     */
    public function assembleProtocol($funcName, $queryString) {
        return $this->urlUtils->assembleUrl('cmblife://'.$funcName, $queryString);
    }

    /**
     * 拼接掌上生活协议
     * @param funcName 功能名
     * @param paramsMap 参数
     * @param isUrlEncode 是否urlEncode
     * @return 拼接后的字符串
     */
    public function assembleProtocolWithParams($funcName, $params, $isUrlEncode) {
        return $this->urlUtils->assembleUrlWithParams('cmblife://'.$funcName, $params, $isUrlEncode);
    }

    /**
     * 拼接签名
     * @param protocol 协议
     * @param sign 签名
     * @return 拼接签名后的协议
     */
    public function assembleSign($protocol, $sign) {
        if (empty($protocol)) {
            return null;
        }
        return $protocol.(strpos($protocol, "?") !== false ? "&" : "?")."sign=".urlencode($sign);
    }

    /**
     * 生成掌上生活协议,带有签名
     * @param funcName 功能名
     * @param paramsMap 参数
     * @param signKey 签名所使用的Key，为商户私钥
     * @param signAlgorithm 签名算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 掌上生活协议
     */
    public function genProtocolWithAlgorithm($funcName, $params, $signKey, $signAlgorithm) {
        if (empty($funcName)) {
            return null;
        }
        $signProtocol = $this->assembleProtocolWithParams($funcName, $params, false);
        $sign = $this->createSign($signProtocol, $signKey, $signAlgorithm);
        return $this->assembleSign($this->assembleProtocolWithParams($funcName, $params, true), $sign);
    }

    /**
     * 生成掌上生活协议，带有签名
     * @param funcName 功能名
     * @param paramsMap 参数
     * @param signKey 签名所使用的Key，为商户私钥
     * @return 掌上生活协议
     */
    public function genProtocol($funcName, $params, $signKey) {
        return $this->genProtocolWithAlgorithm($funcName, $params, $signKey, 'sha256');
    }

    /**
     * 生成请求报文体
     * @param params 参数
     * @return 请求报文体，如： key1=value1&key2=value2...
     */
    public function genRequestBody($params) {
        if (empty($params)) {
            return null;
        }
        return $this->urlUtils->assembleUrlWithParams(null, $params, true);
    }

    /**
     * 对响应验签
     * 调用方向：商户 --> 掌上生活
     *
     * @param response 响应报文
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @return true为验签成功，false为验签失败
     * @throws GeneralSecurityException
     */
    public function verifyForResponse($response, $verifyKey) {
        $params = $this->jsonUtils->stringToObject($response);
        $sign = $params["sign"];
        unset($params["sign"]);
        return $this->verify($this->urlUtils->assembleUrlWithParams("", $params, false), $sign, $verifyKey, 'sha256');
    }

    /**
     * 对请求验签
     * 调用方向：掌上生活 --> 商户
     *
     * @param params 参数
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @return true为验签成功，false为验签失败
     * @throws GeneralSecurityException
     */
    public function verifyForRequest($params, $verifyKey) {
        $sign = $params["sign"];
        unset($params["sign"]);
        return $this->verify($this->urlUtils->assembleUrlWithParams("", $params, false), $sign, $verifyKey, 'sha256');
    }

    /**
     * 对响应验签
     * 调用方向：商户 --> 掌上生活
     *
     * @param response 响应报文
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @return true为验签成功，false为验签失败
     * @throws GeneralSecurityException
     */
    public function signForRequest($funcName, $params, $signKey) {
        return $this->sign($this->urlUtils->assembleUrlWithParams($funcName.".json", $params, false), $signKey, 'sha256');
    }

    /**
     * 对响应签名
     * 调用方向：掌上生活 --> 商户
     *
     * @param params 参数
     * @param signKey 签名使用的Key，为商户RSA私钥
     * @return 签名
     * @throws GeneralSecurityException
     */
    public function signForResponse($params, $signKey) {
        return $this->sign($this->urlUtils->assembleUrlWithParams("", $params, false), $signKey, 'sha256');
    }

    /**
     * 签名
     * @param signBody 待签名数据，queryString
     * @param signKey 签名使用的Key，为商户私钥
     * @param signAlgorithm 签名算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 签名
     */
    public function signWithAlgorithm($signBody, $signKey, $signAlgorithm) {
        if (empty($signBody) || empty($signKey) || empty($signAlgorithm)) {
            return null;
        }
        return $this->createSign($signBody, $signKey, $signAlgorithm);
    }

    /**
     * 签名
     * @param signBody 待签名数据，queryString
     * @param signKey 签名使用的Key，为商户私钥
     * @return 签名
     */
    public function sign($signBody, $signKey) {
        if (empty($signBody) || empty($signKey)) {
            return null;
        }
        return $this->signWithAlgorithm($signBody, $signKey, 'sha256');
    }

    /**
     * 签名
     * @param paramsMap 待签名数据，params
     * @param signKey 签名使用的Key，为商户私钥
     * @param signAlgorithm 签名算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 签名
     */
    public function signWithParamsAndAlgorithm($params, $signKey, $signAlgorithm) {
        if (empty($params) || empty($signKey) || empty($signAlgorithm)) {
            return null;
        }
        return $this->signWithAlgorithm($this->urlUtils->mapToQueryString($params, true, false), $signKey, $signAlgorithm);
    }

    /**
     * 签名
     * @param paramsMap 待签名数据，queryString
     * @param signKey 签名使用的Key，为商户私钥
     * @return 签名
     */
    public function signWithParams($params, $signKey) {
        if (empty($params) || empty($signKey)) {
            return null;
        }
        return $this->signWithParamsAndAlgorithm($params, $signKey, 'sha256');
    }

    /**
     * 签名
     * @param prefix 前缀，如interface.json
     * @param paramsMap 待签名数据，queryString
     * @param signKey 签名使用的Key，为商户私钥
     * @param signAlgorithm 签名算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 签名
     */
    public function signWithPrefixAndParamsAndAlgorithm($prefix, $params, $signKey, $signAlgorithm) {
        $url = $this->urlUtils->assembleUrlWithParams($prefix, $params, false);
        return $this->sign($url, $signKey, $signAlgorithm);
    }

    /**
     * 签名
     * @param prefix 前缀，如interface.json
     * @param paramsMap 待签名数据，queryString
     * @param signKey 签名使用的Key，为商户私钥
     * @param signAlgorithm 签名算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 签名
     */
    public function signWithPrefixAndParams($prefix, $params, $signKey) {
        $url = $this->urlUtils->assembleUrlWithParams($prefix, $params, false);
        return $this->sign($url, $signKey, 'sha256');
    }

    /**
     * 验签
     * @param verifyBody 待验签的数据，queryString
     * @param sign 签名
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @param verifyAlgorithm  验签算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 验签结果
     */
    public function verifyWithAlgorithm($verifyBody, $sign, $verifyKey, $algorithm) {
        if (empty($verifyBody) || empty($sign) || empty($verifyKey) || empty($algorithm)) {
            return null;
        }
        return $this->verifySign($verifyBody, $sign, $verifyKey, $algorithm);
    }

    /**
     * 验签
     * @param verifyBody 待验签的数据，queryString
     * @param sign 签名
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @return 验签结果
     */
    public function verify($verifyBody, $sign, $verifyKey) {
        return $this->verifyWithAlgorithm($verifyBody, $sign, $verifyKey, 'sha256');
    }

    /**
     * 验签
     * @param paramsMap 掌上生活返回报文
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @param verifyAlgorithm 验签算法（SHA1WithRSA 或 SHA256WithRSA）
     * @return 验签结果
     */
    public function verifyWithParamsAndAlgorithm($params, $verifyKey, $algorithm) {
        $sign = $params['sign'];
        unset($params['sign']);
        return $this->verifyWithAlgorithm($this->urlUtils->mapToQueryString($params, true, false), $sign, $verifyKey, $algorithm);
    }

    /**
     * 验签
     * @param paramsMap 掌上生活返回报文
     * @param verifyKey 验签所使用的Key，为掌上生活公钥
     * @return 验签结果
     */
    public function verifyWithParams($params, $verifyKey) {
        return $this->verifyWithParamsAndAlgorithm($params, $verifyKey, 'sha256');
    }

    /**
     * 掌上生活加密
     * @param encryptBody 需要加密的字符串
     * @param encryptKey 加密使用的Key，为掌上生活RSA公钥
     * @return 密文
     */
    public function encrypt($encryptBody, $encryptKey) {
        if (empty($encryptBody) || empty($encryptKey)) {
            return null;
        }
        $aesKey = $this->aesUtils->genKey(128);
        $aesEncryptedBody = $this->aesUtils->encrypt($encryptBody, $aesKey, 'ECB', 'PKCS5', null, 128);
        $encryptedAesKey = $this->rsaUtils->encrypt(base64_decode($aesKey), CRYPT_RSA_ENCRYPTION_PKCS1, $encryptKey);
        return $encryptedAesKey."|".$aesEncryptedBody;
    }

    /**
     * 掌上生活解密
     * @param decryptBody 需要解密的字符串
     * @param decryptKey 解密使用的Key，为商户RSA私钥
     * @return 明文
     */
    public function decrypt($decryptBody, $decryptKey) {
        if (empty($decryptBody) || empty($decryptKey)) {
            return null;
        }
        $data = explode('|', $decryptBody);
        if(2 != count($data)) {
            return null;
        }
        $aesKey = $this->rsaUtils->decrypt($data[0], CRYPT_RSA_ENCRYPTION_PKCS1, $decryptKey);
        return $this->aesUtils->decrypt($data[1], base64_encode($aesKey), 'ECB', 'PKCS5', null, 128);
    }

     /**
     * 将json字符串反序列化为map
     *
     * @param json json字符串
     * @return 参数
     */
    public function jsonToMap($json) {
        return $this->jsonUtils->stringToObject($json);
    }

    /**
     * 将map序列化为json字符串
     *
     * @param params 参数
     * @return json字符串
     */
    public function mapToJson($params) {
        return $this->jsonUtils->objectToString($params);
    }
    
    /**
     * 生成日期
     *
     * @return 日期，格式为yyyyMMddHHmmss
     */
    public static function genDate() {
        date_default_timezone_set('PRC');
        return date("YmdHis");
    }

    /**
     * 生成随机数
     *
     * @return 随机数，为UUID
     */
    public static function genRandom() {
        return str_replace('-', '', uniqid());
    }
}
?>