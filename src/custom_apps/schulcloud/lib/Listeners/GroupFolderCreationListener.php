<?php
namespace OCA\Schulcloud\Listeners;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Group\Events\GroupCreatedEvent;
use OCP\ILogger;

use OCA\Schulcloud\Helper\CurlRequest;
use OCA\Schulcloud\Helper\GroupFolderName;
use OCA\Schulcloud\Db\GroupFolderMapper;

class GroupFolderCreationListener implements IEventListener {

    /** @var GroupFolderMapper */
	private $mapper;

    /** @var ILogger */
    private $logger;

    /** @var CurlRequest */
    private $curl;

	public function __construct(GroupFolderMapper $mapper, ILogger $logger, CurlRequest $curl) {
		$this->mapper = $mapper;
        $this->logger = $logger;
        $this->curl = $curl;
	}

    public function handle(Event $event): void {
        if (!($event instanceof GroupCreatedEvent)) {
            $this->logger->error(self::class . ' is not registered correctly');
			return;
		}

        $affectedGroup = $event->getGroup();
        $groupId = $affectedGroup->getGID();
        $folderName = GroupFolderName::make($groupId, $affectedGroup->getDisplayName());

        try {
            // Create new folder
            $result = $this->curl->send('POST', CurlRequest::API_URL . 'index.php/apps/groupfolders/folders', array('mountpoint'=>$folderName));

            $xml = simplexml_load_string($result);
            $folderId = (string)$xml->xpath('/ocs/data/id')[0];

            // Assign the new folder to a group
            $result = $this->curl->send('POST', CurlRequest::API_URL . "index.php/apps/groupfolders/folders/$folderId/groups", array('group'=>$groupId));

            // Write new connection to database
            $this->mapper->createGroupFolderConnection($groupId, $folderId);

            $this->logger->info("Successfully created new group folder: [id: $folderId, name: $folderName]");
        } catch(\Exception $e) {
            $this->logger->error("Failed to create group folder:\n" . $e->getMessage());
        }
    }
}