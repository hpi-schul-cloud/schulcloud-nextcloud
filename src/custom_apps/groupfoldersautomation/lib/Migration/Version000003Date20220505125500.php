<?php
namespace OCA\GroupFoldersAutomation\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000003Date20220505125500 extends SimpleMigrationStep {

    /**
    * @param IOutput $output
    * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
    * @param array $options
    * @return null|ISchemaWrapper
    */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('schulcloud_group_folder')) {
            $table = $schema->createTable('schulcloud_group_folder');
            $table->addColumn('id', 'bigint', [
                'autoincrement' => true,
                'notnull' => true,
                'length' => 6
            ]);
            $table->addColumn('gid', 'string', [
                'notnull' => true,
                'length' => 64
            ]);
            $table->addColumn('fid', 'bigint', [
                'notnull' => true,
                'length' => 6
            ]);

            $table->setPrimaryKey(['id']);
        }
        return $schema;
    }
}