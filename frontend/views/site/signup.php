<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Country;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <p class="text-center">Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>
                
                <label for="country" class="mt-2">Country</label>
                <?= Html::activeDropDownList($user, 'country_id',
                ArrayHelper::map(Country::find()->all(), 'id', 'title'),
                ['class' => 'form-control mb-3', 'id' => 'country']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
