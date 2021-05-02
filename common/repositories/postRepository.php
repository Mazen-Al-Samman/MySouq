<?php 

namespace common\repositories;
use common\models\Post;
use common\repositories\postRepositoryInterface;
use Yii;

class postRepository implements postRepositoryInterface {
    public function create_new_post($cat_id){
        $modelPost = new Post();
        return $modelPost->create_new_post($cat_id);
    }

    public function get_all_posts(){
        $modelPost = new Post();
        return $modelPost->get_all_posts();
    }

    public function get_all_posts_for_user($user_id, $first_row_id = -1, $posts_per_page = -1){
        $modelPost = new Post();
        return $modelPost->get_all_posts_for_user($user_id, $first_row_id = -1, $posts_per_page = -1);
    }

    public function block_all_posts_that_contains($word){
        $modelPost = new Post();
        return $modelPost->block_all_posts_that_contains($word);
    }

    public function change_post_status($role_id, $post_id, $action, $status_id, $status){
        $modelPost = new Post();
        return $modelPost->change_post_status($role_id, $post_id, $action, $status_id, $status);
    }
    
}
?>