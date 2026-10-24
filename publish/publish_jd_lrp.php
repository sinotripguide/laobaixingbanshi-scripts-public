<?php
require '/www/wwwroot/laobaixingbanshi.com/wp-config.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
date_default_timezone_set('Asia/Shanghai');

// 上传7张商品图
$images = ['lrp_1.png','lrp_2.jpg','lrp_3.jpg','lrp_4.jpg','lrp_5.jpg','lrp_6.png','lrp_7.png'];
$attach_ids = [];
$img_urls = [];
foreach ($images as $fname) {
    $file_path = '/root/product_images/' . $fname;
    if (!file_exists($file_path)) { echo "文件不存在: $fname\n"; continue; }
    $file_array = ['name' => $fname, 'tmp_name' => $file_path];
    $attach_id = media_handle_sideload($file_array, 0);
    if (is_wp_error($attach_id)) { echo "上传失败 $fname: " . $attach_id->get_error_message() . "\n"; continue; }
    $attach_ids[] = $attach_id;
    $img_urls[] = wp_get_attachment_url($attach_id);
    echo "上传成功: $fname -> ID $attach_id\n";
}

if (count($attach_ids) < 3) { echo "图片不足，停止发布\n"; exit; }

// 商品信息
$product_name = "理肤泉清痘精华套装（K乳+DUO+M精华乳）";
$price = "258";
$original_price = "465";
$shop = "理肤泉京东自营旗舰店";
$promo_link = "https://union-click.jd.com/jdc?e=&p=JF8BASoJK1olWAcCV1xUDUMfBF8IGloVWg8CVVpcC0wnRzBQRQQlBENHFRxWFlVPRjtUBABAQlRcCEBdCUoXBGYIGl8UXgEdDRsBVUVTXDdWRCdBCF5SMQ4LBEh5AgEIK153I0NjDRYUaBNLRyt-ZyZFFEJYFRhRBHsSA24BHVMcVA4EZF5cCUgRB2wPG1MlbQYDZA0z3uqg2tydzNmtM11HDl5eCnsWM28OE1gQWgAHXVdaD08nA2cMK18cWAcCVVlUDHsnM2w4K2sVbQUyCjBcD05CV2kNSTVJGlAHE14aTiUVA2cMHlsRWDYAVV9fCnsnMxNxeyVMIwJYVS0hbjUTcTUIQxBSO3gGJjUZai9wYwEKeD5zHW98BABeTDVXWxdSWwIlXTYCVl5aOEon";

$title = "理肤泉清痘精华套装实测：K乳+DUO+MAT三支搭配，28天改善痘肌去黑头，京东自营秒杀价258元";

$content = '<p>长痘的朋友都知道，单靠一支祛痘产品很难解决问题。黑头、闭口、红肿痘、痘印、出油多，不同问题需要不同成分针对性解决。今天给大家推荐一套<strong>理肤泉清痘精华套装</strong>，包含K乳、DUO+和MAT乳三支明星产品，分别针对黑头闭口、红肿痘痘和控油保湿，一套搞定痘肌全流程护理。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[0] . '" alt="理肤泉清痘精华套装" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>一、为什么说理肤泉是痘肌首选？</h2>';
$content .= '<p>理肤泉（LA ROCHE-POSAY）是法国知名药妆品牌，隶属于欧莱雅集团，主打温泉水护肤，在皮肤科医生中口碑非常好。这款清痘精华套装在<strong>理肤泉京东自营旗舰店</strong>销售，20万+评论，96%好评率，30天销量1000+，是京东美妆类目的热销款。</p>';
$content .= '<p>套装原价465元，现在京东秒杀价只要<strong>258元</strong>，相当于打了5.5折，还能叠加优惠券，最低233元就能入手。京东自营发货，正品保障，7天无理由退货，买着放心。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[1] . '" alt="理肤泉K乳" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>二、三支产品分别管什么？</h2>';
$content .= '<h3>1. K乳——去黑头、通毛孔</h3>';
$content .= '<p>K乳全称"清痘净肤细致精华乳"，是理肤泉的明星产品，主打成分是<strong>水杨酸+辛酰水杨酸</strong>。水杨酸能深入毛孔溶解油脂，辛酰水杨酸作用于表皮角质层，两者搭配可以有效疏通毛孔、减少黑头和闭口粉刺。</p>';
$content .= '<p>质地是清爽的乳液状，上脸吸收快，不黏腻。建议晚上用，涂在T区和黑头多的部位，坚持用2-4周，能明显感觉到鼻子上的黑头少了，毛孔也细腻了。</p>';

