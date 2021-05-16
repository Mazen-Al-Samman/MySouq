<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\Category;
use common\models\SubCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$this->title = 'New Post';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login font-poppins-400">
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
                    <?= Html::submitButton('Post', ['class' => 'btn btn-primary mt-2', 'name' => 'post-button', 'style' => 'width:20%;']) ?>
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

            <label for="cat">Sub Category</label>
            <?= Html::activeDropDownList($model, 'sub_cat_id',
            ArrayHelper::map(SubCategory::find()->where(['cat_id' => 1])->all(), 'id', 'title'),
            ['class' => 'form-control mb-3', 'id' => 'sub_cat', 'name' => 'sub_cat'])?>

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
    let site_url = `$url`;
    let selected_cat = 1;
    $(window).on('load', function() {
        $('#exampleModal').modal('show');
    });
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#cat').on('change', function(){
        selected_cat = $('#cat').val();
        $.ajax ({
            url: site_url + `/index.php?r=post%2Fsubcat&cat_id=` + selected_cat,
            success: function(response) {
                let res = JSON.parse(response);
                let option_html = ``;
                for (let i = 0; i < res.length; i++) {
                    option_html += `<option value=` + res[i]['id'] + `>` + res[i]['title'] + `</option>` 
                }
                $('#sub_cat').html(option_html);
            }
        });
    });
    $('#cat-select').on('click', function(){
        selected_cat = $('#cat').val();
        let sub_cat_id = $('#sub_cat').val();
        $('#exampleModal').modal('hide');
        $('#section').append(`<input type='hidden' name='cat_id' value=` + selected_cat + `>`);
        $('#section').append(`<input type='hidden' name='sub_cat_id' value=` + sub_cat_id + `>`);
        $.ajax ({
            url: site_url + `/index.php?r=post%2Fparams&cat_id=` + sub_cat_id,
            success: function(response) {
                res = JSON.parse(response);
                for (let i = 0; i < res.length; i++) {
                    let field = res[i]['field'];
                    let option = res[i]['options'];
                    let html = `<label>` + field.label +`</label>`;
                    if (field.type == 'Option') {
                        html += `<select name=field_option_` + field.field_id + ` class='form-control mb-4'>`;   
                        for (let j = 0; j < option.length; j++) {
                            html += `<option value=` + option[j].id + `>` + option[j].title + `</option>`
                        }
                        html += `</select>`;
                    } else if (field.type == 'Int'){
                        html += `<input type='number' name=field_int_` + field.field_id + ` class='form-control mb-4' />`; 
                    } else if (field.type == 'VarChar'){
                        html += `<input type='text' name=field_varchar_` + field.field_id + ` class='form-control mb-4' />`; 
                    } else if (field.type == 'Float') {
                        html += `<input type='text' name=field_float_` + field.field_id + ` class='form-control mb-4' />`; 
                    }
                    $('#section').append(html);
                }
            }
        })
    });
")
?>