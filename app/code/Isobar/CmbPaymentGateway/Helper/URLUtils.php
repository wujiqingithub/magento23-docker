<?php
namespace Isobar\CmbPaymentGateway\Helper;

class URLUtils extends \Magento\Framework\App\Helper\AbstractHelper
{

     public function __construct()
    {
        $this->jsonUtils = new JsonUtils();
    }

    /**
     * 将map转为queryString
     * @param map 参数
     * @param isSort 是否排序
     * @param isUrlEncode 是否需要UrlEncode
     * @return String
     */
    public function mapToQueryString($params, $isSort, $isUrlEncode)
    {
        if (sizeof($params) <= 0) {
            return null;
        }
        $tempParams = $params;
        if ($isSort) {
            ksort ($params);
            $tempParams = $params;
        }
        foreach($tempParams as $key=>$value) {
            if(empty($value) && "0" != $value) {
                continue;
            }
            if(false === $value) {$value = "false";}
            if(true === $value) {$value = "true";}
            if (is_array($value)) {
                $value = $this->jsonUtils->objectToString($value);
            }
            if ($isUrlEncode) {
                $value = urlencode($value);
            }
            $sb[] = $key.'='.$value;
        }
        // echo "</br>".implode('&', $sb);
        return implode('&', $sb);
    }

    /**
     * 拼接签名字符串
     * @param prefix 前缀
     * @param queryString 参数
     * @return 拼接后的字符串
     */
    public function assembleUrl($prefix, $queryString) {
        return empty($prefix) ? $queryString : 
        $prefix.(strpos($prefix, "?") !== false ? "&" : "?").$queryString;
    }

    /**
     * 拼接签名字符串
     * @param prefix 前缀
     * @param paramsMap 参数
     * @param isUrlEncode 是否urlEncode
     * @return 拼接后的字符串
     */
    public function assembleUrlWithParams($prefix, $params, $isUrlEncode) {
        return $this->assembleUrl($prefix, $this->mapToQueryString($params, true, $isUrlEncode));
    }
    
}
?>