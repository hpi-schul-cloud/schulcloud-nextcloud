<?php
namespace OCA\Schulcloud\Tests\Integration\Listeners;

use PHPUnit\Framework\TestCase;

use OCP\User\Events\PostLoginEvent;
use OCP\IGroupManager;
use OCP\IGroup;
use OCP\ILogger;
use OCP\IUser;

use OCA\Schulcloud\Listeners\GroupFolderRenamingListener;
use OCA\Schulcloud\Helper\CurlRequest;
use OCA\Schulcloud\Db\GroupFolderMapper;
use OCA\Schulcloud\Db\GroupFolderConnection;

class GroupFolderRenamingTest extends TestCase {

    const testFolderId = 10;
    const testGroupId = "100";
    const testGroupName = "Test Folder";

    private $testGroup;
    private $testUser;

    /** @var GroupFolderMapper|\PHPUnit\Framework\MockObject\MockObject */
	private $mapper;

    /** @var IGroupManager|\PHPUnit\Framework\MockObject\MockObject */
	private $groupManager;

    /** @var ILogger|\PHPUnit\Framework\MockObject\MockObject */
	private $logger;

    /** @var CurlRequest|\PHPUnit\Framework\MockObject\MockObject */
	private $curl;

	/** @var GroupFolderRenamingListener */
	private $listener;

    public function setUp(): void {
        $this->testGroup = $this->createMock(IGroup::class);
        $this->testGroup->method('getGID')
            ->will($this->returnValue(self::testGroupId));
        $this->testGroup->method('getDisplayName')
            ->will($this->returnValue(self::testGroupName));

        $this->testUser = $this->createMock(IUser::class);

        $this->mapper = $this->createMock(GroupFolderMapper::class);
        $this->groupManager = $this->createMock(IGroupManager::class);
        $this->logger = $this->createMock(ILogger::class);
        $this->curl = $this->createMock(CurlRequest::class);

        $this->listener = new GroupFolderRenamingListener($this->mapper, $this->groupManager, $this->logger, $this->curl);
    }

    public function testGroupFoldersRenamingSuccessfull() {
        $entity = $this->getMockBuilder(GroupFolderConnection::class)
            ->addMethods(['getGid', 'getFid'])
            ->getMock();
        $entity->method('getGid')
            ->will($this->returnValue(self::testGroupId));
        $entity->method('getFid')
            ->will($this->returnValue(self::testFolderId));

        $this->groupManager->method('getUserGroups')
            ->with($this->equalTo($this->testUser))
            ->will($this->returnValue(array($this->testGroup)));

        $this->mapper->method('findByGroupID')
            ->with($this->equalTo(self::testGroupId))
            ->will($this->returnValue(array($entity)));

        $folderName = self::testGroupName . ' (' . self::testGroupId . ')';
        $this->curl->expects($this->once())
            ->method('send')
            ->with($this->any(), $this->any(), array('mountpoint'=>$folderName));
        
        $this->logger->expects($this->once())
            ->method('info');
            
        $event = new PostLoginEvent($this->testUser, "test_user_name", "test_password", true);
        $this->listener->handle($event);
    }
}