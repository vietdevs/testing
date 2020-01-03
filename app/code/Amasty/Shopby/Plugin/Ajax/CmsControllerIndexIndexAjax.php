<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Ajax;

use Amasty\Shopby\Helper\State;

/**
 * Class CmsControllerIndexIndexAjax
 * @package Amasty\Shopby\Plugin\Ajax
 */
class CmsControllerIndexIndexAjax extends Ajax
{
    /**
     * @var \Amasty\Shopby\Model\Layer\Cms\Manager
     */
    protected $cmsManager;

    public function __construct(
        \Amasty\Shopby\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Url\Encoder $urlEncoder,
        \Magento\Framework\Url\Decoder $urlDecoder,
        State $stateHelper,
        \Amasty\Shopby\Model\Layer\Cms\Manager $cmsManager,
        \Magento\Framework\View\DesignInterface $design
    ) {
        $this->cmsManager = $cmsManager;
        parent::__construct($helper, $resultRawFactory, $urlEncoder, $urlDecoder, $design, $stateHelper);
    }

    /**
     * @param \Magento\Cms\Controller\Index\Index $action
     * @param $resultPage
     * @return \Magento\Framework\Controller\Result\Raw
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterExecute(
        \Magento\Cms\Controller\Index\Index $action,
        $resultPage
    ) {
        if (!$this->isAjax($action->getRequest()) || !$resultPage instanceof \Magento\Framework\View\Result\Page) {
            return $resultPage;
        }

        $cmsBlock = null;

        foreach ($resultPage->getLayout()->getAllBlocks() as $cmsBlock) {
            if ($cmsBlock instanceof \Magento\Cms\Block\Widget\Block) {
                $cmsBlock->toHtml();
                foreach ($resultPage->getLayout()->getAllBlocks() as $block) {
                    if ($block->getData('use_improved_navigation') == 1 &&
                        $block->getProductCollection() instanceof
                        \Magento\Catalog\Model\ResourceModel\Product\Collection) {
                        $this->cmsManager->setCmsCollection($block->getProductCollection());
                        break;
                    }
                }
                if ($this->cmsManager->isCmsPageNavigation()) {
                    break;
                }
            }
        }

        $cmsBlock->getLayout()->unsetElement('widget.products.list.pager');
        
        $responseData = $this->getAjaxResponseData($resultPage);

        if ($cmsBlock) {
            $responseData['cmsPageData'] = $cmsBlock->toHtml();
            $cmsBlock->getLayout()->unsetElement('widget.products.list.pager');
        }

        $response = $this->prepareResponse($responseData);

        return $response;
    }
}
