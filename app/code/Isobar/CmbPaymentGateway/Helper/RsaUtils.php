<?php
namespace Isobar\CmbPaymentGateway\Helper;

use phpseclib\Crypt\RSA;

class RsaUtils extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $rsa;

    /**
     * X constructor.
     */
    public function __construct()
    {
        $this->rsa = new RSA();
    }

    public function encrypt($plaintext, $encryptionMode, $pubKey) 
    {
        $this->rsa->loadKey($pubKey);
        $this->rsa->setEncryptionMode($encryptionMode);
        $ciphertext = $this->rsa->encrypt($plaintext);

        return base64_encode($ciphertext);
    }

    public function decrypt($ciphertext, $encryptionMode, $priKey) 
    {
        $this->rsa->loadKey($priKey);
        $this->rsa->setEncryptionMode($encryptionMode);
        $plaintext = $this->rsa->decrypt(base64_decode($ciphertext));

        return $plaintext;
    }

    public function genKey($size) 
    {
        return $this->rsa->createKey($size);
    }
}

?>