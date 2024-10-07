<?php

use yii\db\Migration;

/**
 * Class m241007_221334_add_responseTime_for_attempt
 */
class m241007_221334_add_responseTime_for_attempt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%attempt}}', 'response_time', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attempt}}', 'response_time');

        return true;
    }
}
