<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "attempt_log".
 *
 * @property int $id
 * @property int|null $attempt_id
 * @property int $created_at
 * @property int|null $log
 *
 * @property Attempt $attempt
 */
class AttemptLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'attempt_log';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['attempt_id', 'created_at'], 'integer'],
            [['log'], 'string'],
            [['created_at'], 'safe'],
            [['attempt_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attempt::class, 'targetAttribute' => ['attempt_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'attempt_id' => 'Attempt ID',
            'created_at' => 'Created At',
            'log' => 'Log',
        ];
    }

    /**
     * Gets query for [[Attempt]].
     *
     * @return ActiveQuery
     */
    public function getAttempt(): ActiveQuery
    {
        return $this->hasOne(Attempt::class, ['id' => 'attempt_id']);
    }
}
