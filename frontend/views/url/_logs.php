<?php

use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

?>
<div class="logs">

    <?php Pjax::begin([
        'enablePushState' => false
    ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            /** Data columns starts @see \yii\grid\DataColumn */
            'created_at',
            'attempt_id',
            [
                    'attribute' => 'log',
                    'contentOptions' => [
                        'style'=>'max-width:150px; white-space: normal;'
                    ]
            ],
            /** Data columns ends @see \yii\grid\DataColumn */
        ],
    ]); ?>
    <?php Pjax::end() ?>

</div>
