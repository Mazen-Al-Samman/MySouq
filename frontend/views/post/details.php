<?php
use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Post Details';
?>
<div class="container mt-5">
    <div class="row d-flex justify-content-center text-center">
        <div class="col-lg-8 mt-2 rounded p-5 font-poppins-400 shadow">
            <h1 class="text-info"><?= $post['title'] ?></h1>
            <p class="pr-5 float-right font-weight-bold text"><?= $post['created_at'] ?></p>
            <p class="pl-5 float-left font-weight-bold text"><?= $post['status'] ?></p>
            <div style="clear:both"></div>
            <hr>
            <h3 class="mt-4 bg-warning p-3 text-black rounded"><?= $post['description'] ?></h3>
            <?php 
                foreach ($post['custom_params'] as $field => $val) {
            ?>
            <h5 class="mt-1 bg-dark p-3 text-white rounded"><?= $field . ' : ' . $val ?></h5>
            <?php } ?>
            <p>Post from : <?php echo isset($post['from'])? $post['from'] : 'Mongo' ?></p>
        </div>
    </div>
</div>

<style>
.text {
    letter-spacing: 2px;
}
</style>