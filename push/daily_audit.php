<?php
date_default_timezone_set('Asia/Shanghai');
require_once('/www/wwwroot/laobaixingbanshi.com/wp-load.php');
global $wpdb;

$today = date('Y-m-d');
$posts = $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} 
    WHERE post_status='publish' AND post_type='post' 
    AND DATE(CONVERT_TZ(post_date,'+00:00','+08:00')) = CURDATE()
    ORDER BY ID");

echo "TODAY_TOTAL: " . count($posts) . "\n\n";

$short = array();
$no_thumb = array();
$cat_issues = array();
$link_audit = array();

foreach ($posts as $p) {
    $id = $p->ID;
    $word_count = strlen(strip_tags($p->post_content));
    $img_count = substr_count($p->post_content, '<img');
    $thumb_id = get_post_thumbnail_id($id);
    
    // Categories
    $cats = wp_get_post_categories($id);
    $cat_names = array();
    foreach ($cats as $c) {
        $cat_names[] = get_cat_name($c);
    }
    
    // Word count check
    if ($word_count < 1000) {
        $short[] = "ID={$id} [{$word_count}字] {$p->post_title}";
    }
    
    // Thumbnail check
    if (!$thumb_id) {
        $no_thumb[] = "ID={$id} {$p->post_title}";
    }
    
    // Link audit - extract all hrefs
    preg_match_all('/href=["\']([^"\']+)["\']/', $p->post_content, $matches);
    $links = $matches[1];
    
    $fliggy = 0; $ctrip = 0; $ly = 0; $jd = 0; $pdd = 0; $vip = 0; $tb = 0; $dpurl = 0;
    $pc_links = array();
    $no_commission = array();
    
    foreach ($links as $link) {
        if (strpos($link, 'taobao.com') !== false || strpos($link, 'fliggy.com') !== false || strpos($link, 'feizhu.com') !== false) {
            $tb++;
            if (strpos($link, 'ali_trackid') === false && strpos($link, 's.click.taobao') === false) {
                $no_commission[] = $link;
            }
            if (strpos($link, 'travelsearch.fliggy.com') !== false || strpos($link, 'vacations.ctrip.com') !== false) {
                $pc_links[] = $link;
            }
        }
        if (strpos($link, 'ctrip.com') !== false) {
            $ctrip++;
            if (strpos($link, 'allianceid') === false && strpos($link, 't.ctrip.cn') === false) {
                $no_commission[] = $link;
            }
        }
        if (strpos($link, 'ly.com') !== false) {
            $ly++;
            if (strpos($link, 'refId') === false && strpos($link, 's.ly.com') === false) {
                $no_commission[] = $link;
            }
            if (strpos($link, 'www.ly.com') !== false) {
                $pc_links[] = $link;
            }
        }
        if (strpos($link, 'jd.com') !== false) { $jd++; if (strpos($link, 'union-click') === false) $no_commission[] = $link; }
        if (strpos($link, 'pinduoduo.com') !== false || strpos($link, 'yangkeduo.com') !== false) { $pdd++; }
        if (strpos($link, 'vip.com') !== false) { $vip++; if (strpos($link, 't.vip.com') === false) $no_commission[] = $link; }
        if (strpos($link, 'dpurl.cn') !== false) { $dpurl++; }
    }
    
    $total_affiliate = $fliggy + $ctrip + $ly + $jd + $pdd + $vip + $tb + $dpurl;
    
    if (count($no_commission) > 0 || count($pc_links) > 0) {
        $link_audit[] = "ID={$id} {$p->post_title}\n  无佣金链接: " . implode(', ', array_slice($no_commission, 0, 3)) . 
            (count($pc_links) > 0 ? "\n  PC端链接: " . implode(', ', array_slice($pc_links, 0, 3)) : "");
    }
}

echo "=== 字数不足1000字 (严重) ===\n";
echo count($short) > 0 ? implode("\n", $short) : "无";
echo "\n\n=== 无特色图 ===\n";
echo count($no_thumb) > 0 ? implode("\n", $no_thumb) : "无";
echo "\n\n=== 联盟链接问题 (无佣金/PC端) ===\n";
echo count($link_audit) > 0 ? implode("\n\n", $link_audit) : "无";
echo "\n";
