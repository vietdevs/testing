<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Model\Source;

/**
 * Class RelNofollow
 * @package Amasty\ShopbySeo\Model\Source
 */
class RelNofollow implements \Magento\Framework\Option\ArrayInterface
{
    const MODE_NO = 0;
    const MODE_AUTO = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_getOptions() as $optionValue => $optionLabel) {
            $options[] = ['value'=>$optionValue, 'label'=>$optionLabel];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_getOptions();
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $options = [
            self::MODE_NO => __('No'),
            self::MODE_AUTO => __('Auto'),
        ];

        return $options;
    }
}
