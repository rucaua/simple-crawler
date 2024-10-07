<?php

use yii\db\Migration;

/**
 * Class m241004_010347_alter_attempt_log
 */
class m241004_010347_alter_attempt_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%attempt_log}}', 'log', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%attempt_log}}', 'log', $this->integer());

        return true;
    }
}
