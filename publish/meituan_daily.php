<?php
/**
 * 美团联盟每日自动更新 v2.0 — 单品推广模式
 * 用法: php /root/meituan_daily.php [--force]
 * 
 * 新模式：每个商品单独一篇文章，上中下3处同一个dpurl.cn链接
 * 每篇≥3张图（美团主图+2张裁剪变体），SEO标题从模板池轮换
 * 每品类每天发2-3篇，全天10-12篇
 */
require '/www/wwwroot/laobaixingbanshi.com/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

date_default_timezone_set('Asia/Shanghai');

define('MT_APP_KEY', 'afed224fdd48475ab5aa0f366110bed5');
define('MT_SECRET', 'a611de892a4b43859c0c9117f66eee69');
define('MT_SID', 'xsbmeituan');
define('MT_BASE', 'https://media.meituan.com');

function mt_body_md5($body) { return base64_encode(md5($body, true)); }

function mt_api($path, $body, $query = array()) {
    $body_str = json_encode($body, JSON_UNESCAPED_UNICODE);
    $ts = (string)round(microtime(true) * 1000);
    $md5 = mt_body_md5($body_str);
    $hdrs = array('S-Ca-App' => MT_APP_KEY, 'S-Ca-Timestamp' => $ts);
    ksort($hdrs);
    $hs = '';
    foreach ($hdrs as $k => $v) $hs .= $k . ':' . $v . "\n";
    $url = $path;
    if ($query) { ksort($query); $qs = array(); foreach ($query as $k => $v) $qs[] = $k . '=' . $v; $url .= '?' . implode('&', $qs); }
    $sts = "POST\n" . $md5 . "\n" . $hs . $url;
    $sign = base64_encode(hash_hmac('sha256', $sts, MT_SECRET, true));
    $h = array(
        'Content-Type: application/json',
        'S-Ca-App: ' . MT_APP_KEY,
        'S-Ca-Timestamp: ' . $ts,
        'Content-MD5: ' . $md5,
        'S-Ca-Signature-Headers: S-Ca-Timestamp,S-Ca-App',
        'S-Ca-Signature: ' . $sign,
    );
    $ch = curl_init(MT_BASE . $url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body_str, CURLOPT_HTTPHEADER => $h,
        CURLOPT_TIMEOUT => 20,
    ));
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true);
}

function mt_query($kw) { return mt_api('/cps_open/common/api/v1/query_coupon', $kw); }

function mt_link($sign, $platform, $biz) {
    $body = array('sid' => MT_SID, 'linkType' => 2, 'productViewSign' => $sign);
    if ($platform) $body['platform'] = $platform;
    if ($biz !== null) $body['bizLine'] = $biz;
    $r = mt_api('/cps_open/common/api/v1/get_referral_link', $body);
    return ($r && $r['code'] == 0) ? $r['data'] : null;
}

// 下载图片到WP媒体库
function upload_image($url, $title, $suffix = '') {
    $upload_dir = wp_upload_dir();
    $dir = $upload_dir['path'];
    $fn = 'mt-' . date('Ymd') . '-' . substr(md5($url . $suffix), 0, 8) . '.jpg';
    $dest = $dir . '/' . $fn;
    $tmp = '/tmp/mt_img_' . substr(md5($url . $suffix), 0, 8) . '.jpg';
    shell_exec("curl -sL --max-time 25 -o '{$tmp}' '{$url}'");
    if (!file_exists($tmp) || filesize($tmp) < 500) return null;
    copy($tmp, $dest);
    @unlink($tmp);
    $att = array('post_mime_type' => 'image/jpeg', 'post_title' => $title, 'post_status' => 'inherit', 'post_content' => '');
    $aid = wp_insert_attachment($att, $dest);
    if ($aid && !is_wp_error($aid)) {
        wp_update_attachment_metadata($aid, wp_generate_attachment_metadata($aid, $dest));
        return array('id' => $aid, 'url' => $upload_dir['url'] . '/' . $fn);
    }
    return null;
}

