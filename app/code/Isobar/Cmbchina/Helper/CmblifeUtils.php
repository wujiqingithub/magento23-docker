<?php
namespace Isobar\Cmbchina\Helper;

/**
 * CmbChina CmblifeUtils helper
 *
 * @api
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class CmblifeUtils extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $rsa;

    /**
     * X constructor.
     */
    public function __construct()
    {
        $this->rsa = new Crypt_RSA();

        $this->aesUtils = new AesUtils();

        $this->rsaUtils = new RsaUtils();

        $this->urlUtils = new URLUtils();

        $this->jsonUtils = new JsonUtils();

        $this->rsa->setHash('sha256');
        //set signature mode
        $this->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        //set encryption mode
        $this->rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    }

    /**
     * generate sign
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
     * verify signature,use merchant public to verify signature
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
     * Splice cmblife Agreement
     * @param funcName function name
     * @param queryString params
     * @return string
     */
    public function assembleProtocol($funcName, $queryString) {
        return $this->urlUtils->assembleUrl('cmblife://'.$funcName, $queryString);
    }

    /**
     * Splice cmblife Agreement
     * @param funcName function name
     * @param paramsMap params
     * @param isUrlEncode is urlEncode
     * @return Splice string
     */
    public function assembleProtocolWithParams($funcName, $params, $isUrlEncode) {
        return $this->urlUtils->assembleUrlWithParams('cmblife://'.$funcName, $params, $isUrlEncode);
    }

    /**
     * Splice signature with signature
     * @param protocol Agreement
     * @param sign signature
     * @return Agreement with signature
     */
    public function assembleSign($protocol, $sign) {
        if (empty($protocol)) {
            return null;
        }
        return $protocol.(strpos($protocol, "?") !== false ? "&" : "?")."sign=".urlencode($sign);
    }

    /**
     * Splice cmblife Agreement, with signature
     * @param funcName function name
     * @param paramsMap params
     * @param signKey Key，merchant private key
     * @param signAlgorithm signature Algorithm（SHA1WithRSA 或 SHA256WithRSA）
     * @return cmblife Agreement
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
     * cmblife Agreement，with signature
     * @param funcName function name
     * @param paramsMap params
     * @param signKey Key，merchant private key
     * @return cmblife Agreement
     */
    public function genProtocol($funcName, $params, $signKey) {
        return $this->genProtocolWithAlgorithm($funcName, $params, $signKey, 'sha256');
    }

    /**
     * Generate request body
     * @param params params
     * @return request body，eg： key1=value1&key2=value2...
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
     * sign
     * @param prefix eg: interface.json
     * @param paramsMap data to be signed，queryString
     * @param signKey key，private
     * @param signAlgorithm（SHA1WithRSA or SHA256WithRSA）
     * @return sign
     */
    public function signWithPrefixAndParamsAndAlgorithm($prefix, $params, $signKey, $signAlgorithm) {
        $url = $this->urlUtils->assembleUrlWithParams($prefix, $params, false);
        return $this->sign($url, $signKey, $signAlgorithm);
    }

    /**
     * sign
     * @param prefix
     * @param paramsMap data to be signed，queryString
     * @param signKey merchant private key
     * @param signAlgorithm（SHA1WithRSA or SHA256WithRSA）
     * @return 签名
     */
    public function signWithPrefixAndParams($prefix, $params, $signKey) {
        $url = $this->urlUtils->assembleUrlWithParams($prefix, $params, false);
        return $this->sign($url, $signKey, 'sha256');
    }

    /**
     * verify signature
     * @param verifyBody str to be verified，queryString
     * @param sign signature
     * @param verifyKey key to verify signature，merchant public key
     * @param verifyAlgorithm Algorithm to verify signature（SHA1WithRSA or SHA256WithRSA）
     * @return verification result
     */
    public function verifyWithAlgorithm($verifyBody, $sign, $verifyKey, $algorithm) {
        if (empty($verifyBody) || empty($sign) || empty($verifyKey) || empty($algorithm)) {
            return null;
        }
        return $this->verifySign($verifyBody, $sign, $verifyKey, $algorithm);
    }

    /**
     * verify signature
     * @param verifyBody str to be verified，queryString
     * @param sign signature
     * @param verifyKey key to verify signature，merchant public key
     * @return verify result
     */
    public function verify($verifyBody, $sign, $verifyKey) {
        return $this->verifyWithAlgorithm($verifyBody, $sign, $verifyKey, 'sha256');
    }

    /**
     * verify signature
     * @param paramsMap response body from cmblife
     * @param verifyKey key to verify signature, merchant public key
     * @param verifyAlgorithm Algorithm to verify signature（SHA1WithRSA or SHA256WithRSA）
     * @return 验签结果
     */
    public function verifyWithParamsAndAlgorithm($params, $verifyKey, $algorithm) {
        $sign = $params['sign'];
        unset($params['sign']);
        return $this->verifyWithAlgorithm($this->urlUtils->mapToQueryString($params, true, false), $sign, $verifyKey, $algorithm);
    }

    /**
     * verify signature
     * @param paramsMap response body from cmblife
     * @param verifyKey key to verify signature, merchant public key
     * @return verification result
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
     * cmblift decrypt
     * @param decryptBody
     * @param decryptKey merchat private key
     * @return Plaintext
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
     * convert json str to map
     *
     * @param json json str
     * @return params
     */
    public function jsonToMap($json) {
        return $this->jsonUtils->stringToObject($json);
    }

    /**
     * convert map to json
     *
     * @param params
     * @return json string
     */
    public function mapToJson($params) {
        return $this->jsonUtils->objectToString($params);
    }

    /**
     * generate date
     *
     * @return date，format yyyyMMddHHmmss
     */
    public static function genDate() {
        date_default_timezone_set('PRC');
        return date("YmdHis");
    }

    /**
     * random str
     *
     * @return random，为UUID
     */
    public static function genRandom() {
        return str_replace('-', '', uniqid());
    }
}