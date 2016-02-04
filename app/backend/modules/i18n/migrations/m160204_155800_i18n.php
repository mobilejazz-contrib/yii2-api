<?php

namespace backend\modules\i18n\migrations;

use yii\db\Migration;
use yii\db\Schema;

class m160204_155800_i18n extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // SOURCE_MESSAGE
        $this->createTable('i18n_source_message', [
            'id' => Schema::TYPE_PK,
            'category' => Schema::TYPE_STRING . '(32) NOT NULL',
            'message' => Schema::TYPE_TEXT
        ], $tableOptions);

        // MESSAGE
        $this->createTable('i18n_message', [
            'id' => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
            'language' => Schema::TYPE_STRING . '(16) NOT NULL',
            'translation' => Schema::TYPE_TEXT,
            'PRIMARY KEY (`id`, `language`)'
        ]);
        $this->addForeignKey('fk_message_source_message', 'i18n_message', 'id', 'i18n_source_message', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        echo "m160204_155800_i18n cannot be reverted.\n";
    }
}
