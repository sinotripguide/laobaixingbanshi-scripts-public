<?php
require '/www/wwwroot/laobaixingbanshi.com/wp-config.php';
date_default_timezone_set('Asia/Shanghai');

// 补全3个遗漏板块文章
$posts = [
    [
        'title' => '2026年个体工商户注册全流程：线上线下办理步骤、材料清单和费用明细',
        'cat' => [37], // 创业办事 + 最新公告
        'content' => '<p>想创业开个小店？个体工商户注册是第一步。本文详细介绍2026年个体工商户注册的全流程，包括线上办理和线下办理两种方式、所需材料清单、费用明细，以及注册后的税务登记和银行开户注意事项。</p>
<h2>一、个体工商户注册条件</h2>
<p>注册个体工商户需要满足以下条件：年满18周岁，有完全民事行为能力；有固定的经营场所；有明确的经营范围；不属于法律法规禁止从事营利性活动的人员（如公务员、现役军人等）。</p>
<h2>二、线上办理流程（推荐）</h2>
<p>第一步：登录当地市场监督管理局官网或"一网通办"平台，注册账号并实名认证。第二步：进入"个体工商户设立登记"模块，填写经营者信息、经营场所信息、经营范围等。第三步：上传身份证、经营场所证明（房产证或租赁合同）等材料。第四步：提交审核，一般1-3个工作日出结果。第五步：审核通过后，可选择邮寄营业执照或到窗口领取。</p>
<h2>三、线下办理流程</h2>
<p>携带身份证原件及复印件、经营场所证明、一寸照片到经营场所所在地的市场监督管理所（工商所）办理。填写《个体工商户开业登记申请书》，工作人员审核材料后当场或3个工作日内发放营业执照。</p>
<h2>四、所需材料清单</h2>
<ul>
<li>经营者身份证原件及复印件</li>
<li>经营场所证明（房产证或租赁合同，部分地区允许住宅注册）</li>
<li>一寸免冠照片（部分地区需要）</li>
<li>《个体工商户开业登记申请书》（现场填写）</li>
<li>经营范围涉及前置审批的，需提供相关许可证</li>
</ul>
<h2>五、费用明细</h2>
<p>个体工商户注册登记费：免费（全国已取消）。营业执照工本费：免费。刻章费用：200-500元（公章、财务章、发票章，部分地区免费送章）。银行开户费：0-300元/年（不同银行收费不同）。税务登记：免费。</p>
<h2>六、注册后必做事项</h2>
<p>1. 刻章：拿到营业执照后到指定刻章点刻制公章、财务章、发票章。2. 税务登记：领取营业执照后30日内到税务局办理税务登记，核定税种。3. 银行开户：选择一家银行开立基本存款账户，用于日常经营收支。4. 社保开户：如果有雇工，需要在30日内办理社保登记。5. 记账报税：个体工商户也需要按时记账报税，小规模纳税人季度销售额30万以下免征增值税。</p>
<h2>七、常见问题</h2>
<p>Q：住宅可以注册个体工商户吗？A：部分地区允许"住改商"，需要提供居委会或业主委员会出具的同意证明，具体咨询当地市场监管部门。Q：个体工商户需要记账吗？A：需要，个体工商户应当建立账簿，如实记载经营收支。Q：个体工商户可以变更经营者吗？A：个体工商户变更经营者的，应当在办理注销登记后，由新的经营者重新申请办理注册登记。</p>
<p>以上就是2026年个体工商户注册的全流程，希望对准备创业的朋友有所帮助。各地政策可能略有差异，具体以当地市场监督管理部门要求为准。</p>'
    ],
    [
        'title' => '2026年社保缴费基数调整：职工社保和灵活就业社保缴费标准对比',
        'cat' => [10], // 社保医保 + 最新公告
        'content' => '<p>每年社保缴费基数都会调整，直接影响我们每个月交多少钱、退休后领多少养老金。本文详细解读2026年社保缴费基数调整政策，对比职工社保和灵活就业社保的缴费标准，帮你搞清楚社保怎么交最划算。</p>
<h2>一、2026年社保缴费基数上下限</h2>
<p>社保缴费基数上下限根据上年度全口径城镇单位就业人员平均工资确定。2026年大部分地区的缴费基数下限在4000-4500元左右，上限在20000-25000元左右。具体标准由各省人社部门公布，一般在每年6-7月份公布，7月1日起执行。</p>
<h2>二、职工社保缴费标准（单位+个人）</h2>
<table style="width:100%;border-collapse:collapse;margin:15px 0;">
<tr style="background:#f5f5f5;"><td style="padding:8px;border:1px solid #ddd;">险种</td><td style="padding:8px;border:1px solid #ddd;">单位比例</td><td style="padding:8px;border:1px solid #ddd;">个人比例</td><td style="padding:8px;border:1px solid #ddd;">合计</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">养老保险</td><td style="padding:8px;border:1px solid #ddd;">16%</td><td style="padding:8px;border:1px solid #ddd;">8%</td><td style="padding:8px;border:1px solid #ddd;">24%</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">医疗保险</td><td style="padding:8px;border:1px solid #ddd;">8-10%</td><td style="padding:8px;border:1px solid #ddd;">2%</td><td style="padding:8px;border:1px solid #ddd;">10-12%</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">失业保险</td><td style="padding:8px;border:1px solid #ddd;">0.5-1%</td><td style="padding:8px;border:1px solid #ddd;">0.5%</td><td style="padding:8px;border:1px solid #ddd;">1-1.5%</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">工伤保险</td><td style="padding:8px;border:1px solid #ddd;">0.2-1.9%</td><td style="padding:8px;border:1px solid #ddd;">0</td><td style="padding:8px;border:1px solid #ddd;">0.2-1.9%</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">生育保险</td><td style="padding:8px;border:1px solid #ddd;">0.5-1%</td><td style="padding:8px;border:1px solid #ddd;">0</td><td style="padding:8px;border:1px solid #ddd;">0.5-1%</td></tr>
</table>
<p>以缴费基数5000元为例，单位每月交约1300元，个人每月交约525元，合计约1825元。</p>
<h2>三、灵活就业社保缴费标准</h2>
<p>灵活就业人员可以以个人身份参加职工养老保险和职工医疗保险。养老保险缴费比例为20%（全部由个人承担），医疗保险缴费比例为8-10%（全部由个人承担）。以缴费基数5000元为例，养老保险每月交1000元，医疗保险每月交400-500元，合计每月1400-1500元。</p>
<h2>四、职工社保vs灵活就业社保对比</h2>
<p><strong>缴费金额：</strong>职工社保单位承担大部分，个人只交小部分；灵活就业社保全部由个人承担，缴费压力更大。<strong>保障范围：</strong>职工社保包含五险（养老、医疗、失业、工伤、生育）；灵活就业社保一般只有养老和医疗两险。<strong>退休年龄：</strong>职工社保女性50岁退休（女干部55岁），男性60岁；灵活就业社保女性55岁退休，男性60岁。<strong>养老金计算：</strong>两者养老金计算方式相同，缴费基数越高、缴费年限越长，养老金越多。</p>
<h2>五、社保缴费常见问题</h2>
<p>Q：社保断交有什么影响？A：养老保险断交影响不大，累计缴满15年即可；医疗保险断交超过3个月，连续缴费年限清零，重新计算等待期；生育保险断交可能影响生育津贴领取。Q：可以一次性补缴社保吗？A：养老保险一般不允许一次性补缴，只能逐年缴纳；医疗保险退休时缴费年限不足的，可以一次性补缴。Q：异地社保怎么转移？A：在新参保地缴费后，通过国家社会保险公共服务平台或"掌上12333"APP申请社保转移，一般45个工作日内完成。</p>
<p>社保是我们生活的重要保障，建议大家按时足额缴纳。各地缴费标准不同，具体以当地人社部门公布为准。</p>'
    ],
    [
        'title' => '2026年商户收款码办理指南：微信/支付宝/聚合支付费率对比和申请流程',
        'cat' => [46], // 商户收款 + 最新公告
        'content' => '<p>开店做生意，收款码是必备工具。本文详细介绍2026年商户收款码的办理指南，包括微信收款码、支付宝收款码、聚合支付收款码的费率对比、申请流程和到账时间，帮你选择最适合的收款方式。</p>
<h2>一、个人收款码vs商户收款码</h2>
<p>2022年3月1日起，个人收款码不得用于经营性收款。商户必须使用商户收款码（经营性质收款码）。个人收款码和商户收款码的区别：个人收款码免费，但有年限额和单笔限额，不能信用卡付款；商户收款码有手续费，但支持信用卡、花呗付款，无经营限额，可申请经营贷款。</p>
<h2>二、微信商户收款码</h2>
<p><strong>费率：</strong>0.6%（标准费率），部分行业可申请0.38%优惠费率。<strong>到账时间：</strong>T+1到账（次日到账），可申请自动提现。<strong>申请条件：</strong>有营业执照（个体工商户或企业），有固定经营场所。<strong>申请流程：</strong>关注"微信支付商家助手"公众号→点击"成为商家"→填写商户信息→上传营业执照、身份证、经营场所照片→审核通过后即可使用。</p>
<h2>三、支付宝商户收款码</h2>
<p><strong>费率：</strong>0.6%（标准费率），部分行业可申请0.38%优惠费率。<strong>到账时间：</strong>T+1到账，可申请自动提现。<strong>申请条件：</strong>有营业执照，有固定经营场所。<strong>申请流程：</strong>打开支付宝→搜索"商家服务"→点击"立即开通"→填写商户信息→上传营业执照、身份证、经营场所照片→审核通过后即可使用。</p>
<h2>四、聚合支付收款码</h2>
<p>聚合支付收款码支持微信、支付宝、云闪付、花呗、信用卡等多种支付方式，一码多用。<strong>费率：</strong>0.38%-0.6%，根据行业和交易量协商。<strong>到账时间：</strong>T+1到账，部分平台支持D+0实时到账（额外收费）。<strong>主流平台：</strong>收钱吧、拉卡拉、汇付天下、银盛支付等。<strong>申请流程：</strong>选择聚合支付平台→联系代理商或在线申请→提交营业执照、身份证、银行卡、经营场所照片→审核通过后领取收款码牌。</p>
<h2>五、三种收款方式对比</h2>
<table style="width:100%;border-collapse:collapse;margin:15px 0;">
<tr style="background:#f5f5f5;"><td style="padding:8px;border:1px solid #ddd;">对比项</td><td style="padding:8px;border:1px solid #ddd;">微信商户码</td><td style="padding:8px;border:1px solid #ddd;">支付宝商户码</td><td style="padding:8px;border:1px solid #ddd;">聚合支付码</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">费率</td><td style="padding:8px;border:1px solid #ddd;">0.38-0.6%</td><td style="padding:8px;border:1px solid #ddd;">0.38-0.6%</td><td style="padding:8px;border:1px solid #ddd;">0.38-0.6%</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">支付方式</td><td style="padding:8px;border:1px solid #ddd;">微信+信用卡</td><td style="padding:8px;border:1px solid #ddd;">支付宝+信用卡</td><td style="padding:8px;border:1px solid #ddd;">全渠道支持</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">到账时间</td><td style="padding:8px;border:1px solid #ddd;">T+1</td><td style="padding:8px;border:1px solid #ddd;">T+1</td><td style="padding:8px;border:1px solid #ddd;">T+1/D+0</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">经营贷款</td><td style="padding:8px;border:1px solid #ddd;">支持</td><td style="padding:8px;border:1px solid #ddd;">支持</td><td style="padding:8px;border:1px solid #ddd;">部分支持</td></tr>
<tr><td style="padding:8px;border:1px solid #ddd;">适合场景</td><td style="padding:8px;border:1px solid #ddd;">微信用户多</td><td style="padding:8px;border:1px solid #ddd;">支付宝用户多</td><td style="padding:8px;border:1px solid #ddd;">全渠道收款</td></tr>
</table>
<h2>六、办理注意事项</h2>
<ul>
<li>必须使用真实营业执照申请，不得借用他人资质</li>
<li>经营场所照片要真实，不得伪造</li>
<li>收款码不得用于违法违规交易，否则会被冻结账户</li>
<li>定期核对账单，确保资金安全</li>
<li>注意保护收款码，避免被恶意替换</li>
<li>费率可以和代理商协商，交易量越大费率越低</li>
</ul>
<h2>七、常见问题</h2>
<p>Q：没有营业执照可以办商户收款码吗？A：可以申请"小微商户"收款码，不需要营业执照，但有交易限额（单笔5000元，单日5万元）。Q：收款码资金安全吗？A：正规支付机构的收款码资金由银行托管，安全有保障。不要使用不明来源的收款码。Q：可以同时使用多个收款码吗？A：可以，微信、支付宝、聚合支付可以同时使用，根据顾客需求选择。</p>
<p>以上就是2026年商户收款码的办理指南，希望对各位商户朋友有所帮助。选择适合自己的收款方式，让生意更顺畅。</p>'
    ],
];

$count = 0;
foreach ($posts as $post_data) {
    $post_id = wp_insert_post([
        'post_title' => $post_data['title'],
        'post_content' => $post_data['content'],
        'post_status' => 'publish',
        'post_category' => $post_data['cat'],
        'post_author' => 1,
    ]);
    if (!is_wp_error($post_id)) {
        wp_set_post_categories($post_id, $post_data['cat']);
        $count++;
        echo "发布成功 ID $post_id: {$post_data['title']}\n";
        echo "  字数: " . mb_strlen(strip_tags($post_data['content'])) . "\n";
    } else {
        echo "发布失败: " . $post_id->get_error_message() . "\n";
    }
}

echo "\n共发布 $count 篇遗漏板块文章\n";
echo "DONE\n";
?>
