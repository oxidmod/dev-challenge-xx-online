<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SheetsTable extends AbstractMigration
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
        $this->table('sheets', ['id' => false, 'primary_key' => ['sheet_id', 'cell_id']])
            ->addColumn('sheet_id', 'string', ['limit' => 255])
            ->addColumn('cell_id', 'string', ['limit' => 255])
            ->addColumn('props', 'jsonb', ['default' => '{}'])
            ->create();
    }
}
