<?php

use Phinx\Migration\AbstractMigration;

class ATK4Homepage extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {

        $this->table('message')
            ->addColumn('text', 'text',['null'=>false])
            ->addColumn('from_id', 'integer',['limit'=>'11','null'=>true])
            ->addColumn('to_id', 'integer',['limit'=>'11','null'=>false])
            ->addColumn('from_type', 'string',['limit'=>'255','null'=>false])
            ->addColumn('to_type', 'string',['limit'=>'255','null'=>false])
            ->addColumn('from_is_deleted', 'integer',['null'=>false,'default'=>'0'])
            ->addColumn('to_is_deleted', 'integer',['null'=>false,'default'=>'0'])
            ->addColumn('is_read', 'integer',['null'=>false,'default'=>'0'])
            ->addColumn('created_dts', 'datetime',['null'=>false])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('message')->drop();
    }
}