<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Element119\ResetBookmarkUi\Controller\Adminhtml\Bookmarks;

use Element119\ResetBookmarkUi\Model\BookmarkUiResetter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Reset extends Action
{
    const ADMIN_RESOURCE = BookmarkUiResetter::RESET_BOOKMARKS_ACL_RESOURCE;
    const ADMIN_ROUTE = 'e119_resetbookmarkui/bookmarks/reset';

    /** @var BookmarkUiResetter */
    private BookmarkUiResetter $bookmarkUiResetter;

    /** @var AdminSession */
    private AdminSession $adminSession;

    /** @var RedirectFactory */
    private RedirectFactory $redirectFactory;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param BookmarkUiResetter $bookmarkUiResetter
     * @param AdminSession $adminSession
     * @param RedirectFactory $redirectFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        BookmarkUiResetter $bookmarkUiResetter,
        AdminSession $adminSession,
        RedirectFactory $redirectFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->bookmarkUiResetter = $bookmarkUiResetter;
        $this->adminSession = $adminSession;
        $this->redirectFactory = $redirectFactory;
        $this->logger = $logger;
    }

    /**
     * Clear the current admin user's UI bookmarks.
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $adminUser = $this->adminSession->getUser();

        try {
            $this->bookmarkUiResetter->clearBookmarks((int)$adminUser->getId());
            $this->messageManager->addSuccessMessage(__(
                'Successfully cleared bookmarks for admin user %1',
                $adminUser->getUserName()
            ));
        } catch (NoSuchEntityException | LocalizedException $e) {
            $errorMessage = __(
                'An error occurred trying to reset bookmarks for the admin user with ID %1',
                $adminUser->getId()
            );

            $this->messageManager->addErrorMessage($errorMessage);
            $this->logger->error("{$errorMessage}: {$e->getMessage()}");
        }

        return $this->redirectFactory->create()->setPath($this->_redirect->getRefererUrl());
    }
}
