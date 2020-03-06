<?php

namespace Isobar\Cmbchina\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        var_dump(1233);
        return $this->resultPageFactory->create();
    }
}
