<?php

use common\models\Url;
use frontend\models\UrlForm;
use yii\helpers\Html;
use yii\helpers\Url as UrlHelper;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\UrlSearch $searchModel */
/** @var UrlForm $form */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Urls';
$this->params['breadcrumbs'][] = $this->title;
$pjaxID = 'grid-pjax'
?>
<div class="url-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= $this->render('_form', ['model' => $form, 'pjaxID' => $pjaxID]) ?>
    </p>

    <?php
    Pjax::begin([
        'id' => $pjaxID
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'url:url',
            'status',
            'initiator',
            'created_at:datetime',
            'external_links',
            'internal_links',
            'images',
            'words',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Url $model, $key, $index, $column) {
                    return UrlHelper::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>

    <?php
    Pjax::end(); ?>

</div>