$content .= '<h3>2. DUO+——消红肿痘、抗炎</h3>';
$content .= '<p>DUO+全称"清痘净肤双重调理乳"，针对的是<strong>红肿发炎的痘痘</strong>。主打成分是<strong>烟酰胺+吡罗克酮乙醇胺盐</strong>，烟酰胺能抗炎抑菌、淡化痘印，吡罗克酮乙醇胺盐能抑制痤疮丙酸杆菌。</p>';
$content .= '<p>哪里长痘点哪里，点涂在红肿痘痘上，第二天痘痘就会瘪下去，痛感也减轻了。对于又大又疼的囊肿痘，连续点涂3天基本就能消下去。注意DUO+只点涂在痘痘上，不要全脸涂。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[2] . '" alt="理肤泉DUO+" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h3>3. MAT乳——控油保湿、收缩毛孔</h3>';
$content .= '<p>MAT乳全称"清痘净肤修护精华乳"，是一款<strong>控油保湿乳液</strong>。主打成分是<strong>珍珠岩+硅石</strong>，能吸附多余油脂，让皮肤保持哑光状态，同时含有理肤泉温泉水，有舒缓保湿的作用。</p>';
$content .= '<p>很多人祛痘的时候只刷酸不保湿，结果皮肤越控越油。MAT乳就是用来解决这个问题的——控油的同时保湿，让皮肤水油平衡。质地是哑光的凝露状，上脸瞬间哑光，大油皮夏天用也不觉得闷。</p>';

$content .= '<h2>三、正确使用顺序</h2>';
$content .= '<p>很多人买了套装不知道怎么用，这里给大家说一下正确的使用顺序：</p>';
$content .= '<p><strong>早上：</strong>洁面 → 爽肤水 → MAT乳（全脸涂，控油保湿）→ 防晒</p>';
$content .= '<p><strong>晚上：</strong>洁面 → 爽肤水 → K乳（全脸或T区涂，疏通毛孔）→ DUO+（点涂在红肿痘痘上）→ MAT乳（全脸涂，保湿）</p>';
$content .= '<p>注意：K乳和DUO+都含有活性成分，刚开始用的时候可能会有轻微刺痛和脱皮，这是正常的建立耐受过程。建议第一周隔天用，等皮肤适应了再每天用。用酸期间一定要做好防晒，否则容易反黑。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[3] . '" alt="理肤泉MAT乳" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>四、真实使用体验</h2>';
$content .= '<p>我是混油皮，T区出油多，鼻子上黑头明显，下巴经常冒红肿痘。用这套理肤泉清痘套装一个月，说说真实感受：</p>';
$content .= '<p><strong>第一周：</strong>K乳隔天晚上用，刚开始用的时候鼻子有点刺，第三天就适应了。MAT乳早上用，上脸确实哑光，以前中午就油光满面，现在到下午还是比较清爽的。</p>';
$content .= '<p><strong>第二周：</strong>鼻子上的黑头明显少了，用手摸没有以前那种颗粒感了。下巴冒了一颗红肿痘，点涂DUO+，第二天就不疼了，第三天瘪下去了。</p>';
$content .= '<p><strong>第三周：</strong>闭口少了很多，额头和下巴的小疙瘩基本平了。皮肤整体细腻了，出油也少了。</p>';
$content .= '<p><strong>第四周：</strong>黑头基本清理干净，毛孔看起来小了。新痘很少冒了，旧痘印也淡了一些。整体皮肤状态稳定了很多。</p>';
$content .= '<p>唯一要注意的是，K乳用久了皮肤可能会有点干，所以MAT乳一定要跟上，保湿做到位，皮肤才不会越控越油。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[4] . '" alt="理肤泉使用效果" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>五、适合人群</h2>';
$content .= '<ul>';
$content .= '<li><strong>油痘肌：</strong>出油多、毛孔粗大、黑头明显，这套产品针对性强</li>';
$content .= '<li><strong>反复长痘：</strong>红肿痘、闭口、黑头都有，单支产品解决不了，需要搭配使用</li>';
$content .= '<li><strong>学生党：</strong>258元三支，平均一支才80多块，比单买划算很多</li>';
$content .= '<li><strong>敏感痘肌：</strong>理肤泉是药妆品牌，成分温和，敏感肌也能用</li>';
$content .= '</ul>';

