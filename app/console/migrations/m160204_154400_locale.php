<?php

use yii\db\Migration;
use yii\db\Schema;

class m160204_154400_locale extends Migration
{

    public function down()
    {
        $this->dropTable('{{%locale}}');
    }

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // MENU
        $this->createTable('{{%locale}}',
            [
                'id'         => Schema::TYPE_INTEGER . '(11) NOT NULL AUTO_INCREMENT',
                'lang'       => $this->string(),
                'label'      => $this->string(),
                'default'    => $this->boolean(),
                'used'       => $this->integer()->defaultValue(1),
                'rtl'        => $this->integer()->defaultValue(0),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY (`id`, `lang`)',
            ],
            $tableOptions);

        $this->insert('{{%locale}}',
            [
                'id'         => 1,
                'lang'       => 'en',
                'label'      => 'English',
                'default'    => true,
                'used'       => 1,
                'rtl'        => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
    }
}
