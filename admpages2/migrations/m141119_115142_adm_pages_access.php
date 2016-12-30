<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.1.2
 */

use yii\db\Schema;
use yii\db\Migration;

class m141119_115142_adm_pages_access extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'],[
            [
                'Adm-Pages',
                2,
                'Access to pages module',
                NULL,
                NULL,
                time(),
                time(),
            ],
        ]);

        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'],[
            [
                'AdmAdmin',
                'Adm-Pages',
            ],
            [
                'AdmRoot',
                'Adm-Pages',
            ],
        ]);
    }

    public function down()
    {
        $this->delete('{{auth_item_child}}', "parent='AdmRoot' AND child='Adm-Pages'");
        $this->delete('{{auth_item_child}}', "parent='AdmAdmin' AND child='Adm-Pages'");
        $this->delete('{{auth_item}}', "name='Adm-Pages'");
    }
}
