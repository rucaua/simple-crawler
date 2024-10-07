<?php

namespace common\models;

use common\interfaces\CrawlerResultInterface;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "url".
 *
 * @property-read int $id
 * @property string|null $url
 * @property int|null $status
 * @property int|null $initiator
 * @property int $created_at
 * @property int|null $external_links
 * @property int|null $internal_links
 * @property int|null $images
 * @property int|null $words
 *
 * @property-read string $initiatorName
 * @property-read string $isInitiatorUrl
 *
 * @property-read Attempt[] $attempts
 * @property-read Attempt $currentAttempt
 * @property-read null | self $nextUrl
 * @property-read null | self $initiatorObject
 * @property-read Attempt | null $lastAttempt
 */
class Url extends ActiveRecord
{

    const INITIATOR_UNKNOWN = 'unknown';

    protected Attempt|null $currentAttempt = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'url';
    }

    public function behaviors(): array
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
            ['status', 'in', 'range' => array_column(UrlStatusEnum::cases(), 'value')],
            ['created_at', 'safe'],
            ['url', 'string', 'max' => 2048],
            ['url', 'unique'],
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
     * @return string
     */
    public function getStatusName(): string
    {
        return UrlStatusEnum::tryFrom($this->status)?->getLabel();
    }


    /**
     * List of possible statuses for self as dbvalue => label
     *
     * @return array array<int, string>
     */
    public static function getStatusList(): array
    {
        return UrlStatusEnum::array();
    }


    /**=
     * @return string
     * @throws \Exception
     */
    public function getInitiatorName(): string
    {
        return match ($this->initiator) {
            0 => 'User',
            null => self::INITIATOR_UNKNOWN,
            default => $this->initiator,
        };
    }

    /**
     * true if this Url is created while crawling another one
     *
     * @return bool
     */
    public function getIsInitiatorUrl(): bool
    {
        return $this->initiator > 0;
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


    /**
     * Gets query for the last [[Attempt]].
     *
     * @return ActiveQuery
     */
    public function getLastAttempt(): ActiveQuery
    {
        return $this->hasOne(Attempt::class, ['url_id' => 'id'])->orderBy('attempt.id');
    }


    /**
     * Gets query for [[Url]] during parsing of which this was created
     *
     * @return ActiveQuery
     */
    public function getInitiatorObject(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'initiator']);
    }


    /**
     * @return $this
     * @throws ErrorException
     * @throws Exception
     */
    public function startAttempt(): self
    {
        $model = new Attempt();
        $model->url_id = $this->id;
        $model->started_at = time();
        if ($model->save()) {
            $this->setCurrentAttempt($model);
            return $this;
        }
        throw new ErrorException('Attempt model not saved');
    }


    /**
     * @throws \Throwable
     * @throws ErrorException
     * @throws StaleObjectException
     */
    public function finishAttempt(CrawlerResultInterface $result): self
    {
        if ($this->currentAttempt->url_id === $this->id && $this->currentAttempt->finish(
                $result->getHttpStatusCode()
            )) {
            if ($result->getHttpStatusCode() === 200) {
                $this->status = UrlStatusEnum::CRAWLED->value;
                $this->external_links = $result->getExternalLinksCount();
                $this->internal_links = count($result->getInternalLinks());
                $this->images = $result->getImagesCount();
                $this->words = $result->WordsCount();
                $this->update(true, ['status', 'external_links', 'internal_links', 'images', 'words']);
                $this->addInternalLinksToQueue($result->getInternalLinks());
            } elseif ($this->getAttempts()->count() >= Yii::$app->params['attemptLimit']) {
                $this->status = UrlStatusEnum::FAILED->value;
                $this->update(true, ['status']);
            }
            return $this;
        }
        throw new ErrorException('Attempt model not saved');
    }


    /**
     * @throws Exception
     * @throws ErrorException
     */
    public function addInternalLinksToQueue(array $links): void
    {
        foreach ($links as $link) {
            if (!$this->createNew($link, $this->id)) {
                $this->getCurrentAttempt()->log("Link $link can not be added");
            }
        }
    }


    /**
     * @param string $url
     * @param int $initiator 0 for manual input from UI and url id for parent page
     * @return bool
     * @throws Exception
     */
    public static function createNew(string $url, int $initiator): bool
    {
        $model = new Url;
        $model->url = $url;
        $model->initiator = $initiator;
        $model->status = UrlStatusEnum::NEW->value;
        return $model->save();
    }


    /**
     * @return ActiveRecord|self|null
     */
    public static function getNextUrl(): self|ActiveRecord|null
    {
        return self::find()
            ->andWhere(['status' => [UrlStatusEnum::NEW->value, UrlStatusEnum::IN_PROGRESS->value]])
            ->orderBy([
                'created_at' => SORT_ASC,
            ])->one();
    }


    /**
     * @return Attempt
     * @throws ErrorException
     */
    public function getCurrentAttempt(): Attempt
    {
        if ($this->currentAttempt !== null) {
            return $this->currentAttempt;
        }
        throw new ErrorException("currentAttempt is not set");
    }

    public function setCurrentAttempt(Attempt $currentAttempt): void
    {
        $this->currentAttempt = $currentAttempt;
    }
}
