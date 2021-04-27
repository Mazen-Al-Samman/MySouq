<?php
use yii\helpers\Html;
use yii\helpers\Json;
use yii\bootstrap4\ActiveForm;

$idx = count($posts);
$last_id = 0;
$posts_per_page = isset(Yii::$app->request->get()['posts_per_page'])? Yii::$app->request->get()['posts_per_page'] : 3;

$this->title = 'My posts';
?>
<div class="container mt-3 text-center">
    <h1 class="text-center font-poppins-400">My Posts</h1>
    <hr>
    <div class="row">
    <?php
foreach ($posts as $post) {
    $idx--;
    if ($idx == 0) {
      $last_id = $post->id;
    }
    ?>
    <div class="col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
        <h2 class="card-title font-cabin text-info"><?=Html::encode($post->title, $doubleEncode = true)?></h2>
        <p style="width:100%;height:30px;line-height:30px;border-radius:20px;letter-spacing:5px;" class="text-uppercase font-weight-bold <?php echo ($post->status_id == 1) ? 'bg-success text-light' : 'bg-warning'; ?>"><?=Html::encode($post->status->title, $doubleEncode = true)?></p>
        <hr>
        <p class="font-poppins-400 text-danger font-weight-bold">$ <?= $post->price ?></p>
        <p class="card-text font-poppins-400 font-weight-bold"><?=Html::encode($post->description, $doubleEncode = true)?></p>
        <div>
          <?php 
          $len = count($post->values);
          for ($i = 0; $i < $len; $i++) { 
          ?>
          <p class="font-poppins-400 bg-dark text-light p-2 rounded"><?= $post->values[$i]->field->title ?> : <?= $post->values[$i]->option->title ?></p>
          <?php
          }
          ?>
        </div>
        <hr>
        <?=Html::a('Delete', ['post/delete', 'id' => $post->id], ['class' => 'btn-danger float-right btn', 'onclick' => "return confirm('Are you sure you want to delete post?')"])?>
      </div>
    </div>
    </div>
    <?php }?>
    </div>
    <?php 
    if (count($posts) == $posts_per_page){
      echo Html::a('Next', ['site/index', 'first_row_id' => $last_id, 'posts_per_page' => $posts_per_page], ['class' => 'btn btn-dark text-light float-right font-poppins-400 mb-4']) ;
    } 
    ?>
</div>
