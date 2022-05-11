<?php
namespace OCA\Schulcloud\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Group\Events\GroupCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\IContainer;
use OCP\ILogger;

use OCA\GroupFolders\Folder\FolderManager;

use OCA\Schulcloud\AppInfo\DependencyManager;
use OCA\Schulcloud\Listeners\GroupFolderCreationListener;
use OCA\Schulcloud\Listeners\GroupFolderRenamingListener;
use OCA\Schulcloud\Folder\GroupFolderManager;

class Application extends App implements IBootstrap {

   const APP_ID = 'schulcloud';

   public function __construct(array $urlParams = []) {
      parent::__construct(self::APP_ID, $urlParams);

      //throws an exception if dependencies are missing
      DependencyManager::checkDependencies();
   }

   public function register(IRegistrationContext $context): void {
      // Register Services
      $context->registerService(GroupFolderManager::class, function(IContainer $c){
         return new GroupFolderManager(
            $c->get(FolderManager::class),
            $c->get(ILogger::class)
         );
      });

      // Register Events
      $context->registerEventListener(GroupCreatedEvent::class, GroupFolderCreationListener::class);
      $context->registerEventListener(PostLoginEvent::class, GroupFolderRenamingListener::class);
   }

   public function boot(IBootContext $context): void {

   }
}
