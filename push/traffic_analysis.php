<?php
$logFile = '/www/wwwlogs/laobaixingbanshi.com.log';
$lines = @file($logFile);
if (!$lines) { echo "无法读取日志文件\n"; exit(1); }

$total = count($lines);
$pv = 0;
$ips = [];
$spiders = ['baidu' => 0, 'google' => 0, 'bing' => 0, 'so360' => 0, 'sogou' => 0, 'toutiao' => 0, 'other' => 0];
$realUser = 0;
$status = [];
$urls = [];
$userAgents = ['mobile' => 0, 'pc' => 0];
$todayCount = 0;
$today = date('d/M/Y');

foreach ($lines as $line) {
    // 解析nginx combined格式
    if (preg_match('/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (\S+) \S+" (\d+) \d+ "([^"]*)" "([^"]*)"/', $line, $m)) {
        $ip = $m[1];
        $datetime = $m[2];
        $method = $m[3];
        $url = $m[4];
        $code = $m[5];
        $referer = $m[6];
        $ua = $m[7];
        
        // 只统计GET请求和200/301状态
        if ($method !== 'GET') continue;
        if ($code != 200 && $code != 301 && $code != 304) continue;
        
        // 跳过静态资源
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot|webp)(\?|$)/i', $url)) continue;
        if (strpos($url, '/wp-content/') !== false || strpos($url, '/wp-includes/') !== false) continue;
        if (strpos($url, '/xmlrpc.php') !== false) continue;
        
        $pv++;
        $ips[$ip] = true;
        
        if (strpos($datetime, $today) !== false) $todayCount++;
        
        if (!isset($status[$code])) $status[$code] = 0;
        $status[$code]++;
        
        // 判断蜘蛛
        $uaLower = strtolower($ua);
        $isSpider = false;
        if (strpos($uaLower, 'baiduspider') !== false) { $spiders['baidu']++; $isSpider = true; }
        elseif (strpos($uaLower, 'googlebot') !== false) { $spiders['google']++; $isSpider = true; }
        elseif (strpos($uaLower, 'bingbot') !== false || strpos($uaLower, 'msnbot') !== false) { $spiders['bing']++; $isSpider = true; }
        elseif (strpos($uaLower, '360spider') !== false || strpos($uaLower, 'haosouspider') !== false) { $spiders['so360']++; $isSpider = true; }
        elseif (strpos($uaLower, 'sogou') !== false) { $spiders['sogou']++; $isSpider = true; }
        elseif (strpos($uaLower, 'bytespider') !== false || strpos($uaLower, 'toutiao') !== false) { $spiders['toutiao']++; $isSpider = true; }
        elseif (preg_match('/(spider|bot|crawler|slurp|duckduckgo|yandex|baiduspider)/i', $uaLower)) { $spiders['other']++; $isSpider = true; }
        
        if (!$isSpider) {
            $realUser++;
            // 设备判断
            if (preg_match('/(android|iphone|ipad|ipod|mobile|windows phone)/i', $uaLower)) {
                $userAgents['mobile']++;
            } else {
                $userAgents['pc']++;
            }
        }
        
        // 热门URL
        if (!$isSpider && $code == 200) {
            $cleanUrl = preg_replace('/\?.*$/', '', $url);
            if ($cleanUrl !== '/' && strlen($cleanUrl) > 5) {
                if (!isset($urls[$cleanUrl])) $urls[$cleanUrl] = 0;
                $urls[$cleanUrl]++;
            }
        }
    }
}

$uv = count($ips);
$spiderTotal = array_sum($spiders);

echo "=== 访问量统计（全部日志） ===\n";
echo "日志总行数: " . number_format($total) . "\n";
echo "有效PV(页面访问): " . number_format($pv) . "\n";
echo "UV(独立IP): " . number_format($uv) . "\n";
echo "今日PV: " . number_format($todayCount) . "\n";
echo "\n=== 访客类型 ===\n";
echo "真实用户请求: " . number_format($realUser) . " (" . round($realUser/$pv*100,1) . "%)\n";
echo "蜘蛛请求: " . number_format($spiderTotal) . " (" . round($spiderTotal/$pv*100,1) . "%)\n";
echo "\n=== 蜘蛛明细 ===\n";
foreach ($spiders as $k => $v) {
    if ($v > 0) echo "  $k: " . number_format($v) . "\n";
}
echo "\n=== 设备分布(真实用户) ===\n";
echo "手机端: " . number_format($userAgents['mobile']) . " (" . round($userAgents['mobile']/$realUser*100,1) . "%)\n";
echo "PC端: " . number_format($userAgents['pc']) . " (" . round($userAgents['pc']/$realUser*100,1) . "%)\n";
echo "\n=== HTTP状态 ===\n";
arsort($status);
foreach ($status as $k => $v) {
    echo "  $k: " . number_format($v) . "\n";
}
echo "\n=== 热门文章TOP10(真实用户) ===\n";
arsort($urls);
$i = 1;
foreach ($urls as $url => $cnt) {
    if ($i > 10) break;
    echo "  $i. [$cnt次] $url\n";
    $i++;
}
?>
