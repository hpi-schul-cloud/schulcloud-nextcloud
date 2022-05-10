<?php
namespace OCA\Schulcloud\AppInfo;

class DependencyManager {

   /**
	 * @throws Exception
	 */
   public static function checkDependencies() {
      self::checkAppDependency('sociallogin');
      self::checkAppDependency('groupfolders');

      self::checkClassDependency('OCA\GroupFolders\Folder\FolderManager');
   }

   /**
	 * @throws Exception
	 */
   public static function checkAppDependency(string $name) {
      if(!\OCP\App::isEnabled($name)) {
         throw new \Exception("App '$name' is not enabled, but it is a required dependency.");
      }
   }

   /**
	 * @throws Exception
	 */
   public static function checkClassDependency(string $class) {
      if(!class_exists($class)) {
         throw new \Exception("Could not find class '$class', but it is a required dependency.");
      }
   }
}
