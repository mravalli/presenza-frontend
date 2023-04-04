<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Offices extends AbstractMigration
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
        $tab = $this->table('offices');
        $tab->addColumn('name', 'string', ['limit' => 45])
            ->addColumn('color', 'integer')
            ->addColumn('location', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('manager_id', 'integer', ['null' => true])
            ->addTimestamps()
            ->create();
    }
}
