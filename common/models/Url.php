<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "url".
 *
 * @property int $id
 * @property string|null $url
 * @property int|null $status
 * @property int|null $initiator
 * @property int $created_at
 * @property int|null $external_links
 * @property int|null $internal_links
 * @property int|null $images
 * @property int|null $words
 *
 * @property Attempt[] $attempts
 */
class Url extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'url';
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
            [['status', 'initiator', 'created_at', 'external_links', 'internal_links', 'images', 'words'], 'integer'],
            [['created_at'], 'required'],
            [['url'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'status' => 'Status',
            'initiator' => 'Initiator',
            'created_at' => 'Created At',
            'external_links' => 'External Links',
            'internal_links' => 'Internal Links',
            'images' => 'Images',
            'words' => 'Words',
        ];
    }

    /**
     * Gets query for [[Attempts]].
     *
     * @return ActiveQuery
     */
    public function getAttempts(): ActiveQuery
    {
        return $this->hasMany(Attempt::class, ['url_id' => 'id']);
    }
}
