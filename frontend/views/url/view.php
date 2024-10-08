<?php

use yii\bootstrap5\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
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
                'lastAttempt.http_code',
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
                [
                    'class' => ActionColumn::class,
                    'template' => '{logs}',
                    'buttons' => [
                        'logs' => function (string $url, \common\models\Attempt $attempt) {
                            return Html::a(
                                '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M192 0c-41.8 0-77.4 26.7-90.5 64L64 64C28.7 64 0 92.7 0 128L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64l-37.5 0C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16l128 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-128 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm88 0c0-8.8 7.2-16 16-16l128 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-128 0c-8.8 0-16-7.2-16-16z"/></svg>',
                                ['url/view-logs/', 'id' => $attempt->id],
                                ['title' => "Logs", 'data-toggle' => 'show-logs']
                            );
                        }
                    ],
                ],
            ],
        ]); ?>

        <?php
        Modal::begin([
            'id' => 'logs',
            'size' => 'modal-lg',
            'title' => 'Logs'
        ]); ?>
        <div id='modalContent'></div>
        <?php
        Modal::end(); ?>
    </div>
    <script type="text/template" data-template="spinner">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </script>

<?php
$this->registerJs(
    "
    let spinnerHTML = $('[data-template=spinner]').html()
    $('[data-toggle=show-logs]').on('click', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        $('#logs').modal('show')
            .find('#modalContent')
            .html(spinnerHTML)
            .load(url);
    })
"
);