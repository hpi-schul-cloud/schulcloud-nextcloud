<?php

declare(strict_types=1);

namespace OCA\GroupFoldersAutomation\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCP\EventDispatcher\IEventDispatcher;
use OCP\Group\Events\GroupCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\IGroupManager;

use OCA\GroupFoldersAutomation\Db\GroupFolderMapper;

use Psr\Log\LoggerInterface;

class Application extends App implements IBootstrap {

   const API_URL = 'http://localhost:80/';

   /** @var string */
   private $adminUser;

   /** @var string */
   private $adminPassword;

   /** @var LoggerInterface */
   private $logger;

   public function __construct() {
      parent::__construct('groupfoldersautomation');

      // Load environment variables
      $this->adminUser = getenv('NEXTCLOUD_ADMIN_USER');
      $this->adminPassword = getenv('NEXTCLOUD_ADMIN_PASSWORD');
   }

   public function register(IRegistrationContext $context): void {
      // Register Services
      $context->registerService('GroupFolderMapper', function(ContainerInterface $c){
         return new GroupFolderMapper(
            $c->get('ServerContainer')->getDatabaseConnection()
         );
      });
   }

   public function boot(IBootContext $context): void {
      $this->logger = $this->getService(LoggerInterface::class);
      $this->groupFolderMap = $this->getService(GroupFolderMapper::class);
      
      // Register event handlers
      $dispatcher = $this->getService(IEventDispatcher::class);
      $dispatcher->addListener(GroupCreatedEvent::class, [$this, 'handleGroupFolderCreation']);
      $dispatcher->addListener(PostLoginEvent::class, [$this, 'handleGroupFoldersRenaming']);
   }

   public function handleGroupFolderCreation(GroupCreatedEvent $event) {
      $mapper = $this->getService(GroupFolderMapper::class);

      $affectedGroup = $event->getGroup();
      $groupId = $affectedGroup->getGID();
      $folderName = $affectedGroup->getDisplayName();

      try {
         // Create new folder
         $result = $this->callAPI('POST', self::API_URL . 'index.php/apps/groupfolders/folders', array('mountpoint'=>$folderName));

         $xml = simplexml_load_string($result);
         $folderId = (string)$xml->xpath('/ocs/data/id')[0];

         // Assign the new folder to a group
         $result = $this->callAPI('POST', self::API_URL . "index.php/apps/groupfolders/folders/$folderId/groups", array('group'=>$groupId));

         // Write new connection to database
         $mapper->createGroupFolderConnection($groupId, $folderId);

         $this->logger->info("Successfully created new group folder: [id: $folderId, name: $folderName]");
      } catch(Exception $e) {
         $this->logger->error("Failed to create group folder:\n" . $e->getMessage());
      }
   }

   public function handleGroupFoldersRenaming(PostLoginEvent $event) {
      $groupManager = $this->getService(IGroupManager::class);
      $mapper = $this->getService(GroupFolderMapper::class);

      $groups = $groupManager->getUserGroups($event->getUser());

      foreach($groups as $group) {
         $connections = $mapper->findByGroupID($group->getGID());
         $newFolderName = $group->getDisplayName();

         if(count($connections) == 1) {
            $folderId = $connections[0]->getFid();

            $result = $this->callAPI('POST', self::API_URL . "index.php/apps/groupfolders/folders/$folderId/mountpoint", array('mountpoint'=>$newFolderName));

            $this->logger->info("Renamed group folder: [id: $folderId, name: $newFolderName]");
         } else {
            $this->logger->notice("Cannot determine Group Folder to rename for group [name: $newFolderName], since the group has multiple registered Group Folders");
         }
      }
   }

   private function getService($className) {
      return $this->getContainer()->get($className);
   }

   /**
    * Helper function to make HTTP requests with cURL
    *
    * @param string $method HTTP request method
    * @param string $url address to send the request to
    * @param array $data HTTP message body (array('param'=>'value'))
    * @return string HTTP response data
    * @throws Exception On cURL error
    */
   private function callAPI(string $method, string $url, $data = false): string {
      $curl = curl_init();

      switch ($method){
         case 'POST':
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
               curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
         case 'PUT':
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data)
               curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
         default:
            if ($data)
               $url = sprintf('%s?%s', $url, http_build_query($data));
      }

      //Optional Authentication:
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, $this->adminUser . ':' . $this->adminPassword);

      //Options:
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
         'OCS-APIRequest: true',
         'Content-Disposition: form-data'
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

      //Execute:
      $result = curl_exec($curl);

      if(curl_errno($curl))
         throw new Exception('Curl error: '. curl_error($curl));

      curl_close($curl);
      return $result;
   }
}
