<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Isobar\Cmbchina\Model\Ui;

use Isobar\Cmbchina\Gateway\Config\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class ConfigProvider
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'cmbchina';

    const CC_VAULT_CODE = 'cmbchina_cc_vault';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * Constructor
     *
     * @param Config $config
     * @param SessionManagerInterface $session
     */
    public function __construct(
        Config $config,
        SessionManagerInterface $session
    ) {
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $storeId = $this->session->getStoreId();
        $isActive = $this->config->isActive($storeId);
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $isActive
                ]
            ],
        ];
    }

}