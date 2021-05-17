<?php
use yii\helpers\Html;
use yii\helpers\Json;

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
for ($i = 0; $i < count($posts); $i++) {
    $idx--;
    if ($idx == 0) {
      $last_id = $posts[$i]['post_id'];
    }
    ?>
    <div class="col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
        <h2 class="card-title font-cabin text-info"><?=Html::encode($posts[$i]['title'], $doubleEncode = true)?></h2>
        <p style="width:100%;height:30px;line-height:30px;border-radius:20px;letter-spacing:5px;" class="text-uppercase font-weight-bold <?php echo ($posts[$i]['status'] == 'Live') ? 'bg-success text-light' : 'bg-warning'; ?>"><?=Html::encode($posts[$i]['status'], $doubleEncode = true)?></p>
        <hr>
        <p class="font-poppins-400 text-danger font-weight-bold">$ <?= $posts[$i]['price'] ?></p>
        <p class="card-text font-poppins-400 font-weight-bold"><?=Html::encode($posts[$i]['description'], $doubleEncode = true)?></p>
        <div class="params">
          <?php 
          foreach ($posts[$i]['custom_params'] as $field => $option) { 
          ?>
          <p class="font-poppins-400 bg-dark text-light p-2 rounded"><?= $field ?> : <?= $option ?></p>
          <?php
          }
          ?>
        </div>
        <hr>
        <?=Html::a('Delete', ['post/delete', 'id' => $posts[$i]['post_id']], ['class' => 'btn-danger float-right btn font-poppins-400', 'onclick' => "return confirm('Are you sure you want to delete post?')"])?>
        <?=Html::a('More', ['post/more', 'id' => $posts[$i]['post_id']], ['class' => 'btn-info float-left btn font-poppins-400'])?>
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
