<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\DataProvider;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory;
use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Model\Icon\GetIconForFile;
use Magento\Framework\App\RequestInterface;

class InsertListing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\ProductAttachment\Model\File\ResourceModel\Collection
     */
    protected $collection;

    /**
     * @var GetIconForFile
     */
    private $getIconForFile;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var int
     */
    private $totalRecordPlus;

    public function __construct(
        CollectionFactory $collectionFactory,
        GetIconForFile $getIconForFile,
        RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->collection->addInsertListingFileData($request->getParam('store_id', 0));
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->getIconForFile = $getIconForFile;
        $this->request = $request;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == FileInterface::FILE_ID && $filter->getConditionType() == 'nin') {
            if ($filter->getValue()) {
                $this->totalRecordPlus = count($filter->getValue());
            }
        }
        parent::addFilter($filter);
    }

    public function getData()
    {
        $data = parent::getData();
        if (!empty($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (!empty($item[FileInterface::EXTENSION])) {
                    $item['icon_src'] = $this->getIconForFile->byFileExtension(
                        $item[FileInterface::EXTENSION]
                    );
                }
                $item['show_file_id'] = $item[FileInterface::FILE_ID];
                $item[FileInterface::SIZE] = $this->getReadableFileSize((int)$item[FileInterface::SIZE]);
                foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
                    $item[$field . '_use_defaults'] = "1";
                }
            }
        }
        if ($this->totalRecordPlus) {
            $data['totalRecords'] += $this->totalRecordPlus;
        }
        return $data;
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    public function getReadableFileSize($bytes = 0)
    {
        $size   = ['B', 'kB', 'MB', 'GB', 'TB'];
        $factor = (int)floor((strlen($bytes) - 1) / 3);
        if (isset($size[$factor])) {
            $bytes = sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
        }

        return $bytes;
    }
}
