<?php
/**
 * WordPress 通用发布辅助函数库
 * 用法：在发布脚本中 require_once '/root/wp_publish_helper.php';
 */
date_default_timezone_set('Asia/Shanghai');

// 加载WordPress
require_once('/www/wwwroot/laobaixingbanshi.com/wp-load.php');

/**
 * 从URL下载图片并上传到WP媒体库
 * @param string $image_url 图片URL
 * @param int $post_id 关联文章ID（可选）
 * @return int|false 附件ID，失败返回false
 */
function upload_image_from_url($image_url, $post_id = 0) {
    if (empty($image_url)) return false;
    
    // 下载图片
    $tmp = download_url($image_url);
    if (is_wp_error($tmp)) {
        // 尝试用curl
        $ch = curl_init($image_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $img_data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_code != 200 || empty($img_data)) return false;
        
        $tmp = sys_get_temp_dir() . '/wp_img_' . uniqid() . '.jpg';
        file_put_contents($tmp, $img_data);
    }
    
    // 获取文件名
    $parsed = parse_url($image_url);
    $path_info = pathinfo($parsed['path']);
    $filename = !empty($path_info['basename']) ? $path_info['basename'] : 'image_' . uniqid() . '.jpg';
    // 清理文件名
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    if (strlen($filename) > 100) $filename = substr($filename, -100);
    
    $file_array = array(
        'name' => $filename,
        'tmp_name' => $tmp
    );
    
    // 检查文件类型
    $wp_filetype = wp_check_filetype($filename, null);
    if (empty($wp_filetype['type'])) {
        $wp_filetype = array('type' => 'image/jpeg', 'ext' => 'jpg');
    }
    
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    $attach_id = wp_insert_attachment($attachment, $tmp, $post_id);
    if (is_wp_error($attach_id) || !$attach_id) {
        @unlink($tmp);
        return false;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $tmp);
    wp_update_attachment_metadata($attach_id, $attach_data);
    
    @unlink($tmp);
    return $attach_id;
}

/**
 * 从本地文件上传图片到WP媒体库
 * @param string $local_path 本地文件路径
 * @param int $post_id 关联文章ID
 * @return int|false 附件ID
 */
function upload_image_from_local($local_path, $post_id = 0) {
    if (!file_exists($local_path)) return false;
    
    $filename = basename($local_path);
    $wp_filetype = wp_check_filetype($filename, null);
    if (empty($wp_filetype['type'])) {
        $wp_filetype = array('type' => 'image/jpeg', 'ext' => 'jpg');
    }
    
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    $attach_id = wp_insert_attachment($attachment, $local_path, $post_id);
    if (is_wp_error($attach_id) || !$attach_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $local_path);
    wp_update_attachment_metadata($attach_id, $attach_data);
    
    return $attach_id;
}

/**
 * 发布文章
 * @param array $args {
 *     @type string $title 标题
 *     @type string $content 正文HTML
 *     @type array $categories 分类ID数组
 *     @type string $featured_image_url 特色图URL（可选）
 *     @type int $featured_image_id 特色图附件ID（可选，优先于URL）
 *     @type string $post_date 发布日期 'Y-m-d H:i:s'（可选，默认当前时间）
 *     @type string $post_status 状态（默认publish）
 * }
 * @return int|false 文章ID
 */
function publish_article($args) {
    $defaults = array(
        'title' => '',
        'content' => '',
        'categories' => array(),
        'featured_image_url' => '',
        'featured_image_id' => 0,
        'post_date' => '',
        'post_status' => 'publish'
    );
    $args = wp_parse_args($args, $defaults);
    
    if (empty($args['title']) || empty($args['content'])) {
        echo "ERROR: title or content empty\n";
        return false;
    }
    
    // 去重检查
    global $wpdb;
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_status='publish' AND post_type='post' LIMIT 1",
        $args['title']
    ));
    if ($existing) {
        echo "DUPLICATE: title already exists (ID={$existing})\n";
        return false;
    }
    
    $post_data = array(
        'post_title' => $args['title'],
        'post_content' => $args['content'],
        'post_status' => $args['post_status'],
        'post_type' => 'post',
        'post_author' => 1,
        'post_category' => $args['categories']
    );
    
    if (!empty($args['post_date'])) {
        $post_data['post_date'] = $args['post_date'];
        $post_data['post_date_gmt'] = get_gmt_from_date($args['post_date']);
    }
    
    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id) || !$post_id) {
        echo "ERROR: wp_insert_post failed\n";
        return false;
    }
    
    // 设置分类
    if (!empty($args['categories'])) {
        wp_set_post_categories($post_id, $args['categories']);
    }
    
    // 设置特色图
    $thumb_id = intval($args['featured_image_id']);
    if (!$thumb_id && !empty($args['featured_image_url'])) {
        $thumb_id = upload_image_from_url($args['featured_image_url'], $post_id);
    }
    if ($thumb_id) {
        set_post_thumbnail($post_id, $thumb_id);
    }
    
    return $post_id;
}

/**
 * 检查标题是否已存在
 * @param string $title
 * @return bool
 */
function title_exists($title) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_status='publish' AND post_type='post' LIMIT 1",
        $title
    )) ? true : false;
}

/**
 * 模糊搜索标题（去重用）
 * @param string $keyword
 * @return array
 */
function search_titles($keyword) {
    global $wpdb;
    return $wpdb->get_col($wpdb->prepare(
        "SELECT post_title FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_status='publish' AND post_type='post'",
        '%' . $wpdb->esc_like($keyword) . '%'
    ));
}

/**
 * 给图片HTML加响应式样式
 * @param string $img_html
 * @return string
 */
function add_responsive_style($img_html) {
    if (strpos($img_html, 'max-width:100%') !== false) return $img_html;
    return str_replace('<img ', '<img style="max-width:100%;height:auto;display:block;margin:15px auto;border-radius:8px;" ', $img_html);
}

/**
 * 生成购买按钮HTML
 * @param string $url 链接
 * @param string $text 按钮文字
 * @param string $color 按钮颜色 (orange/blue/green/pink/yellow)
 * @return string
 */
function buy_button($url, $text, $color = 'orange') {
    $colors = array(
        'orange' => '#ff6a00',
        'blue' => '#00a0e9',
        'green' => '#07c160',
        'pink' => '#ff4d88',
        'yellow' => '#ff9500'
    );
    $bg = isset($colors[$color]) ? $colors[$color] : $colors['orange'];
    return '<a href="' . esc_url($url) . '" target="_blank" style="display:inline-block;background:' . $bg . ';color:#fff;padding:12px 30px;border-radius:8px;text-decoration:none;font-size:16px;font-weight:bold;margin:10px 5px;">' . esc_html($text) . '</a>';
}

echo "WP_PUBLISH_HELPER_LOADED\n";
