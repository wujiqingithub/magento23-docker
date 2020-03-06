<?php
namespace Isobar\CmbPaymentGateway\Helper;

class JsonUtils extends \Magento\Framework\App\Helper\AbstractHelper{

    /**
     * 将JsonObject转为map
     *
     * @param json
     * @return
     */
    public function stringToObject($json)
    {
        return json_decode($json, 10);
    }

    /**
     * 将map转为JsonObject
     * @param array
     * @return
     */
    public function objectToString($array)
    {
        return urldecode(json_encode($this->objectToEncode($array)));
    }

    /**
     * 将map进行urlencode
     * @param array
     * @return
     */
    private function objectToEncode($array)
    {
        foreach($array as $key=>$value)
        {
            if (is_string($value))
            {
                $array[$key] = urlencode($value);
            }

            if (is_array($value))
            {
                $array[$key] = $this->objectToEncode($value);
            }
        }
        return $array;
    }

}
?>