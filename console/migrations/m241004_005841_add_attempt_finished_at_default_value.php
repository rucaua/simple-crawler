<?php

use yii\db\Migration;

/**
 * Class m241004_005841_add_attempt_finished_at_default_value
 */
class m241004_005841_add_attempt_finished_at_default_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%attempt}}', 'finished_at', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%attempt}}', 'finished_at', $this->integer()->notNull());
        return true;
    }
}
