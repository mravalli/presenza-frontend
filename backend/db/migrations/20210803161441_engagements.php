<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Engagements extends AbstractMigration
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
        $tab = $this->table('engagements');
        $tab->addColumn('begin', 'date')
            ->addColumn('end', 'date')
            ->addColumn('employee_id', 'integer')
            ->addColumn('hours_week_id', 'integer')
            ->addForeignKey('employee_id', 'employees', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->addForeignKey('hours_week_id', 'hours_week', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->addTimestamps()
            ->create();
    }
}
