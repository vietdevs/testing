<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Ui\Component\Listing\Column;

class TemplateCategoryFilterActions extends \MageWorx\SeoXTemplates\Ui\Component\Listing\Column\TemplateActions
{
    /**
     * Url path to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'mageworx_seoxtemplates/templatecategoryfilter/edit';

    /**
     * Url path  to delete
     *
     * @var string
     */
    const URL_PATH_DELETE = 'mageworx_seoxtemplates/templatecategoryfilter/delete';

    /**
     * Url path to test apply
     *
     * @var string
     */
    const URL_PATH_TEST_APPLY = 'mageworx_seoxtemplates/templatecategoryfilter/csv';

    /**
     * Url path to apply
     *
     * @var string
     */
    const URL_PATH_APPLY = 'mageworx_seoxtemplates/templatecategoryfilter/apply';

    /**
     * @return string
     */
    protected function getEditUrlPath()
    {
        return self::URL_PATH_EDIT;
    }

    /**
     * @return string
     */
    protected function getDeleteUrlPath()
    {
        return self::URL_PATH_DELETE;
    }

    /**
     * @return string
     */
    protected function getApplyUrlPath()
    {
        return self::URL_PATH_APPLY;
    }

    /**
     * @return string
     */
    protected function getTestApplyUrlPath()
    {
        return self::URL_PATH_TEST_APPLY;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    protected function getApplyMessage()
    {
        return __('Are you sure you want to apply the Category Filter Template "${ $.$data.name }" ? This action cannot be canceled.');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    protected function getDeleteMessage()
    {
        return __('Are you sure you want to delete the Category Filter Template "${ $.$data.name }" ?');
    }
}
