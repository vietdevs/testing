<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Order;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use MageWorx\OrderEditor\Model\Order\Item as Entity;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OrderEditor\Model\Order\ItemFactory as EntityFactory;
use MageWorx\OrderEditor\Api\OrderItemRepositoryInterface as RepositoryInterface;
use MageWorx\OrderEditor\Model\ResourceModel\Order\Item as EntityResource;
use MageWorx\OrderEditor\Model\ResourceModel\Order\Item\CollectionFactory as EntityCollectionFactory;
use MageWorx\OrderEditor\Model\ResourceModel\Order\Item\Collection as EntityCollection;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class OrderItemRepository
 */
class OrderItemRepository implements RepositoryInterface
{
    /**
     * @var EntityResource
     */
    protected $resource;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var EntityCollectionFactory
     */
    protected $entityCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param EntityResource $resource
     * @param EntityFactory $entityFactory
     * @param EntityCollectionFactory $entityCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EntityResource $resource,
        EntityFactory $entityFactory,
        EntityCollectionFactory $entityCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource                = $resource;
        $this->entityFactory           = $entityFactory;
        $this->entityCollectionFactory = $entityCollectionFactory;
        $this->searchResultsFactory    = $searchResultsFactory;
        $this->dataObjectHelper        = $dataObjectHelper;
        $this->dataObjectProcessor     = $dataObjectProcessor;
        $this->storeManager            = $storeManager;
    }

    /**
     * Save Entity data
     *
     * @param OrderItemInterface $entity
     * @return OrderItemInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderItemInterface $entity): OrderItemInterface
    {
        try {
            /** @var Entity $entity */
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the entity: %1',
                    $exception->getMessage()
                )
            );
        }

        return $entity;
    }

    /**
     * Load Entity data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @param bool $returnRawObjects
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria, $returnRawObjects = false): SearchResultsInterface
    {
        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var EntityCollection $collection */
        $collection = $this->entityCollectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter(
                    $filter->getField(),
                    [
                        $condition => $filter->getValue()
                    ]
                );
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $entities = [];
        /** @var Entity|AbstractModel $entityModel */
        foreach ($collection as $entityModel) {
            if ($returnRawObjects) {
                $entities[] = $entityModel;
            } else {
                /** @var Entity $entityData */
                $entityData = $this->entityFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $entityData,
                    $entityModel->getData(),
                    static::ENTITY_DATA_INTERFACE
                );
                $entities[] = $this->dataObjectProcessor->buildOutputDataArray(
                    $entityData,
                    static::ENTITY_DATA_INTERFACE
                );
            }
        }
        $searchResults->setItems($entities);

        return $searchResults;
    }

    /**
     * Delete Entity by given Entity Identity
     *
     * @param string $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }

    /**
     * Delete Entity
     *
     * @param OrderItemInterface $entity
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(OrderItemInterface $entity): bool
    {
        try {
            /** @var Entity $entity */
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the entity: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * Load Entity data by given Entity Identity
     *
     * @param int $entityId
     * @return Entity
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): Entity
    {
        /** @var Entity $entity */
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Entity with id "%1" does not exist.', $entityId));
        }

        return $entity;
    }

    /**
     * Get empty Entity
     *
     * @return Entity
     */
    public function getEmptyEntity(): Entity
    {
        /** @var Entity $entity */
        $entity = $this->entityFactory->create();

        return $entity;
    }
}
