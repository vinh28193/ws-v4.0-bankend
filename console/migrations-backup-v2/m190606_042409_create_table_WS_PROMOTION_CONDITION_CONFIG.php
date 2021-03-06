<?php

use yii\db\Migration;

class m190606_042409_create_table_WS_PROMOTION_CONDITION_CONFIG extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%PROMOTION_CONDITION_CONFIG}}', [
            'id' => $this->integer()->notNull()->comment('ID'),
            'store_id' => $this->integer()->notNull()->comment('Store ID'),
            'name' => $this->string(80)->notNull()->comment('name of condition'),
            'operator' => $this->string(10)->notNull()->comment('Operator of condition'),
            'type_cast' => $this->string(10)->notNull()->defaultValue('\'integer\'')->comment('php type cast (integer,string,float ..etc)'),
            'description' => $this->text()->comment('description'),
            'status' => $this->integer()->defaultValue('1')->comment('Status (1:Active;2:Inactive)'),
            'created_by' => $this->integer()->comment('Created by'),
            'created_at' => $this->integer()->comment('Created at (timestamp)'),
            'updated_by' => $this->integer()->comment('Updated by'),
            'updated_at' => $this->integer()->comment('Updated at (timestamp)'),
        ], $tableOptions);

        $this->createIndex('SYS_IL0000108788C00006$$', '{{%PROMOTION_CONDITION_CONFIG}}', '', true);
    }

    public function down()
    {
        $this->dropTable('{{%PROMOTION_CONDITION_CONFIG}}');
    }
}
