<?php
/**
 * 批量发布招考公告文章
 */
date_default_timezone_set('Asia/Shanghai');
require_once '/root/wp_publish_helper.php';

$json_file = '/root/articles_final.json';
$raw = file_get_contents($json_file);
$articles = json_decode($raw, true);

if (!is_array($articles)) {
    echo "FATAL: cannot decode JSON\n";
    exit(1);
}

echo "Total articles to process: " . count($articles) . "\n";

$results = array();
foreach ($articles as $i => $a) {
    $title = $a['title'];
    $cats = $a['cats'];
    $img_path = $a['img'];
    $content = $a['content'];

    echo "\n--- [" . ($i+1) . "/" . count($articles) . "] " . $title . " ---\n";

    // 去重：完全标题匹配
    if (title_exists($title)) {
        echo "SKIP: title exists\n";
        $results[] = array('title'=>$title, 'status'=>'duplicate', 'post_id'=>0);
        continue;
    }

    // 上传特色图
    $thumb_id = upload_image_from_local($img_path, 0);
    if (!$thumb_id) {
        echo "WARN: image upload failed, continue without thumb\n";
    } else {
        echo "thumb_id=" . $thumb_id . "\n";
    }

    // 发布
    $post_id = publish_article(array(
        'title' => $title,
        'content' => $content,
        'categories' => $cats,
        'featured_image_id' => $thumb_id,
        'post_status' => 'publish',
    ));

    if ($post_id) {
        echo "OK post_id=" . $post_id . "\n";
        $results[] = array('title'=>$title, 'status'=>'ok', 'post_id'=>$post_id, 'thumb_id'=>$thumb_id, 'cats'=>$cats);
    } else {
        echo "FAIL\n";
        $results[] = array('title'=>$title, 'status'=>'fail', 'post_id'=>0);
    }
}

// 输出结果JSON
file_put_contents('/root/publish_result.json', json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "\n=== DONE ===\n";
echo "Success: " . count(array_filter($results, function($r){return $r['status']==='ok';})) . "\n";
echo "Duplicate: " . count(array_filter($results, function($r){return $r['status']==='duplicate';})) . "\n";
echo "Fail: " . count(array_filter($results, function($r){return $r['status']==='fail';})) . "\n";