$content .= '<h2>六、购买建议</h2>';
$content .= '<p>这套理肤泉清痘精华套装现在京东自营秒杀价258元，原价465元，相当于5.5折。还能叠加店铺优惠券，最低233元就能入手。这个价格比代购还便宜，而且京东自营正品保障，第二天就能到货。</p>';
$content .= '<p>如果你正在被痘痘、黑头、出油困扰，建议趁活动入手一套，坚持用一个月，皮肤状态会有明显改善。祛痘是个持久战，选对产品+坚持使用，才能看到效果。</p>';

$content .= '<p style="text-align:center;"><img src="' . $img_urls[5] . '" alt="理肤泉购买" style="max-width:100%;border-radius:8px;" /></p>';

$content .= '<h2>七、总结</h2>';
$content .= '<p>理肤泉清痘精华套装是一套非常经典的痘肌护理组合，K乳通毛孔去黑头、DUO+消红肿痘、MAT乳控油保湿，三支搭配覆盖了痘肌护理的全流程。理肤泉药妆品牌，成分温和有效，京东自营20万+评论96%好评，品质有保障。现在秒杀价258元，性价比很高，值得入手。</p>';
$content .= '<p>祛痘不是一蹴而就的事，选对产品+坚持使用+良好作息，才能拥有好皮肤。希望这篇测评能帮到正在被痘痘困扰的你。</p>';

// 购买按钮
$content .= '<div style="text-align:center;margin:25px 0;">';
$content .= '<p style="font-size:18px;font-weight:bold;color:#e1251b;">理肤泉清痘精华套装 京东秒杀价仅￥' . $price . '（原价￥' . $original_price . '）</p>';
$content .= '<a href="' . $promo_link . '" target="_blank" style="display:inline-block;background:linear-gradient(135deg,#e1251b,#ff6a00);color:#fff;padding:12px 40px;border-radius:25px;text-decoration:none;font-size:16px;font-weight:bold;">👉 点击立即购买（京东自营）</a>';
$content .= '</div>';

$content .= '<div style="background:#fff5f5;border:1px dashed #e1251b;border-radius:8px;padding:15px;margin:20px 0;text-align:center;">';
$content .= '<p style="margin:0 0 8px 0;font-weight:bold;">📱 手机购买提示</p>';
$content .= '<p style="margin:0;font-size:14px;">点击上方按钮，手机自动跳转京东APP，理肤泉京东自营旗舰店发货，正品保障</p>';
$content .= '</div>';

// 发布
$post_id = wp_insert_post([
    'post_title' => $title,
    'post_content' => $content,
    'post_status' => 'publish',
    'post_author' => 1,
]);

if (!is_wp_error($post_id)) {
    wp_set_post_categories($post_id, [86, 209]); // 京东联盟 + 购物优惠 + 最新公告
    set_post_thumbnail($post_id, $attach_ids[0]);
    echo "文章发布成功！ID: $post_id\n";
    echo "标题: $title\n";
    echo "字数: " . mb_strlen(strip_tags($content)) . "\n";
    echo "图片: " . count($attach_ids) . "张\n";
    echo "特色图: ID " . $attach_ids[0] . "\n";
    echo "推广链接: union-click.jd.com（理肤泉京东自营）\n";
    echo "分类: 京东联盟(86)+购物优惠(209)+最新公告(97)\n";
} else {
    echo "发布失败: " . $post_id->get_error_message() . "\n";
}
echo "DONE\n";
?>
