<?php
namespace OCA\Schulcloud\Listeners;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\PostLoginEvent;
use OCP\ILogger;

use OCA\Schulcloud\Folder\GroupFolderManager;

class GroupFolderRenamingListener implements IEventListener {

    /** @var GroupFolderManager */
	private $manager;

    /** @var ILogger */
    private $logger;

	public function __construct(GroupFolderManager $manager, ILogger $logger) {
		$this->manager = $manager;
        $this->logger = $logger;
	}

    public function handle(Event $event): void {
        if (!($event instanceof PostLoginEvent)) {
            $this->logger->error(self::class . ' is not registered correctly');
			return;
		}

        $this->manager->renameFoldersOfUser($event->getUser());
    }
}