<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Company extends AbstractMigration
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
        $tab = $this->table('company');
        $tab->addColumn('fullname', 'string', ['limit' => 150])
            ->addColumn('vat', 'string', ['limit' => 13])
            ->addColumn('cf', 'string', ['limit' => 16])
            ->addColumn('address', 'string', ['limit' => 150])
            ->addColumn('cap', 'string', ['limit' => 5])
            ->addColumn('city', 'string', ['limit' => 45])
            ->addColumn('phone', 'string', ['limit' => 15])
            ->addColumn('email', 'string', ['limit' => 150])
            ->addColumn('days_off', 'integer', ['default' => 26])
            ->addColumn('hours_leave', 'integer', ['default' => 32])
            ->addColumn('notes', 'mediumblob', ['null' => true])
            ->addTimestamps()
            ->create();
    }
}