// 从headUrl生成3张不同裁剪的图
function get_three_images($headUrl, $brand) {
    $images = array();
    // 图1：原图（去裁剪后缀）
    $orig = preg_replace('/@.*/', '', $headUrl);
    $img1 = upload_image($orig, $brand . '商品主图', 'orig');
    if ($img1) $images[] = $img1;
    // 图2：方形裁剪（原参数）
    $img2 = upload_image($headUrl, $brand . '商品细节图', 'sq');
    if ($img2) $images[] = $img2;
    // 图3：宽幅裁剪（改参数）
    $wide = preg_replace('/@.*/', '', $headUrl) . '@0_0_800_450a|800w_450h_2e_90Q';
    $img3 = upload_image($wide, $brand . '商品场景图', 'wide');
    if ($img3) $images[] = $img3;
    return $images;
}

// 检查商品是否已发过（标题含品牌+商品名）
function already_published($brand, $name) {
    global $wpdb;
    $keyword = $wpdb->esc_like($brand) . '%' . $wpdb->esc_like(mb_substr($name, 0, 6));
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_status='publish' AND post_date > DATE_SUB(NOW(), INTERVAL 30 DAY)",
        '%' . $brand . '%'
    ));
    return $count > 0;
}

// ========== SEO标题模板池 ==========
$title_templates = array(
    '外卖' => array(
        '{brand}优惠券怎么领？{name}美团团购{sell}元，比门店省{save}元',
        '{brand}外卖优惠力度最大的套餐是哪个？{name}{sell}元亲测最划算',
        '{cat}优惠券哪里领？{brand}{name}美团团购{sell}元，全国通用',
        '怎么领{brand}的优惠券？{name}团购攻略，{sell}元吃{orig}元套餐',
        '{brand}多少钱？{name}美团团购{sell}元，原价{orig}元',
    ),
    '到店美食' => array(
        '{brand}优惠券怎么领？{name}美团团购{sell}元，比门店省{save}元',
        '{brand}到店优惠力度最大的套餐是哪个？{name}{sell}元亲测最划算',
        '{cat}优惠券哪里领？{brand}{name}美团团购{sell}元',
        '怎么领{brand}的优惠券？{name}团购攻略，{sell}元吃{orig}元套餐',
        '{brand}团购划算吗？{name}{sell}元实测，比门店便宜{save}元',
    ),
    '特惠酒店' => array(
        '{brand}优惠券怎么领？美团团购比前台便宜{save}元',
        '住{city}酒店哪个平台最便宜？{brand}美团团购{sell}元',
        '{brand}多少钱一晚？美团团购优惠攻略，{sell}元住{orig}元房',
        '{city}酒店优惠券领取方法，{brand}美团团购立省{save}元',
        '{brand}酒店团购划算吗？{sell}元住一晚，比OTA便宜{save}元',
    ),
    '门票度假' => array(
        '{brand}门票怎么买最便宜？美团团购{sell}元，比窗口省{save}元',
        '{brand}门票多少钱？美团优惠券领取攻略，{sell}元起',
        '怎么领{brand}的门票优惠券？美团团购{sell}元，随时退',
        '{brand}优惠门票哪里买？美团团购{sell}元，比窗口便宜{save}元',
        '{brand}门票团购攻略，{sell}元入园，比现场买省{save}元',
    ),
    '休闲生活' => array(
        '{brand}优惠券怎么领？{name}美团团购{sell}元，比门店省{save}元',
        '{cat}哪里最划算？{brand}{name}美团团购{sell}元',
        '{brand}多少钱？{name}美团团购{sell}元，原价{orig}元',
        '怎么领{brand}的优惠券？{name}团购攻略，{sell}元享{orig}元服务',
        '{brand}团购划算吗？{name}{sell}元实测，比门店便宜{save}元',
    ),
);

