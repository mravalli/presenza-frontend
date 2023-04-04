<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OfficeEmployees extends AbstractMigration
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
        $tab = $this->table('office_employees');
        $tab->addColumn('office_id', 'integer')
            ->addColumn('employee_id', 'integer')
            ->addForeignKey('office_id', 'offices', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addIndex(['office_id', 'employee_id'], [
                'unique' => true,
                'name' => 'idx_employee_office',
            ])
            ->create();
    }
}
