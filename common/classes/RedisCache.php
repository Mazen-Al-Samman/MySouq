<?php 
namespace common\classes;
use Yii;

class RedisCache extends yii\redis\Cache {
    
    // A new function to get all keys from the Redis.
    public function getkeys() {
        return $this->redis->executeCommand('keys', array('*'));
    }

    // Function to generate a new hash in cache.
    public function CreatePost($post_key, $post, $post_params) {
        $command = "HMSET {$post_key}";
        $command_param = "HMSET params:{$post_key}";
        $fields = [];
        foreach ($post as $key => $value) {
            $fields[] = "{$key}";
            $fields[] = "{$value}";
        }

        $params = [];
        foreach ($post_params as $key => $value) {
            $params[] = "{$key}";
            $params[] = "{$value}";
        }

        $this->redis->executeCommand($command_param, $params);
        return $this->redis->executeCommand($command, $fields);
    }

    // Function will return all the data for a speicific post name.
    public function GetPost($post_key) {
        $command = "HGETALL {$post_key}";
        $result = $this->redis->executeCommand($command);
        $len = count($result);
        $result_object = [];
        $result_object['post_id'] = $post_key;
        for ($i = 0; $i < $len; $i += 2) { 
            $key = $result[$i];
            $val = $result[$i + 1];
            $result_object[$key] = $val;
        }

        $command = "HGETALL params:{$post_key}";
        $result = $this->redis->executeCommand($command);
        $len = count($result);
        for ($i = 0; $i < $len; $i += 2) { 
            $key = $result[$i];
            $val = $result[$i + 1];
            $result_object['custom_params'][$key] = $val;
        }
        return $result_object;
    }

    // Function to remove post from the cache.
    public function RemovePost($post_key) {
        $this->redis->executeCommand("DEL $post_key");
        $this->redis->executeCommand("DEL params:$post_key");
        return;
    }

    // Function to gel all posts.
    public function AllPosts() {
        $posts = $this->redis->executeCommand('LRANGE posts 0 -1');
        $all_posts = [];
        for($i = 0; $i < count($posts); $i++) {
            $post_key = $posts[$i];
            $post_data = $this->GetPost($post_key);
            $all_posts[] = $post_data;
        }
        return $all_posts;
    }

    public function cachePost($post_details, $post_key, $status) {
        $this->RemovePost($post_key);
        $post_data = [
            'title' => $post_details['title'],
            'description' => $post_details['description'],
            'user_id' => $post_details['user_id'],
            'status' => $status,
            'category' => $post_details['category'],
            'price' => $post_details['price'],
            'created_at' => $post_details['created_at'],
            'from' => 'Cache'
        ];

        $params = $post_details['custom_params'];
        $this->CreatePost($post_key, $post_data, $params);
        return true;
    }
}
?>