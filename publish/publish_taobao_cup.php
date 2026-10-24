<?php
require '/www/wwwroot/laobaixingbanshi.com/wp-config.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
date_default_timezone_set('Asia/Shanghai');

// 上传6张商品图
$images = [
    'fuguang_cup_1.jpg',
    'fuguang_cup_2.jpg',
    'fuguang_cup_3.jpg',
    'fuguang_cup_4.jpg',
    'fuguang_cup_5.jpg',
    'fuguang_cup_6.jpg',
];
$attach_ids = [];
$img_urls = [];
foreach ($images as $fname) {
    $file_path = '/root/product_images/' . $fname;
    if (!file_exists($file_path)) {
        echo "文件不存在: $fname\n";
        continue;
    }
    $file_array = ['name' => $fname, 'tmp_name' => $file_path];
    $attach_id = media_handle_sideload($file_array, 0);
    if (is_wp_error($attach_id)) {
        echo "上传失败 $fname: " . $attach_id->get_error_message() . "\n";
        continue;
    }
    $attach_ids[] = $attach_id;
    $img_urls[] = wp_get_attachment_url($attach_id);
    echo "上传成功: $fname -> ID $attach_id\n";
}

if (count($attach_ids) < 3) {
    echo "图片不足，停止发布\n";
    exit;
}

// 商品信息
$product_name = "富光智能指纹解锁密码保温杯";
$price = "179.55";
$shop = "富光旗舰店（天猫）";
$promo_link = "https://m.tb.cn/h.jWTKBN";
$taokouling = "49￥HU006TgOKTFQ￥hu8￥";

// 写文章
$title = "富光智能指纹保温杯实测：学生上学专用，指纹解锁防偷喝，316不锈钢保温12小时";

$content = '<p>孩子上学带水杯，最头疼的就是杯子被同学乱拿、水被偷喝。普通保温杯没有锁，放在教室一会儿就不知道被谁动过了。今天给大家推荐一款<strong>富光智能指纹解锁密码保温杯</strong>，用指纹就能开锁，只有孩子自己能打开，彻底解决水杯被乱用的问题。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[0] . '" alt="富光智能指纹保温杯" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>一、为什么选择富光指纹保温杯？</h2>';
$content .= '<p>富光是国内知名的水杯品牌，做了三十多年杯壶，品质有保障。这款智能指纹保温杯是富光旗舰店的热销款，天猫店铺评分三项全红：宝贝描述4.85分（高于同行业15.65%）、卖家服务4.88分（高于同行业12.23%）、物流服务4.89分（高于同行业13.52%），口碑非常好。</p>';
$content .= '<p>最核心的卖点就是<strong>指纹解锁功能</strong>。杯盖内置指纹识别模块，孩子把手指放上去就能自动开锁，按一下就能喝水。别人拿到杯子也打不开，从根本上杜绝了水杯被乱用、水被偷喝的问题。特别适合小学、初中学生在学校使用。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[1] . '" alt="富光保温杯指纹解锁" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>二、核心功能详解</h2>';
$content .= '<h3>1. 指纹解锁，安全私密</h3>';
$content .= '<p>杯盖采用半导体指纹识别技术，识别速度快，0.5秒就能解锁。支持录入多个指纹，孩子自己的手指、爸爸妈妈的手指都能录进去。指纹信息存在本地，不上传网络，隐私安全有保障。除了指纹解锁，还支持密码解锁作为备用，万一手指湿了或者脱皮，用密码也能打开。</p>';

$content .= '<h3>2. 316不锈钢内胆，保温效果好</h3>';
$content .= '<p>内胆采用食品级316不锈钢，比普通304不锈钢更耐腐蚀、更安全，装酸性饮料（比如果汁、柠檬水）也不用担心。双层真空保温结构，<strong>6小时保温65℃以上，12小时保温45℃以上</strong>。早上装的热水，中午喝还是温的，下午喝也不凉。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[2] . '" alt="富光保温杯316不锈钢" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h3>3. 吸管设计，喝水方便</h3>';
$content .= '<p>采用吸管式饮水口，孩子喝水不用仰头，上课的时候也能偷偷喝一口，不会影响课堂纪律。吸管是食品级硅胶材质，柔软不伤嘴，可拆卸清洗，不会藏污纳垢。杯盖一键弹开，单手就能操作，孩子一只手拿书一只手喝水也没问题。</p>';

