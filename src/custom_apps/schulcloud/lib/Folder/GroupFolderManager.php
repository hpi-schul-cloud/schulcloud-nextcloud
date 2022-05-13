<?php
namespace OCA\Schulcloud\Folder;

use OCP\ILogger;

use OCA\GroupFolders\Folder\FolderManager;

use OCA\Schulcloud\Helper\GroupFolderName;

class GroupFolderManager {

    /** @var FolderManager from Group Folders*/
    private $folderManager;

    /** @var ILogger */
    private $logger;

    public function __construct(FolderManager $folderManager, ILogger $logger) {
        $this->folderManager = $folderManager;
        $this->logger = $logger;
    }

    /**
     * Creates a group folder and associates a group with it
     * @param $group
     * @return void
     */
    public function createFolderForGroup($group) {
        $groupId = $group->getGID();
        $folderName = GroupFolderName::make($groupId, $group->getDisplayName());

        try {
            $folderId = $this->folderManager->createFolder($folderName);
            $this->folderManager->addApplicableGroup($folderId, $groupId);

            $this->logger->info("Successfully created new group folder: [id: $folderId, name: $folderName]");
        } catch(\Exception $e) {
            $this->logger->error("Failed to create group folder:\n" . $e->getMessage());
        }
    }

    /**
     * Fetches all group folders of a user and renames them according to the nextcloud group that is connected to that folder
     * @param $user
     * @return void
     */
    public function renameFoldersOfUser($user) {
        try {
            $folderList = $this->folderManager->getFoldersForUser($user);

            foreach ($folderList as $folder) {
                $folderId = $folder['folder_id'];
                
                $groups = $this->folderManager->searchGroups($folderId);
                // Assumes that the automatically generated group folders are named after the first (and only) connected group
                $group = $groups[0];
                
                $newFolderName = GroupFolderName::make($group['gid'], $group['displayname']);

                $this->folderManager->renameFolder($folderId, $newFolderName);
            }

            if(count($folderList) > 0) {
                $this->logger->info("Successfully renamed group folders for user: [id: " . $user->getUID() . ", name: " . $user->getDisplayName() . "]");
            }
        } catch(\Exception $e) {
            $this->logger->error("Failed to rename group folder:\n" . $e->getMessage());
        }
    }
}