<?php

use yii\db\Migration;

/**
 * Class m241003_193154_add_crawl
 */
class m241003_193154_add_attempt extends Migration
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

        $this->createTable('{{%attempt}}', [
            'id' => $this->primaryKey(11),
            'url_id' => $this->integer(11),
            'http_code' => $this->integer(3),
            'started_at' => $this->integer()->notNull(),
            'finished_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-attempt-url_id',
            '{{%attempt}}',
            'url_id',
            '{{%url}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-attempt-url_id','{{%attempt}}');
        $this->dropTable('{{%attempt}}');
        return true;
    }
}
