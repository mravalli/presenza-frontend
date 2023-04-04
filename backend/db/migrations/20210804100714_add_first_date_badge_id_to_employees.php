<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFirstDateBadgeIdToEmployees extends AbstractMigration
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
        $tab->addColumn('badge_id', 'string', ['after' => 'id', 'limit' => 15, 'null' => true])
            ->addColumn('first_engagement', 'date', ['null' => true, 'after' => 'email'])
            ->update();
    }
}
