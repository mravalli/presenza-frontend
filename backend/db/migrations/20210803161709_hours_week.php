<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class HoursWeek extends AbstractMigration
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
        $tab = $this->table('hours_week');
        $tab->addColumn('type', 'enum', ['values' => ['FT', 'PT'], 'default' => 'FT'])
            ->addColumn('days', 'integer')
            ->addColumn('mon', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('tue', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('wed', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('thu', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('fri', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('sat', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addColumn('sun', 'decimal', ['precision' => 3, 'scale' => 2])
            ->addTimestamps()
            ->create();
    }
}
