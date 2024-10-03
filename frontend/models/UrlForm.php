<?php

namespace frontend\models;

use common\models\Url;
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
        if ($this->validate()) {
            $model = new Url();
            $model->url = $this->url;
            return $model->save();
        }
        return false;
    }
}