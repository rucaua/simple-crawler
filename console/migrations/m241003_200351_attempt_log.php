<?php

use yii\db\Migration;

/**
 * Class m241003_200351_log
 */
class m241003_200351_attempt_log extends Migration
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

        $this->createTable('{{%attempt_log}}', [
            'id' => $this->primaryKey(11),
            'attempt_id' => $this->integer(11),
            'created_at' => $this->integer()->notNull(),
            'log' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-attempt_log-attempt_id',
            '{{%attempt_log}}',
            'attempt_id',
            '{{%attempt}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-attempt_log-attempt_id','{{%attempt_log}}');
        $this->dropTable('{{%attempt_log}}');
        return true;
    }
}
