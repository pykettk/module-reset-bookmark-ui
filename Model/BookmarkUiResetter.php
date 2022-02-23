<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Element119\ResetBookmarkUi\Model;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Api\Data\BookmarkInterface;

class BookmarkUiResetter
{
    const RESET_BOOKMARKS_ACL_RESOURCE = 'Element119_ResetBookmarkUi::reset_bookmarks';

    /** @var SearchCriteriaBuilderFactory */
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    /** @var BookmarkRepositoryInterface */
    private BookmarkRepositoryInterface $bookmarkRepository;

    /**
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param BookmarkRepositoryInterface $bookmarkRepository
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        BookmarkRepositoryInterface $bookmarkRepository
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * Clear the UI bookmarks for the admin user with a given ID.
     *
     * @param int $userId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function clearBookmarks(int $userId): void
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter(BookmarkInterface::USER_ID, $userId, 'eq')->create();
        $bookmarks = $this->bookmarkRepository->getList($searchCriteria)->getItems();

        foreach ($bookmarks as $bookmark) {
            $this->bookmarkRepository->deleteById($bookmark->getId());
        }
    }
}
