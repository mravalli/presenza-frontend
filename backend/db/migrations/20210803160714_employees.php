<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Employees extends AbstractMigration
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
        $tab = $this->table('employees');
        $tab->addColumn('firstname', 'string', ['limit' => 64])
            ->addColumn('lastname', 'string', ['limit' => 64])
            ->addColumn('address', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('cap', 'string', ['limit' => 5, 'null' => true])
            ->addColumn('city', 'string', ['limit' => 45, 'null' => true])
            ->addColumn('cf', 'string', ['limit' => 16])
            ->addColumn('phone', 'string', ['limit' => 15, 'null' => true])
            ->addColumn('mobile', 'string', ['limit' => 15, 'null' => true])
            ->addColumn('birthday', 'date')
            ->addColumn('notes', 'mediumblob', ['null' => true])
            ->addTimestamps()
            ->create();
    }
}
