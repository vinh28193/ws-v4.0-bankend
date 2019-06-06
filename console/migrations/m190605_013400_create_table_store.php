<?php

use yii\db\Migration;

class m190605_013400_create_table_store extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%store}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'country_id' => $this->integer(11),
            'locale' => $this->string(255),
            'name' => $this->string(255),
            'country_name' => $this->string(255),
            'address' => $this->text(),
            'url' => $this->string(255),
            'currency' => $this->string(255),
            'currency_id' => $this->integer(11),
            'status' => $this->integer(11),
            'env' => $this->integer(11)->comment('PROD or UAT or BETA ...'),
            'version' => $this->string(255)->defaultValue('4.0')->comment('version 4.0'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%store}}');
    }
}