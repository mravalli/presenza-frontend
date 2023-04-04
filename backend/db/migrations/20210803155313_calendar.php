<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Calendar extends AbstractMigration
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
        $tab = $this->table('calendar');
        $tab->addColumn('day', 'date')
            ->addColumn('office_id', 'integer')
            ->addColumn('employee_id', 'integer')
            ->addColumn('hours', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('disease', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('justification_code', 'string', ['limit' => 3, 'null' => true])
            ->addForeignKey('office_id', 'offices', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->addTimestamps()
            ->create();
    }
}
