<?php

namespace Magenest\Core\Plugin\Controller;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;

class B2MeNotification
{
    const B2ME_ACTION = 'b2me';
    const SUBMITED = 1;
    protected $messageManager;

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }
    /**
     * @param FrontControllerInterface $subject
     * @param $result
     * @param RequestInterface $request
     * @return mixed
     */
    public function afterDispatch(FrontControllerInterface $subject, $result, RequestInterface $request)
    {
        $resultAction = $request->getParam('success');
        if ($request->getFrontName() == self::B2ME_ACTION && $resultAction == self::SUBMITED) {
            $this->messageManager->addSuccessMessage(__("Thank you for sending, we will contact you soon."));
        }
        return $result;
    }
}
