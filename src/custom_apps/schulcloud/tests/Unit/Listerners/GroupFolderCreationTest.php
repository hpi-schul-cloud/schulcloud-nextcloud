<?php
namespace OCA\Schulcloud\Tests\Integration\Listeners;

use PHPUnit\Framework\TestCase;

use OCP\Group\Events\GroupCreatedEvent;
use OCP\IGroup;
use OCP\ILogger;

use OCA\Schulcloud\Listeners\GroupFolderCreationListener;
use OCA\Schulcloud\Helper\CurlRequest;
use OCA\Schulcloud\Db\GroupFolderMapper;

class GroupFolderCreationTest extends TestCase {

    const testFolderId = 10;
    const testGroupId = "100";

    private $testGroup;

    /** @var GroupFolderMapper|\PHPUnit\Framework\MockObject\MockObject */
	private $mapper;

    /** @var ILogger|\PHPUnit\Framework\MockObject\MockObject */
	private $logger;

    /** @var CurlRequest|\PHPUnit\Framework\MockObject\MockObject */
	private $curl;

	/** @var GroupFolderCreationListener */
	private $listener;

    public function setUp(): void {
        $this->testGroup = $this->createMock(IGroup::class);
        $this->testGroup->method('getGID')
            ->will($this->returnValue(self::testGroupId));
        $this->testGroup->method('getDisplayName')
            ->will($this->returnValue(self::testGroupId));

        $this->mapper = $this->createMock(GroupFolderMapper::class);
        $this->logger = $this->createMock(ILogger::class);
        $this->curl = $this->createMock(CurlRequest::class);

        $this->listener = new GroupFolderCreationListener($this->mapper, $this->logger, $this->curl);
    }

    public function testGroupFolderCreationSuccessfull() {
        $this->mapper->expects($this->once())
            ->method('createGroupFolderConnection')
            ->with($this->equalTo(self::testGroupId), $this->equalTo(self::testFolderId));

        $this->curl->expects($this->exactly(2))
            ->method('send')
            ->willReturnOnConsecutiveCalls(
                '<?xml version="1.0"?>
                <ocs>
                 <meta>
                  <status>ok</status>
                  <statuscode>100</statuscode>
                  <message>OK</message>
                  <totalitems></totalitems>
                  <itemsperpage></itemsperpage>
                 </meta>
                 <data>
                  <id>' . self::testFolderId . '</id>
                  <groups/>
                 </data>
                </ocs>',
                '<?xml version="1.0"?>
                <ocs>
                    <meta>
                        <status>ok</status>
                        <statuscode>100</statuscode>
                        <message>OK</message>
                        <totalitems></totalitems>
                        <itemsperpage></itemsperpage>
                    </meta>
                    <data>
                        <success>1</success>
                    </data>
                </ocs>'
            );
        
        $this->logger->expects($this->once())
            ->method('info');
            
        $event = new GroupCreatedEvent($this->testGroup);
        $this->listener->handle($event);
    }

    public function testGroupFolderCreationApiNotResponding() { 
        $this->curl->expects($this->once())
            ->method('send')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())
            ->method('error');

        $event = new GroupCreatedEvent($this->testGroup);
        $this->listener->handle($event);
    }
}