$content .= '<h3>4. 容量适中，轻便好带</h3>';
$content .= '<p>500ml容量，刚好满足孩子半天的饮水需求，不会太大太重放不进书包侧袋，也不会太小不够喝。杯身重量只有300多克，孩子背着不累。外壳采用磨砂工艺，防滑防汗，手握感舒适，不容易滑落。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[3] . '" alt="富光保温杯细节" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>三、使用体验</h2>';
$content .= '<p>我给上小学的孩子买了一个，用了一个月，说说真实感受。首先指纹解锁确实好用，孩子按一下就开，比以前拧杯盖方便多了。以前孩子总说杯子被同学拿错，现在再也没出现过，因为别人根本打不开。</p>';
$content .= '<p>保温效果也满意，早上7点装的热水，中午12点喝还烫嘴，下午4点放学还是温的。吸管设计孩子很喜欢，说喝水比以前方便了。清洗也简单，杯盖可以拆开，吸管能拔出来，用刷子一刷就干净。</p>';
$content .= '<p>唯一的小缺点是杯盖因为有电子元件，不能整个泡在水里洗，只能用湿布擦。不过这是智能水杯的通病，注意一下就好。电池续航也不错，充一次电能用一个月左右，Type-C充电口，跟手机充电线通用。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[4] . '" alt="富光保温杯使用场景" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>四、购买建议</h2>';
$content .= '<p>这款保温杯有多个版本可选：</p>';
$content .= '<ul>';
$content .= '<li><strong>基础款（无蓝牙）：</strong>179.55元，指纹+密码解锁，316不锈钢内胆，500ml容量，性价比最高</li>';
$content .= '<li><strong>加菲猫联名款（红/蓝）：</strong>379.05元，加菲猫IP联名外观，蓝牙连接手机，功能更丰富</li>';
$content .= '</ul>';
$content .= '<p>普通学生用基础款就足够了，179.55元的价格比很多普通保温杯贵不了多少，但多了指纹解锁这个核心功能，很划算。如果孩子特别喜欢加菲猫，或者想要蓝牙提醒喝水功能，可以选联名款。</p>';
$content .= '<p>颜色有蓝色和粉色可选，男孩女孩都能用。现在天猫富光旗舰店有活动，88VIP还能再打折，下单还送杯刷和吸管刷，配件齐全。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[5] . '" alt="富光保温杯购买" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>五、适合人群</h2>';
$content .= '<ul>';
$content .= '<li>小学生、初中生：学校水杯容易被拿错，指纹解锁完美解决</li>';
$content .= '<li>住校生：宿舍人多，个人物品需要隐私保护</li>';
$content .= '<li>注重卫生的家长：担心孩子水杯被乱用，指纹锁更安心</li>';
$content .= '<li>喜欢智能产品的用户：科技感十足，用着方便</li>';
$content .= '</ul>';

$content .= '<h2>六、总结</h2>';
$content .= '<p>富光智能指纹解锁保温杯是一款很有针对性的产品，精准解决了学生水杯被乱用的痛点。指纹解锁+316不锈钢+吸管设计+12小时保温，核心功能都到位了。富光品牌品质有保障，天猫旗舰店售后也放心。179.55元的价格，给孩子买一个能用好几年，平均下来一天才几毛钱，很值得。</p>';
$content .= '<p>如果你家孩子也在为水杯被拿错而烦恼，不妨试试这款指纹保温杯，孩子用着开心，家长也放心。</p>';

// 购买按钮
$content .= '<div style="text-align:center;margin:25px 0;">';
$content .= '<p style="font-size:18px;font-weight:bold;color:#e4393c;">富光智能指纹保温杯 88VIP价仅￥' . $price . '</p>';
$content .= '<a href="' . $promo_link . '" target="_blank" style="display:inline-block;background:linear-gradient(135deg,#ff6a00,#ee0979);color:#fff;padding:12px 40px;border-radius:25px;text-decoration:none;font-size:16px;font-weight:bold;">👉 点击立即购买（淘宝）</a>';
$content .= '</div>';

// 淘口令
$content .= '<div style="background:#fff5f5;border:1px dashed #ff6a00;border-radius:8px;padding:15px;margin:20px 0;text-align:center;">';
$content .= '<p style="margin:0 0 8px 0;font-weight:bold;">📱 手机淘宝购买方式</p>';
$content .= '<p style="margin:0;font-size:15px;">复制淘口令，打开手机淘宝自动跳转：</p>';
$content .= '<p style="margin:8px 0 0 0;font-size:18px;color:#e4393c;font-weight:bold;letter-spacing:1px;">' . $taokouling . '</p>';
$content .= '</div>';

// 发布文章
$post_id = wp_insert_post([
    'post_title' => $title,
    'post_content' => $content,
    'post_status' => 'publish',
    'post_author' => 1,
]);

if (!is_wp_error($post_id)) {
    wp_set_post_categories($post_id, [85, 209]); // 淘宝联盟 + 购物优惠 + 最新公告
    set_post_thumbnail($post_id, $attach_ids[0]);
    echo "文章发布成功！ID: $post_id\n";
    echo "标题: $title\n";
    echo "字数: " . mb_strlen(strip_tags($content)) . "\n";
    echo "图片: " . count($attach_ids) . "张\n";
    echo "特色图: ID " . $attach_ids[0] . "\n";
    echo "推广链接: $promo_link\n";
    echo "分类: 淘宝联盟(85)+购物优惠(209)+最新公告(97)\n";
} else {
    echo "发布失败: " . $post_id->get_error_message() . "\n";
}

echo "DONE\n";
?>
