<?php
namespace OCA\Schulcloud\Tests\Integration\Db;

use PHPUnit\Framework\TestCase;

use OCP\IDBConnection;

use OCA\Schulcloud\AppInfo\Application;
use OCA\Schulcloud\Db\GroupFolderMapper;
use OCA\Schulcloud\Db\GroupFolderConnection;

class GroupFolderMapperTest extends TestCase {

    const testFolderId = 32;
    const testGroupId = "1231";

    /** @var GroupFolderConnection */
	private $testEntity;

    /** @var IDBConnection */
	private $db;

	/** @var GroupFolderMapper */
	private $mapper;

    public function setUp(): void {
        $this->app = new Application();
        $this->container = $this->app->getContainer();

        $this->db = $this->container->get(IDBConnection::class);

        $this->testEntity = new GroupFolderConnection();
        $this->testEntity->setGid(self::testGroupId);
        $this->testEntity->setFid(self::testFolderId);

        $this->mapper = new GroupFolderMapper($this->db);
    }

    public function testCrdByGroupId() {
        $this->mapper->createGroupFolderConnection(self::testGroupId, self::testFolderId);

        $entities = $this->mapper->findByGroupID(self::testGroupId);

        $this->mapper->deleteByGroupID(self::testGroupId);

        $entityNotFound = $this->mapper->findByGroupID(self::testGroupId);

        $this->assertEquals($this->testEntity->getGid(), $entities[0]->getGid());
        $this->assertEquals($this->testEntity->getFid(), $entities[0]->getFid());
        $this->assertTrue(count($entityNotFound) == 0);
    }

    public function testCrdByFolderId() {
        $this->mapper->createGroupFolderConnection(self::testGroupId, self::testFolderId);

        $entities = $this->mapper->findByFolderID(self::testFolderId);

        $this->mapper->deleteByFolderID(self::testFolderId);

        $entityNotFound = $this->mapper->findByFolderID(self::testFolderId);

        $this->assertEquals($this->testEntity->getGid(), $entities[0]->getGid());
        $this->assertEquals($this->testEntity->getFid(), $entities[0]->getFid());
        $this->assertTrue(count($entityNotFound) == 0);
    }
}