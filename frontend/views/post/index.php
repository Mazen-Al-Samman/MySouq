<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\Category;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

$json_options = Json::encode($options);
$json_fields = Json::encode($fields);

$this->title = 'New Post';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <?php $form = ActiveForm::begin(['id' => 'new_post', 'class' => 'form-group']); ?>

                <?= $form->field($model, 'title')->textInput(['autofocus' => true, 'class' => 'form-control']) ?>

                <?= $form->field($model, 'description')->textArea(['class' => 'form-control']) ?>

                <div id="section"></div>

                <?= $form->field($model, 'price')->textInput(['class' => 'form-control']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Post', ['class' => 'btn btn-primary', 'name' => 'post-button', 'style' => 'width:20%;']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select a category</h5>
        </button>
      </div>
      <div class="modal-body">
        <form id="cat-form">
            <label for="cat">Category</label>
            <?= Html::activeDropDownList($model, 'cat_id',
            ArrayHelper::map(Category::find()->all(), 'id', 'title'),
            ['class' => 'form-control mb-3', 'id' => 'cat'])?>
        </form>
      </div>
    <div class="modal-footer">
        <button type="button" id="cat-select" class="btn btn-primary">Select</button>
    </div>
    </div>
  </div>
</div>

<?php 
$this->registerJs("
    let json_fields = $json_fields;
    let json_options = $json_options;
    let selected_cat = 1;
    $(window).on('load', function() {
        $('#exampleModal').modal('show');
    });
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#cat-select').on('click', function(){
        selected_cat = $('#cat').val();
        $('#exampleModal').modal('hide');
        $('#section').append(`<input type='hidden' name='cat_id' value=` + selected_cat + `>`);
        for (let i = 0; i < json_fields.length; i++){
            let obj = json_fields[i];
            console.log(obj['label']);
            if(obj['cat_id'] == selected_cat) {
                $('#section').append(function(){
                    let html = `<label>` + obj['label'] +`</label>`;
                    html += `<select name=field` + obj['field_id'] + ` class='form-control mb-4'>`;
                    for (let j = 0; j < json_options.length; j++){
                        let opt_obj = json_options[j];
                        if (opt_obj['field_id'] == obj['field_id']){
                            html += `<option value=` + opt_obj['id'] + `>` + opt_obj['title'] + `</option>`
                        }
                    }
                    html += `</select>`;
                    return html;
                });
            }
        }
    });
")
?>