<?php
require_once __DIR__ . '/../config.php';
date_default_timezone_set('Asia/Shanghai');
$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($db->connect_error) { die("DB failed: " . $db->connect_error); }
$db->set_charset('utf8mb4');

$limit = isset($argv[1]) ? intval($argv[1]) : 2000;

// 取所有已发布文章，按时间倒序
$sql = "SELECT p.ID, p.post_name, p.post_date 
        FROM wp_posts p 
        WHERE p.post_status='publish' AND p.post_type='post' 
        ORDER BY p.post_date DESC LIMIT $limit";
$result = $db->query($sql);

$urls = [];
while ($row = $result->fetch_assoc()) {
    if (!$row['post_name']) continue;
    // 取主分类（parent最小的那个）
    $r3 = $db->query("SELECT t.slug FROM wp_term_relationships tr 
                      JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id 
                      JOIN wp_terms t ON tt.term_id=t.term_id 
                      WHERE tr.object_id=" . $row['ID'] . " AND tt.taxonomy='category' 
                      ORDER BY tt.parent ASC LIMIT 1");
    $cat = $r3->fetch_assoc();
    $catslug = $cat ? $cat['slug'] : 'wanle';
    // 跳过emoji分类slug
    if (strpos($catslug, '%f0') === 0) $catslug = 'wanle';
    $urls[] = "https://laobaixingbanshi.com/{$catslug}/{$row['post_name']}.html";
}

// 去重
$urls = array_unique($urls);
echo "取到 " . count($urls) . " 个唯一URL\n";

// 百度API推送（分批，每批2000条以内）
$token = $config["baidu_token"];
$api = "http://data.zz.baidu.com/urls?site=https://laobaixingbanshi.com&token={$token}";

$batches = array_chunk($urls, 2000);
$total_success = 0;
$total_remain = 0;

foreach ($batches as $i => $batch) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, implode("\n", $batch));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/plain']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "批次" . ($i+1) . " (" . count($batch) . "条): HTTP=$httpcode 响应=$response\n";
    
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        $total_success += $data['success'];
        $total_remain = $data['remain'] ?? $total_remain;
    }
    if ($data && isset($data['error'])) {
        echo "  错误: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    }
    sleep(1);
}

echo "\n=== 推送完成 ===\n";
echo "成功推送: $total_success 条\n";
echo "今日剩余配额: $total_remain 条\n";
