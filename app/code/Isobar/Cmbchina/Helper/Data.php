<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Isobar\Cmbchina\Helper;

/**
 * CmbChina data helper
 *
 * @api
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * convert JsonObject to map
     *
     * @param json
     * @return
     */
    public function stringToObject($json)
    {
        return json_decode($json, 10);
    }

    /**
     * convert map to JsonObject
     * @param array
     * @return
     */
    public function objectToString($array)
    {
        return urldecode(json_encode($this->objectToEncode($array)));
    }

    /**
     * urlencode map
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

    /**
     * encrypt function
     * default 128 bite ECB encrypt,NoPadding
     * mode：suppurt ECB，CBC , default ECB
     * padding：support NoPadding，PKCS5，PKCS7
     * size：support 128,192,256
     * iv: displacement vector, empty in ECB mode, not empty in CBC mode
     * @param string $plaintext
     * @return string
     */
    public function encrypt($plaintext, $secretKey, $mode, $padding, $iv, $size) {
        if (empty($plaintext) || empty($secretKey)) {
            return null;
        }
        $secretKey = base64_decode($secretKey);
        $plaintext = trim($plaintext);
        // 选择秘钥长度
        switch ($size) {
            case 192:
                $keySize = MCRYPT_RIJNDAEL_192;
                break;
            case 256:
                $keySize = MCRYPT_RIJNDAEL_256;
                break;
            default:
                $keySize = MCRYPT_RIJNDAEL_128;
        }
        // encrypt mode
        if ($mode == 'CBC') {
            $mode = MCRYPT_MODE_CBC;
        } else {
            $mode = MCRYPT_MODE_ECB;
        }
        // PKCS5，PKCS7 need to add padding
        if ($padding == 'PKCS7' || $padding == 'PKCS5') {
            $plaintext = $this->addPadding($plaintext);
        }
        // set displacement vector
        if (empty($iv)) {
            $iv = mcrypt_create_iv(mcrypt_get_iv_size($keySize, $mode), MCRYPT_RAND);
        }

        $encrypt_str = mcrypt_encrypt($keySize, $secretKey, $plaintext, $mode, $iv);
        return base64_encode($encrypt_str);
    }

    /**
     * decrypt function
     * default 128 bite ECB encrypt,NoPadding
     * mode：suppurt ECB，CBC , default ECB
     * padding：support NoPadding，PKCS5，PKCS7
     * size：support 128,192,256
     * iv: displacement vector, empty in ECB mode, not empty in CBC mode
     * @param string $plaintext
     * @return string
     */
    public function decrypt($ciphertext, $secretKey, $mode, $padding, $iv, $size) {
        if (empty($ciphertext) || empty($secretKey)) {
            return null;
        }
        $ciphertext = base64_decode($ciphertext);
        $secretKey = base64_decode($secretKey);
        // Choose key length
        switch ($size) {
            case 192:
                $keySize = MCRYPT_RIJNDAEL_192;
                break;
            case 256:
                $keySize = MCRYPT_RIJNDAEL_256;
                break;
            default:
                $keySize = MCRYPT_RIJNDAEL_128;
        }
        // encrypt mode
        if ($mode == 'CBC') {
            $mode = MCRYPT_MODE_CBC;
        } else {
            $mode = MCRYPT_MODE_ECB;
        }
        // set displacement vector
        if (empty($iv)) {
            $iv = mcrypt_create_iv(mcrypt_get_iv_size($keySize, $mode), MCRYPT_RAND);
        }
        $encrypt_str = mcrypt_decrypt($keySize, $secretKey, $ciphertext, $mode, $iv);
        $encrypt_str = trim($encrypt_str);
        // PKCS5，PKCS7 need to add padding
        if ($padding == 'PKCS7' || $padding == 'PKCS5') {
            $encrypt_str = $this->stripPadding($encrypt_str);
        }
        return $encrypt_str;
    }

    /**
     * Fill algorithm PKCS7Padding or PKCS5Padding
     * @param string $text
     * @return string
     */
    function addPadding($text) {
        $blocksize = mcrypt_get_block_size('rijndael-128', 'ecb');
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * Remove padding characters from decrypted plain text PKCS7Padding or PKCS5Padding
     * @param string $text
     * @return string
     */
    function stripPadding($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return $text;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return $text;
        }
        return substr($text, 0, -1*$pad);
    }

    /**
     * Generate AES key
     * @param string $size
     * @return string key
     */
    public function genKey($size) {
        $bytes = openssl_random_pseudo_bytes($size / 8);
        return base64_encode($bytes);
    }



}
