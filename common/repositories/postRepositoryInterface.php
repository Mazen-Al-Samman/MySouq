<?php 
namespace common\repositories;

interface postRepositoryInterface {
    public function create_new_post($cat_id);
    public function get_all_posts();
    public function get_all_posts_for_user($user_id, $first_row_id, $posts_per_page);
    public function block_all_posts_that_contains($word);
    public function change_post_status($role_id, $post_id, $action, $status_id, $status);
}
?>