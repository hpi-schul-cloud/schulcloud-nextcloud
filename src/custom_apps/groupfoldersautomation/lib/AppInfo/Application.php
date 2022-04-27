<?php

declare(strict_types=1);

namespace OCA\GroupFoldersAutomation\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Group\Events\GroupCreatedEvent;

use Psr\Log\LoggerInterface;

class Application extends App {
   
   private const API_URL = "http://localhost:80/";
   
   /** @var string */
   private $adminUser;

   /** @var string */
   private $adminPassword;

   /** @var LoggerInterface */
   private $logger;

   public function __construct() {
      parent::__construct('groupfoldersautomation');

      //Load environment variables
      $this->adminUser = getenv('NEXTCLOUD_ADMIN_USER');
      $this->adminPassword = getenv('NEXTCLOUD_ADMIN_PASSWORD');

      //Get logger
      $this->logger = $this->getContainer()->get(LoggerInterface::class);

      //Get dispatcher for event registration
      $dispatcher = $this->getContainer()->query(IEventDispatcher::class);

      //Handle GroupCreatedEvent for automatic folder creation
      $dispatcher->addListener(GroupCreatedEvent::class, function(GroupCreatedEvent $event) {
         $affectedGroup = $event->getGroup();
         $folderName = $affectedGroup->getDisplayName();
         $groupId = $affectedGroup->getGID();

         try {
            //Create new folder
            $result = $this->callAPI('POST', self::API_URL . 'index.php/apps/groupfolders/folders', array('mountpoint'=>$folderName));

            $xml = simplexml_load_string($result);
            $folderId = $xml->xpath('/ocs/data/id')[0];

            //Assign the new folder to a group
            $result = $this->callAPI('POST', self::API_URL . "index.php/apps/groupfolders/folders/$folderId/groups", array('group'=>$groupId));

            $this->logger->info("Successfully created new group folder: [id: $folderId, name: $folderName]");
         } catch(Exception $e) {
            $this->logger->error("Failed to create group folder:\n" . $e->getMessage() . "\n");
         }
      });
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
   private function callAPI($method, $url, $data = false): string {
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