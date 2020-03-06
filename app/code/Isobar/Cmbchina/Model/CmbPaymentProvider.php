<?php

namespace Isobar\Cmbchia\Model;

use Magento\Checkout\Model\ConfigProviderInterface;


class CmbPaymentProvider implements ConfigProviderInterface
{
    public function getConfig()
    {
        return [
            'code' => 'cmbchina'
        ];
    }
}