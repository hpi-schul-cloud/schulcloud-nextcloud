<?php

declare(strict_types=1);

namespace OCA\Schulcloud\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCA\Schulcloud\Db\GroupFolderMapper;
use OCA\Schulcloud\Listener\GroupFolderCreationListener;
use OCA\Schulcloud\Listener\GroupFolderRenamingListener;

class Application extends App implements IBootstrap {

   const APP_ID = 'schulcloud';

   public function __construct(array $urlParams = []) {
      parent::__construct(self::APP_ID, $urlParams);
   }

   public function register(IRegistrationContext $context): void {
      // Register Services
      $context->registerService(GroupFolderMapper::class, function(ContainerInterface $c){
         return new GroupFolderMapper(
            $c->get('ServerContainer')->getDatabaseConnection()
         );
      });

      // Register Events
      $context->registerEventListener(GroupCreatedEvent::class, GroupFolderCreationListener::class);
      $context->registerEventListener(PostLoginEvent::class, GroupFoldersRenamingListener::class);
   }

   public function boot(IBootContext $context): void {
      
   }
}
