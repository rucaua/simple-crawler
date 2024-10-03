<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "attempt".
 *
 * @property int $id
 * @property int|null $url_id
 * @property int|null $http_code
 * @property int $started_at
 * @property int $finished_at
 *
 * @property AttemptLog[] $attemptLogs
 * @property Url $url
 */
class Attempt extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'attempt';
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'started_at',
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
            [['url_id', 'http_code', 'started_at', 'finished_at'], 'integer'],
            [['started_at'], 'required'],
            [['url_id'], 'exist', 'skipOnError' => true, 'targetClass' => Url::class, 'targetAttribute' => ['url_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'url_id' => 'Url ID',
            'http_code' => 'Http Code',
            'started_at' => 'Started At',
            'finished_at' => 'Finished At',
        ];
    }

    /**
     * Gets query for [[AttemptLogs]].
     *
     * @return ActiveQuery
     */
    public function getAttemptLogs(): ActiveQuery
    {
        return $this->hasMany(AttemptLog::class, ['attempt_id' => 'id']);
    }

    /**
     * Gets query for [[Url]].
     *
     * @return ActiveQuery
     */
    public function getUrl(): ActiveQuery
    {
        return $this->hasOne(Url::class, ['id' => 'url_id']);
    }
}
