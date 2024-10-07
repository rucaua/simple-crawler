<?php

namespace frontend\models;

use common\models\Url;
use common\models\UrlStatusEnum;
use yii\base\Model;
use yii\db\Exception;

class UrlForm extends Model
{
    public $url;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['url', 'required'],
            ['url', 'unique', 'targetClass' => Url::class, 'message' => 'Url {value} already in list.'],
            ['url', 'url'],
        ];
    }

    /**
     * create new
     *
     * @throws Exception
     */
    public function create(): bool
    {
        return $this->validate() && Url::createNew($this->url, 0);
    }
}