<?php
require_once __DIR__ . '/../config.php';
date_default_timezone_set('Asia/Shanghai');
$key = $config["indexnow_key"];
$keyLocation = "https://laobaixingbanshi.com/{$key}.txt";

$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($db->connect_error) { die("DB failed: " . $db->connect_error); }
$db->set_charset('utf8mb4');

$limit = isset($argv[1]) ? intval($argv[1]) : 10000;

// 取所有已发布文章URL
$sql = "SELECT p.ID, p.post_name FROM wp_posts p 
        WHERE p.post_status='publish' AND p.post_type='post' 
        ORDER BY p.post_date DESC LIMIT $limit";
$result = $db->query($sql);

$urls = [];
while ($row = $result->fetch_assoc()) {
    if (!$row['post_name']) continue;
    $r3 = $db->query("SELECT t.slug FROM wp_term_relationships tr 
                      JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id 
                      JOIN wp_terms t ON tt.term_id=t.term_id 
                      WHERE tr.object_id=" . $row['ID'] . " AND tt.taxonomy='category' 
                      ORDER BY tt.parent ASC LIMIT 1");
    $cat = $r3->fetch_assoc();
    $catslug = $cat ? $cat['slug'] : 'wanle';
    if (strpos($catslug, '%f0') === 0) $catslug = 'wanle';
    $urls[] = "https://laobaixingbanshi.com/{$catslug}/{$row['post_name']}.html";
}
$urls = array_unique($urls);
echo "取到 " . count($urls) . " 个唯一URL\n";

// IndexNow API端点（Bing, Yandex, Seznam等通用）
$endpoints = [
    "https://api.indexnow.org/indexnow",
    "https://www.bing.com/indexnow",
    "https://yandex.com/indexnow",
];

$batches = array_chunk($urls, 10000); // IndexNow每批最多10000条

foreach ($batches as $bi => $batch) {
    $payload = json_encode([
        "host" => "laobaixingbanshi.com",
        "key" => $key,
        "keyLocation" => $keyLocation,
        "urlList" => $batch
    ]);
    
    foreach ($endpoints as $endpoint) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        echo "批次" . ($bi+1) . " -> $endpoint: HTTP=$httpcode 响应=$response\n";
    }
    sleep(1);
}

echo "\n=== IndexNow推送完成 ===\n";
echo "已推送到Bing/Yandex/Seznam等搜索引擎\n";
