<?php
namespace OCA\Schulcloud\Tests\Unit\Folder;

use PHPUnit\Framework\TestCase;

use OCP\IUser;
use OCP\IGroup;
use OCP\ILogger;

use OCA\GroupFolders\Folder\FolderManager;

use OCA\Schulcloud\Helper\GroupFolderName;
use OCA\Schulcloud\Folder\GroupFolderManager;

class GroupFolderManagerTest extends TestCase {

    const testFolderId = 10;
    const testGroupId = "100";
    const testGroupName = "Test Folder";

    private $testGroup;
    private $testUser;
    private $testFolderName;

    /** @var FolderManager from Group Folders*/
    private $folderManager;

	/** @var GroupFolderManager */
	private $gfManager;

    public function setUp(): void {
        $this->testGroup = $this->createMock(IGroup::class);
        $this->testGroup->method('getGID')
            ->will($this->returnValue(self::testGroupId));
        $this->testGroup->method('getDisplayName')
            ->will($this->returnValue(self::testGroupName));

        $this->testUser = $this->createMock(IUser::class);

        $this->testFolderName = GroupFolderName::make(self::testGroupId, self::testGroupName);

        $this->logger = $this->createMock(ILogger::class);

        $this->folderManager = $this->createMock(FolderManager::class);

        $this->gfManager = new GroupFolderManager($this->folderManager, $this->logger);
    }

    public function testGroupFolderCreationSuccessfull() {
        $this->folderManager->expects($this->once())
            ->method('createFolder')
            ->with($this->equalTo($this->testFolderName))
            ->will($this->returnValue(self::testFolderId));

        $this->folderManager->expects($this->once())
            ->method('addApplicableGroup')
            ->with($this->equalTo(self::testFolderId), $this->equalTo(self::testGroupId));
        
        $this->logger->expects($this->once())
            ->method('info');

        $this->gfManager->createFolderForGroup($this->testGroup);
    }

    public function testGroupFoldersRenamingSuccessfull() {
        $folderList = array(array("folder_id" => self::testFolderId));
        $groups = array(array("gid" => self::testGroupId, "displayname" => self::testGroupName));

        $this->folderManager->expects($this->once())
            ->method('getFoldersForUser')
            ->with($this->testUser)
            ->will($this->returnValue($folderList));

        $this->folderManager->expects($this->once())
            ->method('searchGroups')
            ->with(self::testFolderId)
            ->will($this->returnValue($groups));

        $this->folderManager->expects($this->once())
            ->method('renameFolder')
            ->with(self::testFolderId, $this->testFolderName);
        
        $this->logger->expects($this->once())
            ->method('info');
            
        $this->gfManager->renameFoldersOfUser($this->testUser);
    }
}