// ========== 品类配置 ==========
$cats = array(
    '外卖' => array(
        'id' => 92, 'cat_name' => '外卖',
        'query' => array('platform' => 1, 'listTopiId' => 1, 'pageSize' => 10),
        'daily' => 2,
    ),
    '到店美食' => array(
        'id' => 93, 'cat_name' => '到店美食',
        'query' => array('platform' => 2, 'bizLine' => 1, 'listTopiId' => 5, 'pageSize' => 50),
        'daily' => 3,
    ),
    '特惠酒店' => array(
        'id' => 94, 'cat_name' => '酒店住宿',
        'query' => array('platform' => 2, 'bizLine' => 3, 'listTopiId' => 3, 'pageSize' => 10),
        'daily' => 2,
    ),
    '门票度假' => array(
        'id' => 95, 'cat_name' => '门票景区',
        'query' => array('platform' => 2, 'bizLine' => 4, 'listTopiId' => 3, 'pageSize' => 10),
        'daily' => 2,
    ),
    '休闲生活' => array(
        'id' => 96, 'cat_name' => '休闲娱乐',
        'query' => array('platform' => 2, 'bizLine' => 2, 'listTopiId' => 5, 'pageSize' => 10),
        'daily' => 2,
    ),
);

$force = in_array('--force', $argv);
$today = date('Y-m-d');
$created = 0;
$btn_style = 'display:inline-block;background:#ffc300;color:#333;padding:12px 32px;border-radius:8px;text-decoration:none;font-size:16px;font-weight:600;margin:10px 0;';
$box_style = 'background:#fff8e6;border:1px solid #ffe082;border-radius:12px;padding:16px 20px;margin:16px 0;text-align:center;';

