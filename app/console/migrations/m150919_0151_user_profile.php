<?php

use yii\db\Schema;
use yii\db\Migration;

class m150803_155200_user_profile extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user_profile', [
            'id' => Schema::TYPE_PK,
            'about' => Schema::TYPE_STRING,
			'notify_new_jibbs' => Schema::TYPE_BOOLEAN ,
			'notify_jibb_update' => Schema::TYPE_BOOLEAN ,
            'created_at' => Schema::TYPE_DATETIME . ' NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NULL',
        ], $tableOptions);


		$this->addForeignKey('user_profile_fk', 'user_profile', 'id', 'user', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
		$this->dropTable('user_profile');
    }
}
