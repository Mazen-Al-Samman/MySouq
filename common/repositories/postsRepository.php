<?php 

namespace common\repositories;
use common\models\Posts;
use common\repositories\postRepositoryInterface;
use Yii;

class postsRepository implements postRepositoryInterface {
    public function create_new_post($cat_id){
        $modelPost = new Posts();
        return $modelPost->create_new_post($cat_id);
    }

    public function get_all_posts(){
        $modelPost = new Posts();
        return $modelPost->get_all_posts();
    }

    public function get_all_posts_for_user($user_id, $first_row_id = -1, $posts_per_page = -1){
        $modelPost = new Posts();
        return $modelPost->get_all_posts_for_user($user_id, $first_row_id, $posts_per_page);
    }

    public function block_all_posts_that_contains($word){
        $modelPost = new Posts();
        return $modelPost->block_all_posts_that_contains($word);
    }

    public function change_post_status($role_id, $post_id, $action, $status_id, $status){
        $modelPost = new Posts();
        return $modelPost->change_post_status($role_id, $post_id, $action, $status_id, $status);
    }
    
}
?>