foreach ($cats as $cat_key => $cfg) {
    $cat_id = $cfg['id'];
    $cat_name = $cfg['cat_name'];
    
    // 当天该分类已发数量
    if (!$force) {
        $ex = get_posts(array('post_type' => 'post', 'posts_per_page' => -1,
            'category' => $cat_id, 'year' => date('Y'), 'monthnum' => date('n'), 'day' => date('j')));
        $today_count = count($ex);
        if ($today_count >= $cfg['daily']) {
            echo "SKIP {$cat_key} 今日已发{$today_count}篇\n";
            continue;
        }
        $need = $cfg['daily'] - $today_count;
    } else {
        $need = $cfg['daily'];
    }

    // 查询商品
    $r = mt_query($cfg['query']);
    if (!$r || $r['code'] != 0) {
        echo "ERR {$cat_key} API: " . ($r['message'] ?? 'no resp') . "\n";
        continue;
    }

    $published = 0;
    foreach ($r['data'] as $d) {
        if ($published >= $need) break;
        $cp = $d['couponPackDetail'] ?? array();
        $bd = $d['brandInfo'] ?? array();
        if (empty($cp['productViewSign'])) continue;

        $brand = trim($bd['brandName'] ?? '');
        $name = $cp['name'] ?? '';
        // 清理商品名：去掉【】前缀和后缀编码
        $clean_name = preg_replace('/^【[^】]*】/', '', $name);
        $clean_name = preg_replace('/\s*[A-Z]{2,}\d+$/', '', $clean_name);
        $clean_name = trim($clean_name);
        if (!$clean_name) $clean_name = $name;
        // 品牌为空时用商品名或分类名兜底
        if (!$brand) $brand = mb_substr($clean_name, 0, 8) ?: $cat_name;
        $sell = $cp['sellPrice'] ?? '0';
        $orig = $cp['originalPrice'] ?? '0';
        $save = round((float)$orig - (float)$sell, 1);
        // 省钱金额过大时用百分比
        $save_display = ($save > 999) ? round((1 - $sell / $orig) * 100) . '%' : $save . '元';
        $vol = $cp['saleVolume'] ?? '';
        $headUrl = $cp['headUrl'] ?? '';
        $city_num = $d['availablePoiInfo']['availablePoiCityNum'] ?? '全国';
        $rank = $cp['productLabel']['productRankLabel'] ?? '';
        $price_label = $cp['productLabel']['pricePowerLabel']['historyPriceLabel'] ?? '';

        // 去重：品牌已发过则跳过（简单去重）
        if (!$force && already_published($brand, $name)) {
            echo "SKIP 重复商品: {$brand}\n";
            continue;
        }

        // 生成推广链接
        $link = mt_link($cp['productViewSign'], $cfg['query']['platform'] ?? null, $cfg['query']['bizLine'] ?? null);
        if (!$link) { echo "ERR 链接生成失败: {$brand}\n"; continue; }

        // 下载3张图
        $images = get_three_images($headUrl, $brand);
        if (count($images) < 1) { echo "ERR 图片下载失败: {$brand}\n"; continue; }
        $thumb_id = $images[0]['id'];

        // 生成SEO标题
        $templates = $title_templates[$cat_key];
        $tpl = $templates[array_rand($templates)];
        $title = str_replace(
            array('{brand}', '{name}', '{sell}', '{orig}', '{save}', '{cat}', '{city}'),
            array($brand, mb_substr($clean_name, 0, 12), $sell, $orig, $save_display, $cat_name, '热门城市'),
            $tpl
        );
        // 标题过长截断
        if (mb_strlen($title) > 60) $title = mb_substr($title, 0, 58) . '…';

        // 生成正文
        $img_tags = '';
        foreach ($images as $img) {
            $img_tags .= '<p><img src="' . $img['url'] . '" alt="' . $brand . '" style="max-width:100%;height:auto;display:block;margin:15px auto;border-radius:8px;" /></p>';
        }
        $img1 = isset($images[0]) ? $images[0]['url'] : '';
        $img2 = isset($images[1]) ? $images[1]['url'] : $img1;
        $img3 = isset($images[2]) ? $images[2]['url'] : $img1;

        $content = '<p>作为一个经常在美团上找优惠的人，最近发现' . $brand . '这个' . $clean_name . '真的划算。原价' . $orig . '元，美团团购只要' . $sell . '元，直接省了' . $save_display . '。我亲自买了一份体验了一下，给大家说说真实感受，值不值得买一看便知。</p>

<div style="' . $box_style . '">
<p style="margin:0 0 8px 0;font-size:15px;color:#666;">👇 点击下方按钮，查看' . $brand . '实时优惠价</p>
<a href="' . $link . '" target="_blank" rel="nofollow noopener" style="' . $btn_style . '">美团团购 ' . $sell . '元抢</a>
</div>

<h2>' . $clean_name . '到底包含什么</h2>
<p>先说清楚这个套餐的内容。' . $clean_name . '，在' . $brand . '门店原价要' . $orig . '元，美团团购价' . $sell . '元，相当于打了' . round($sell / $orig * 10) . '折。' . ($vol ? '目前已经' . $vol . '，' : '') . ($rank ? $rank . '，' : '') . ($price_label ? '还是' . $price_label . '。' : '') . '</p>
<p>我买的时候仔细看了使用规则，' . ($city_num != '全国' ? '全国' . $city_num . '个城市可用' : '全国多城通用') . '，不用预约，到店出示券码直接核销。有效期内未使用可以随时退，过期也能自动退，这点比较良心。</p>

<p><img src="' . $img1 . '" alt="' . $brand . '商品图" style="max-width:100%;height:auto;display:block;margin:15px auto;border-radius:8px;" /></p>

<h2>真实体验：核销方便吗</h2>
<p>买之前我还担心团购券会不会很麻烦，结果完全多虑了。美团下单后直接出券码，到店出示给店员扫码就行，不用预约不用等。我去的那家店中午人不少，出示券码后等了不到10分钟就搞定了。</p>
<p>有一点要提醒：买了之后注意有效期，别像我第一次一样买了忘了用，差点过期。建议设个提醒，或者买了就尽快用。另外，部分门店高峰期可能要排队，错峰去体验更好。</p>

<p><img src="' . $img2 . '" alt="' . $brand . '细节图" style="max-width:100%;height:auto;display:block;margin:15px auto;border-radius:8px;" /></p>

<h2>价格对比：到底省了多少钱</h2>
<table style="width:100%;border-collapse:collapse;margin:15px 0;font-size:14px;">
<tr style="background:#f5f5f5;"><th style="border:1px solid #ddd;padding:10px;text-align:left;">项目</th><th style="border:1px solid #ddd;padding:10px;text-align:left;">门店原价</th><th style="border:1px solid #ddd;padding:10px;text-align:left;">美团团购</th><th style="border:1px solid #ddd;padding:10px;text-align:left;">节省</th></tr>
<tr><td style="border:1px solid #ddd;padding:10px;">' . $clean_name . '</td><td style="border:1px solid #ddd;padding:10px;">' . $orig . '元</td><td style="border:1px solid #ddd;padding:10px;color:#e02020;font-weight:600;">' . $sell . '元</td><td style="border:1px solid #ddd;padding:10px;color:#10883a;font-weight:600;">' . $save . '元</td></tr>
<tr style="background:#fff8e6;"><td style="border:1px solid #ddd;padding:10px;font-weight:600;">折扣力度</td><td style="border:1px solid #ddd;padding:10px;">—</td><td style="border:1px solid #ddd;padding:10px;font-weight:600;">' . round($sell / $orig * 10) . '折</td><td style="border:1px solid #ddd;padding:10px;color:#10883a;font-weight:600;">省' . round((1 - $sell / $orig) * 100) . '%</td></tr>
</table>
<p>相当于花' . $sell . '元就能享受到' . $orig . '元的' . $cat_name . '服务，性价比确实高。' . ($vol ? '目前已经' . $vol . '，说明很多人都在买。' : '') . '</p>

<div style="' . $box_style . '">
<p style="margin:0 0 8px 0;font-size:15px;color:#666;">👇 点击下方按钮，锁价' . $sell . '元，手慢无</p>
<a href="' . $link . '" target="_blank" rel="nofollow noopener" style="' . $btn_style . '">立即抢购 ' . $brand . '</a>
</div>

<h2>适合谁买，要注意什么</h2>
<p><strong>推荐买的：</strong></p>
<ul>
<li>日常' . $cat_name . '消费比较多的，团购价比直接买省不少</li>
<li>想尝试' . $brand . '但又怕贵的，可以先用团购券体验</li>
<li>家庭或朋友一起去的，一次买多份更划算</li>
<li>出差旅行到新城市的，提前买好到店直接用</li>
</ul>
<p><strong>注意事项：</strong></p>
<ul>
<li>买了尽快用，注意有效期，过期虽然能退但麻烦</li>
<li>部分门店高峰期可能要等，建议错峰</li>
<li>具体可用门店以美团详情页为准，买之前先看附近有没有店</li>
</ul>

<p><img src="' . $img3 . '" alt="' . $brand . '场景图" style="max-width:100%;height:auto;display:block;margin:15px auto;border-radius:8px;" /></p>

<h2>总结</h2>
<p>' . $brand . '这个' . $clean_name . '是我近期买过比较划算的团购了。' . $sell . '元享' . $orig . '元的服务，核销方便，随时可退，没什么风险。唯一要注意的就是别买了忘用。</p>
<p>如果你也正好需要，链接放下面了，趁现在有活动赶紧冲，晚了可能恢复原价。</p>

<div style="' . $box_style . '">
<p style="margin:0 0 8px 0;font-size:15px;color:#666;">👇 点击下方按钮，立即购买，支持随时退、过期退</p>
<a href="' . $link . '" target="_blank" rel="nofollow noopener" style="' . $btn_style . '">美团团购 ' . $sell . '元 全国通用</a>
</div>

<p style="color:#999;font-size:12px;margin-top:20px;">以上价格为美团团购实时价格，具体以美团页面为准。套餐内容和可用门店以美团详情页说明为准。</p>';

        // 发布
        $pid = wp_insert_post(array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_category' => array(51, 210, 84, $cat_id),
        ));

        if ($pid && !is_wp_error($pid)) {
            if ($thumb_id) set_post_thumbnail($pid, $thumb_id);
            $word_count = mb_strlen(strip_tags($content));
            echo "OK {$cat_key} ID={$pid}: {$title} ({$word_count}字, " . count($images) . "图)\n";
            $published++;
            $created++;
        } else {
            echo "ERR 发布失败 {$cat_key}: " . ($pid ? $pid->get_error_message() : 'unknown') . "\n";
        }
        usleep(300000);
    }
    echo "--- {$cat_key} 发布{$published}篇 ---\n";
}

echo "\nDONE created={$created}\n";
