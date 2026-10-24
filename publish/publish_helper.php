<?php
// 通用文章发布脚本
require('/www/wwwroot/laobaixingbanshi.com/wp-load.php');

function publish_article($title, $content, $category_ids = array(), $author = '老张') {
    global $wpdb;
    
    // 检查作者
    $user = get_user_by('login', $author);
    if (!$user) {
        $user = get_user_by('login', 'xushubao');
    }
    $author_id = $user ? $user->ID : 1;
    
    $post_data = array(
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => $author_id,
        'post_category' => $category_ids,
    );
    
    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
        echo "ERROR: " . $post_id->get_error_message() . "\n";
        return false;
    }
    echo "发布 $post_id: $title (分类" . implode(',', $category_ids) . ")\n";
    return $post_id;
}

// 从命令行参数读取JSON
if (isset($argv[1])) {
    $json = file_get_contents($argv[1]);
    $articles = json_decode($json, true);
    if ($articles) {
        foreach ($articles as $art) {
            publish_article($art['title'], $art['content'], $art['categories'], isset($art['author']) ? $art['author'] : '老张');
        }
        echo "DONE total=" . count($articles) . "\n";
    }
}
