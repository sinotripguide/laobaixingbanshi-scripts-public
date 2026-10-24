<?php
/**
 * 配置文件模板（config.example.php）
 * 部署时复制为 config.php 并填写真实值；config.php 已被 .gitignore 排除，不会提交。
 */
return [
    // WordPress 数据库连接（生产服务器环境变量注入）
    'db_host'      => '{DB_HOST}',        // 通常 127.0.0.1
    'db_user'      => '{DB_USER}',
    'db_pass'      => '{DB_PASSWORD}',
    'db_name'      => '{DB_NAME}',
    // 百度搜索推送 token
    'baidu_token'  => '{BAIDU_TOKEN}',
    // IndexNow 密钥
    'indexnow_key' => '{INDEXNOW_KEY}',
    // 联盟推广位参数（按实际账号填写）
    'taobao_pid'   => '{TAOBAO_PID}',    // 淘宝/飞猪推广位 mm_xxx
    'ctrip_aid'    => '{CTRIP_AID}',     // 携程 allianceid
    'ctrip_sid'    => '{CTRIP_SID}',     // 携程 sid
    'ctrip_ouid'   => '{CTRIP_OUID}',    // 携程 ouid
    'ly_refid'     => '{LY_REFID}',      // 同程 refId
    'jd_siteid'    => '{JD_SITEID}',     // 京东联盟 siteId
    'pdd_pid'      => '{PDD_PID}',       // 拼多多 p_id
    'vip_chantag'  => '{VIP_CHANTAG}',   // 唯品会 chanTag
];
