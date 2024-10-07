<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Url $model */
/** @var ActiveDataProvider $attemptDataProvider */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Urls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="url-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-dark',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'url:url',
            'status',
            'initiator',
            'created_at:datetime',
            'external_links',
            'internal_links',
            'images',
            'words',
        ],
    ]) ?>


    <h2>Attempts:</h2>
    <?= GridView::widget([
        'dataProvider' => $attemptDataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            /** Data columns starts @see \yii\grid\DataColumn */
            'started_at:datetime',
            'finished_at:datetime',
            'http_code',
            [
                'attribute' => 'response_time',
                'value' => 'responseTimeInSeconds'
            ],
            /** Data columns ends @see \yii\grid\DataColumn */
        ],
    ]); ?>
</div>
