<?php

namespace OCA\Schulcloud\Tests\Integration\Controller;

use OCA\Schulcloud\AppInfo\Application;
use OCA\Schulcloud\Db\GroupFolderConnection;
use OCA\Schulcloud\Db\GroupFolderMapper;
use OCA\Schulcloud\AppInfo\CurlRequest;

use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Group\Events\GroupCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\IGroup;
use OCP\IUser;
use OCP\ILogger;
use OCP\IGroupManager;

use PHPUnit\Framework\TestCase;

/**
 * This test shows how to make a small Integration Test. Query your class
 * directly from the container, only pass in mocks if needed and run your tests
 * against the database
 */
class AppTest extends TestCase {

    private $container;
    private $app;
    private $mapper;
    private $logger;

    const testFolderId = 10;
    const testGroupId = "100";

    private $testGroup;
    private $testUser;

    public function setUp(): void{
        parent::setUp();
        $this->app = new Application();

        $bootContext = $this->createMock(IBootContext::class);
        $this->app->boot($bootContext);

        $this->container = $this->app->getContainer();

        $this->mapper = $mapper = $this->getMockBuilder(GroupFolderMapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $logger = $this->getMockBuilder(ILogger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->registerService('GroupFolderMapper', function(ContainerInterface $c) use ($mapper) {
            return $mapper;
        });

        $this->container->registerService('ILogger', function(ContainerInterface $c) use ($logger) {
            return $logger;
        });

        $this->testGroup = $this->createMock(IGroup::class);
        $this->testGroup->method('getGID')
            ->will($this->returnValue(self::testGroupId));
        $this->testGroup->method('getDisplayName')
            ->will($this->returnValue(self::testGroupId));

        $this->testUser = $this->createMock(IUser::class);
    }

    public function testAppInstalled() {
        $appManager = $this->container->query('OCP\App\IAppManager');
        $this->assertTrue($appManager->isInstalled('schulcloud'));
    }

    public function testGroupFolderCreationSuccessfull() {
        $this->mapper->expects($this->once())
            ->method('createGroupFolderConnection')
            ->with($this->equalTo(self::testGroupId), $this->equalTo(self::testFolderId));

        // $curl = $this->getMockBuilder(CurlRequest::class)
        //     ->disableOriginalConstructor()
        //     ->getMock();

        // $curl->expects($this->exactly(2))
        //     ->method('callAPI')
        //     ->willReturnOnConsecutiveCalls(
        //         $this->returnValue('<ocs><data><id>' . self::testFolderId . '</id></data></ocs>'),
        //         $this->returnValue('<ocs>...</ocs>')
        //     );
        
        /*$this->logger->expects($this->once())
            ->method('info');*/
            
        $event = new GroupCreatedEvent($this->testGroup);



        $this->app->handleGroupFolderCreation($event);
    }

    public function testGroupFolderCreationApiNotResponding() {  
        /*$this->logger->expects($this->once())
            ->method('error');*/

        $event = new GroupCreatedEvent($this->testGroup);
        

        
        $this->app->handleGroupFolderCreation($event);
    }

    public function testGroupFoldersRenamingSuccessfull() {
        $event = new PostLoginEvent($this->testUser, "test_user_name", "test_password", true);

        $entity = $this->getMockBuilder(GroupFolderConnection::class)
            ->addMethods(['getGid', 'getFid'])
            ->getMock();

        $entity->method('getGid')
            ->will($this->returnValue(self::testGroupId));
        $entity->method('getFid')
            ->will($this->returnValue(self::testFolderId));

        $groupManager = $this->getMockBuilder(IGroupManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $groupManager->method('getUserGroups')
            ->with($this->equalTo($this->testUser))
            ->will($this->returnValue(array($this->testGroup)));

        $this->mapper->method('findByGroupID')
            ->with($this->equalTo(self::testGroupId))
            ->will($this->returnValue(array($entity)));

        $this->logger->expects($this->once())
            ->method('info');



        $this->app->handleGroupFoldersRenaming($event);
    }
}
