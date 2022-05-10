<?php
namespace OCA\Schulcloud\Listeners;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Group\Events\GroupCreatedEvent;
use OCP\ILogger;

use OCA\Schulcloud\Folder\GroupFolderManager;

class GroupFolderCreationListener implements IEventListener {

    /** @var GroupFolderManager */
	private $manager;

    /** @var ILogger */
    private $logger;

	public function __construct(GroupFolderManager $manager, ILogger $logger) {
		$this->manager = $manager;
        $this->logger = $logger;
	}

    public function handle(Event $event): void {
        if (!($event instanceof GroupCreatedEvent)) {
            $this->logger->error(self::class . ' is not registered correctly');
			return;
		}

        $this->manager->createFolderForGroup($event->getGroup());
    }
}