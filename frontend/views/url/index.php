<?php

use common\models\Url;
use frontend\models\UrlForm;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\UrlSearch $searchModel */
/** @var UrlForm $form */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'URLs';
$this->params['breadcrumbs'][] = $this->title;
$pjaxID = 'grid-pjax'
?>
<div class="url-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= $this->render('_form', ['model' => $form, 'pjaxID' => $pjaxID]) ?>
    </p>

    <?php
    Pjax::begin(['id' => $pjaxID]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            /** Data columns starts @see \yii\grid\DataColumn */
            'id',
            [
                'attribute' => 'url',
                'format' => 'raw',
                'value' => function (Url $data) {
                    return Html::a($data->url, $data->url, ['target' => '_blank', 'data-pjax' => 0]);
                }
            ],
            [
                'attribute' => 'statusName',
                'label' => 'Status',
                'filterInputOptions' => ['prompt' => 'Any', 'class' => 'form-control'],
                'filter' => Url::getStatusList(),
            ],
            [
                'attribute' => 'createdBy',
                'label' => 'Created by',
                'format' => 'raw',
                'value' => function (Url $data) {
                    if ($data->isInitiatorUrl) {
                        return Html::a(
                            $data->initiatorName,
                            ['url/view', 'id' => $data->initiator],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                    } else {
                        return $data->initiatorName;
                    }
                }
            ],
            [
                'attribute' => 'createdBefore',
                'filter' => [
                    'today' => 'Today',
                    'yesterday' => 'Yesterday',
                    'thisWeek' => 'This Week',
                    'thisMonth' => 'This Month',
                    'thisYear' => 'This Year',
                    'older' => 'Last Year And Older',
                ],
                'format' => ['datetime', 'short'],
                'value' => 'created_at',
            ],
            [
                'label' => 'Last Http Code',
                'attribute' => 'lastHttpCode',
                'value' => 'lastAttempt.http_code',
            ],
            [
                'label' => 'Last Response Time',
                'attribute' => 'responseTime',
                'filterInputOptions' => ['prompt' => 'Any', 'class' => 'form-control'],
                'filter' => ['slow' => 'Slow', 'normal' => 'Normal'],
                'value' => 'responseTime'
            ],
            'external_links',
            'internal_links',
            'images',
            'words',
            /** Data columns ends @see \yii\grid\DataColumn */
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>

    <?php
    Pjax::end(); ?>

</div>
