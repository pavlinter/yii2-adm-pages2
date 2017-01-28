<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.1.4
 */

use yii\db\Schema;
use yii\db\Migration;

class m141119_115055_adm_pages extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%page}}', [
            'id' => Schema::TYPE_PK,
            'id_parent' => Schema::TYPE_INTEGER,
            'layout' => Schema::TYPE_STRING . "(50) NOT NULL",
            'type' => Schema::TYPE_STRING . "(50) NOT NULL",
            'weight' => Schema::TYPE_INTEGER,
            'visible' => Schema::TYPE_BOOLEAN . "(1) NOT NULL DEFAULT '1'",
            'active' => Schema::TYPE_BOOLEAN . "(1) NOT NULL DEFAULT '1'",
            'date' => Schema::TYPE_DATETIME . " NULL",
            'created_at' => Schema::TYPE_TIMESTAMP . " NOT NULL",
            'updated_at' => Schema::TYPE_TIMESTAMP . " NOT NULL",
        ], $tableOptions);


        $this->createTable('{{%page_lang}}', [
            'page_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'language_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'name' => Schema::TYPE_STRING . "(100)",
            'title' => Schema::TYPE_STRING . "(70)",
            'description' => Schema::TYPE_STRING . "(160)",
            'alias' => Schema::TYPE_STRING . "(200)",
            'url' => Schema::TYPE_STRING . "(2000)",
            'short_text' => Schema::TYPE_TEXT . " NULL",
            'text' => Schema::TYPE_TEXT,
        ], $tableOptions);

        $this->createIndex('type', '{{%page}}', 'type');
        $this->createIndex('page_id', '{{%page_lang}}', 'page_id');
        $this->createIndex('language_id', '{{%page_lang}}', 'language_id');
        $this->createIndex('alias', '{{%page_lang}}', 'alias');

        $this->addPrimaryKey('page_language_pk', '{{%page_lang}}', ['page_id', 'language_id']);

        $this->addForeignKey('page_lang_ibfk_1', '{{%page_lang}}', 'page_id', '{{%page}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('page_lang_ibfk_2', '{{%page_lang}}', 'language_id', '{{%language}}', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%page_lang}}');
        $this->dropTable('{{%page}}');
    }
}
