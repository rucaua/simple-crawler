<?php

use yii\db\Migration;


/**
 * Class m241003_191353_add_url
 */
class m241003_191353_add_url extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%url}}', [
            'id' => $this->primaryKey(11),
            'url' => $this->string(2048),
            'status' => $this->tinyInteger(2),
            'initiator' => $this->integer(11),
            'created_at' => $this->integer()->notNull(),
            'external_links' => $this->integer(),
            'internal_links' => $this->integer(),
            'images' => $this->integer(),
            'words' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%url}}');
        return true;
    }
}
