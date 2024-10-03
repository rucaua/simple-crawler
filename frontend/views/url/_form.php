<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Url $model */
/** @var yii\widgets\ActiveForm $form */
/** @var yii\widgets\ActiveForm $pjaxID */
?>

<div class="url-form">

    <?php
    $formID = 'url-form';
    $form = ActiveForm::begin(
        [
            'id' => $formID,
            'class' => 'row g-3',
            'action' => Url::toRoute(['url/create']),
            'enableAjaxValidation' => true,
            'validationUrl' => Url::toRoute(['url/validate']),
        ]
    ); ?>

    <div class="row">
        <div class="col">
            <?= $form->field($model, 'url')->textInput(
                ['maxlength' => true, 'placeholder' => 'Add url for crawling']
            )->label(false) ?>
        </div>

        <div class="col">
            <?= Html::submitButton('Add', ['class' => 'btn btn-dark']) ?>
        </div>
    </div>
    <?php
    ActiveForm::end();

    $this->registerJs(
            /** @lang JavaScript */
        '
        $(document).on("beforeSubmit", "#' . $formID . '", function () {
            const form = $(this);
            const formData = form.serialize();
            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                success: function (data) {
                    $.pjax.reload({container: "#' . $pjaxID . '"});
                },
                error: function (e) {
                    console.log("Url form error", e);
                }
            });
        return false;
        });
'
    );
    ?>

</div>
