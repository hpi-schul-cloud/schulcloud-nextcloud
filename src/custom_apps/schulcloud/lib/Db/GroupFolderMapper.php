<?php
namespace OCA\Schulcloud\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db;
use OCP\AppFramework\Db\QBMapper;
use OCP\AppFramework\Db\DoesNotExistException;

use OCA\Schulcloud\Db\GroupFolderConnection;

class GroupFolderMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'schulcloud_group_folder', GroupFolderConnection::class);
    }

    /**
     * @param string $id
     * @param string $field
     * @return GroupFolderConnection|null
     */
    private function find($id, $field) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq($field, $qb->createNamedParameter($id))
            );

        try {
            return $this->findEntities($qb);
        } catch(DoesNotExistException $e) {
            return array();
        }
    }

    public function findByGroupID($gid) {
        return $this->find($gid, 'gid');
    }

    public function findByFolderID($fid) {
        return $this->find($fid, 'fid');
    }

    /**
     * @param string $gid Nextcloud group id
     * @param string $fid GroupFolders folder id
     */
    public function createGroupFolderConnection($gid, $fid)
    {
        $connection = new GroupFolderConnection();
        $connection->setGid($gid);
        $connection->setFid($fid);
        $this->insert($connection);
    }

    /**
     * @param string $id
     * @param string $field
     */
    private function deleteConnections($id, $field)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where(
                $qb->expr()->eq($field, $qb->createNamedParameter($id))
            );

        if (method_exists($qb, 'executeStatement')) {
            $qb->executeStatement();
        } else {
            $qb->execute();
        }
    }

    public function deleteByGroupID($gid) {
        return $this->deleteConnections($gid, 'gid');
    }

    public function deleteByFolderID($fid) {
        return $this->deleteConnections($fid, 'fid');
    }
}
