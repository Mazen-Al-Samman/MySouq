<?php
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
$this->title = 'All Posts';
?>
<div class="container mt-3 text-center">
    <h1 class="text-center font-poppins-400">All new Posts</h1>
    <hr>
    <div class="row">
    <?php
foreach ($posts as $post) {
    ?>
    <div class="col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
        <h3 class="font-poppins-400"><?= $post->user->username ?></h3>
        <hr>
        <h2 class="card-title font-cabin text-info"><?=Html::encode($post->title, $doubleEncode = true)?></h2>
        <p style="width:100%;height:30px;line-height:30px;border-radius:20px;letter-spacing:5px;" class="text-uppercase font-weight-bold <?php echo ($post->status_id == 1) ? 'bg-success text-light' : 'bg-warning'; ?>"><?=Html::encode($post->status->title, $doubleEncode = true)?></p>
        <hr>
        <p class="font-poppins-400 text-danger font-weight-bold">$ <?= $post->price ?></p>
        <p class="card-text font-weight-bold"><?=Html::encode($post->description, $doubleEncode = true)?></p>
        <div class="params">
          <?php 
          $len = count($post->values);
          $idx = 0;
          for ($i = 0; $i < $len; $i++) { 
            $type = $post->subCat->fieldAssigns[$idx]->type;
            $idx += 2;
          ?>
          <p class="font-poppins-400 bg-dark text-light p-2 rounded"><?= $post->values[$i]->field->title ?>
           :
          <?php 
            if ($type == 'Int') {
              echo $post->values[$i]->int_val;
            } else if ($type == 'Float') {
              echo $post->values[$i]->float_val;
            } else if ($type == 'VarChar') {
              echo $post->values[$i]->varchar_val;
            } else if ($type == 'Option') {
              echo $post->values[$i]->option->title;
            }?>
          </p>
          <?php
          }
          ?>
        </div>
        <hr>
        <?php if ($post->status_id == 2) { ?>
            <?=Html::a('Accept', ['site/accept', 'id' => $post->id], ['class' => 'btn-success float-left font-poppins-400 btn', 'onclick' => "return confirm('Are you sure you want to aceept this post?')"])?>
        <?php } ?>
        <?=Html::a('Block', ['site/block', 'id' => $post->id], ['class' => 'btn-danger float-right font-poppins-400 btn', 'onclick' => "return confirm('Are you sure you want to block this post?')"])?>
      </div>
    </div>
    </div>
    <?php }?>
    </div>
</div>