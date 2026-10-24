# 老百姓办事指南网站 - 发布脚本库

> 网站地址：[https://laobaixingbanshi.com](https://laobaixingbanshi.com)

## 网站介绍

老百姓办事指南是一个聚合全国招考公告、旅游攻略、购物优惠、知识文库、法律合同、职业考证等便民信息的网站。

## 主要分类入口

| 分类 | 链接 |
|------|------|
| 首页 | https://laobaixingbanshi.com |
| 招考就业 | https://laobaixingbanshi.com/category/zhaokao |
| 旅游酒店 | https://laobaixingbanshi.com/category/travel |
| 吃喝玩乐购 | https://laobaixingbanshi.com/category/gouwu |
| 知识文库 | https://laobaixingbanshi.com/category/wenku |
| 职业考证 | https://laobaixingbanshi.com/category/kaozheng |
| 法律合同 | https://laobaixingbanshi.com/category/falv |
| 创业办事 | https://laobaixingbanshi.com/category/chuangye |
| 社保医保 | https://laobaixingbanshi.com/category/shebao |

## 文章联盟链接格式规范（手机端）

### 旅游类文章

**飞猪（跟团游搜索）：**
```
https://s.m.taobao.com/h5?q={目的地}跟团游&ali_trackid=2:{TAOBAO_PID}
```

**携程（跟团游搜索）：**
```
https://m.ctrip.com/webapp/vacations/tour/list?kwd={目的地}&allianceid={CTRIP_AID}&sid={CTRIP_SID}&ouid={CTRIP_OUID}
```
> 注意：必须用 `kwd` 参数，不是 `kw`

**同程（度假搜索）：**
```
https://m.ly.com/dujia/search.html?dest={目的地}&refId={LY_REFID}
```
> 注意：必须用 `dest` 参数，不是 `keyword`

**禁止使用的PC端链接：**
- ~~travelsearch.fliggy.com~~（飞猪PC端）
- ~~vacations.ctrip.com~~（携程PC端）
- ~~www.ly.com~~（同程PC端）

### 购物类文章

**淘宝联盟：**
```
https://s.click.taobao.com/xxx（短链格式）
```
推广位：`{TAOBAO_PID}`

**京东联盟：**
```
https://union-click.jd.com/jdc?e=&p=JF8B...
```
siteId：`{JD_SITEID}`

**拼多多联盟：**
```
https://p.pinduoduo.com/xxx?sc=EFAC
```
p_id：`{PDD_PID}`

**唯品会联盟：**
```
https://t.vip.com/xxx?chanTag={VIP_CHANTAG}
```

## 脚本说明

| 脚本 | 用途 |
|------|------|
| publish_goods.php | 购物好物文章发布 |
| publish_jd_lrp.php | 京东联盟文章发布 |
| publish_pdd_proya.php | 拼多多联盟文章发布 |
| publish_taobao_cup.php | 淘宝联盟文章发布 |
| publish_today.php | 每日综合内容发布 |
| add_missing_cats.php | 补充分类脚本 |

## 服务器信息

- IP: {SERVER_IP}
- 站点目录: /www/wwwroot/laobaixingbanshi.com
- 脚本目录: /root/

## 注意事项

1. 所有旅游文章必须使用手机端联盟链接，禁止PC端链接
2. 购物文章必须使用真实商品短链，禁止占位链接
3. 每篇文章发布前需校验：字数、配图、分类、链接、去重
4. 服务器PHP时区为UTC，脚本需设置 `date_default_timezone_set('Asia/Shanghai')`

