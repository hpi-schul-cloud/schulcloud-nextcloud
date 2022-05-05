<?php
namespace OCA\Schulcloud\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db;
use OCP\AppFramework\Db\QBMapper;

class GroupFolderMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'schulcloud_group_folder', GroupFolderConnection::class);
    }

    /**
     * @param string $id
     * @param string $field
     * @return GroupFolderConnection|null
     */
    private function find(string $id, string $field) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq($field, $qb->createNamedParameter($id))
            );

        try {
            return $this->findEntities($qb);
        } catch(Db\DoesNotExistException $e) {
            return null;
        }
    }

    public function findByGroupID(string $gid) {
        return $this->find($gid, 'gid');
    }

    public function findByFolderID(string $fid) {
        return $this->find($fid, 'fid');
    }

    /**
     * @param string $gid Nextcloud group id
     * @param string $fid GroupFolders folder id
     */
    public function createGroupFolderConnection(string $gid, string $fid)
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
    private function deleteConnections(string $id, string $field)
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

    public function deleteByGroupID(string $gid) {
        return $this->deleteConnections($gid, 'gid');
    }

    public function deleteByFolderID(string $fid) {
        return $this->deleteConnections($fid, 'fid');
    }
}
