<?php
namespace OCA\GroupFoldersAutomation\Db;
 
use OCP\AppFramework\Db\Entity;
 
class GroupFolderConnection extends Entity
{
    protected $gid;
    protected $fid;

    /*public function __construct() {
        $this->addType('gid', 'string');
        $this->addType('fid', 'string');
    }*/
}