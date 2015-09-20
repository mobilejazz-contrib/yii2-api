<?php

use yii\db\Schema;
use yii\db\Migration;

class m150803_160000_user_notifications extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user_notification', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			'item_class' => Schema::TYPE_STRING,
			'item_id' => Schema::TYPE_INTEGER,
			'message' => Schema::TYPE_STRING . ' NOT NULL',
			'data' => Schema::TYPE_TEXT . ' NULL',
            'created_at' => Schema::TYPE_TIMESTAMP,
            'updated_at' => Schema::TYPE_TIMESTAMP,
			'sent_at' => Schema::TYPE_TIMESTAMP,
			'read_at' => Schema::TYPE_TIMESTAMP,
        ], $tableOptions);

		$this->addForeignKey('user_notification_user_fk', 'user_notification', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
		$this->dropTable('user_notification');
    }
}
