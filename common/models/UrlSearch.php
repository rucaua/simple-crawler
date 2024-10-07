<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * UrlSearch represents the model behind the search form of `common\models\Url`.
 */
class UrlSearch extends Url
{

    public int|string|null $statusName = null;
    public int|string|null $createdBy = null;
    public int|string|null $createdBefore = null;
    public int|string|null $lastHttpCode = null;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                ['id', 'statusName', 'external_links', 'internal_links', 'images', 'words', 'lastHttpCode'],
                'integer'
            ],
            [['url', 'createdBy', 'createdBefore'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Url::find()->joinWith('lastAttempt');
        $dataProvider = $this->createDataProvider($query);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->statusName,
            'external_links' => $this->external_links,
            'internal_links' => $this->internal_links,
            'images' => $this->images,
            'words' => $this->words,
            'attempt.http_code' => $this->lastHttpCode,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url]);

        switch ($this->createdBefore) {
            case 'today':
                $query->andFilterWhere(['>=', 'created_at', strtotime('today')])
                    ->andFilterWhere(['<=', 'created_at', time()]);
                break;
            case 'yesterday':
                $query->andFilterWhere(['>=', 'created_at', strtotime('yesterday')])
                    ->andFilterWhere(['<', 'created_at', strtotime('today')]);
                break;
            case 'thisWeek':
                $query->andFilterWhere(['>=', 'created_at', strtotime('monday this week')]);
                break;
            case 'thisMonth':
                $query->andFilterWhere(['>=', 'created_at', strtotime('first day of this month')]);
                break;
            case 'thisYear':
                $query->andFilterWhere(['>=', 'created_at', strtotime('first day of January this year')]);
                break;
            case 'older':
                $query->andFilterWhere(['<', 'created_at', strtotime('first day of January this year')]);
                break;
        }


        if ($this->createdBy !== '') {
            if (mb_strtolower((string)$this->createdBy) === self::INITIATOR_UNKNOWN || $this->createdBy < 0) {
                $query->andWhere(['initiator' => null]);
            } elseif (in_array(mb_strtolower((string)$this->createdBy), ['user', 'u', 0])) {
                $query->andFilterWhere(['initiator' => 0]);
            } else {
                $query->andFilterWhere(['initiator' => $this->createdBy]);
            }
        }


        return $dataProvider;
    }

    public function createDataProvider(Query $query): ActiveDataProvider
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // custom sorting for statusName
        $dataProvider->sort->attributes['statusName'] = [
            'asc' => ['status' => SORT_ASC],
            'desc' => ['status' => SORT_DESC],
        ];

        // custom sorting for createdBy
        $dataProvider->sort->attributes['createdBy'] = [
            'asc' => ['initiator' => SORT_ASC],
            'desc' => ['initiator' => SORT_DESC],
        ];

        // custom sorting for createdBefore
        $dataProvider->sort->attributes['createdBefore'] = [
            'asc' => ['created_at' => SORT_ASC],
            'desc' => ['created_at' => SORT_DESC],
        ];

        // custom sorting for lastHttpCode
        $dataProvider->sort->attributes['lastHttpCode'] = [
            'asc' => ['attempt.http_code' => SORT_ASC],
            'desc' => ['attempt.http_code' => SORT_DESC],
        ];

        $dataProvider->sort->defaultOrder = ['id' => SORT_ASC];

        return $dataProvider;
    }
}
