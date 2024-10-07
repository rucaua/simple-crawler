<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\StaleObjectException;

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

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['url_id', 'http_code', 'started_at', 'finished_at'], 'integer'],
            [['started_at'], 'required'],
            [
                ['url_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Url::class,
                'targetAttribute' => ['url_id' => 'id']
            ],
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
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function start(): bool
    {
        $this->started_at = time();
        return $this->update(false, ['started_at']);
    }

    /**
     * @param int $httpCode
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function finish(int $httpCode): bool
    {
        $this->finished_at = time();
        $this->http_code = $httpCode;
        return $this->update(false, ['finished_at','http_code']);
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


    /**
     * @param string $message
     * @return void
     * @throws Exception
     */
    public function log(string $message): void
    {
        $model = new AttemptLog();
        $model->attempt_id = $this->id;
        $model->log = $message;
        if (!$model->save()) {
            throw new Exception("AttemptLog not saved");
        }
    }
}
