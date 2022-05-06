<?php
namespace OCA\Schulcloud\Listeners;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\PostLoginEvent;
use OCP\IGroupManager;
use OCP\ILogger;

use OCA\Schulcloud\Helper\CurlRequest;
use OCA\Schulcloud\Db\GroupFolderMapper;

class GroupFolderRenamingListener implements IEventListener {

    /** @var GroupFolderMapper */
	private $mapper;

    /** @var IGroupManager */
	private $groupManager;

    /** @var ILogger */
    private $logger;

    /** @var CurlRequest */
    private $curl;

	public function __construct(GroupFolderMapper $mapper, IGroupManager $groupManager, ILogger $logger, CurlRequest $curl) {
        $this->mapper = $mapper;
		$this->groupManager = $groupManager;
        $this->logger = $logger;
        $this->curl = $curl;
	}

    public function handle(Event $event): void {
        if (!($event instanceof PostLoginEvent)) {
            $this->logger->error(self::class . ' is not registered correctly');
			return;
		}

        $groups = $this->groupManager->getUserGroups($event->getUser());

        foreach($groups as $group) {
            $groupId = $group->getGID();
            $connections = $this->mapper->findByGroupID($groupId);
            $newFolderName = self::makeFolderName($groupId, $group->getDisplayName());

            if(count($connections) == 1) {
                $folderId = $connections[0]->getFid();

                $result = $this->curl->send('POST', CurlRequest::API_URL . "index.php/apps/groupfolders/folders/$folderId/mountpoint", array('mountpoint'=>$newFolderName));

                $this->logger->info("Renamed group folder: [id: $folderId, name: $newFolderName]");
            } else {
                $this->logger->notice("Cannot determine Group Folder to rename for group [name: $newFolderName], since the group has multiple registered Group Folders");
            }
        }
    }

    private static function makeFolderName($groupId, $groupName) {
        return "$groupName ($groupId)";
    }
}