<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class Users extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $tab = $this->table('users');
        $tab->addColumn('firstname', 'string', ['limit' => 64])
            ->addColumn('lastname', 'string', ['limit' => 64])
            ->addColumn('username', 'string', ['limit' => 64])
            ->addColumn('password_hash', 'string', ['limit' => 64])
            ->addColumn('email', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 1])
            ->addColumn('role', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 9])
            ->addColumn('primary_key', 'string', ['limit' => 40, 'null' => true])
            ->addColumn('secondary_key', 'string', ['limit' => 40, 'null' => true])
            ->addTimestamps()
            ->create();
    }
}
