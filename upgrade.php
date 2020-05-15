<?php
//升级数据表
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `agent_id` int(10) DEFAULT '0' COMMENT '推荐人id',
  `level_id` int(10) DEFAULT '0' COMMENT '团长等级id',
  `community_name` varchar(255) DEFAULT NULL COMMENT '小区名称',
  `head_name` varchar(100) NOT NULL COMMENT '团长名称',
  `head_mobile` varchar(50) NOT NULL COMMENT '团长手机号',
  `groupid` int(10) DEFAULT '0' COMMENT '分组id',
  `wechat` varchar(100) DEFAULT NULL COMMENT '微信号',
  `province_id` int(10) NOT NULL COMMENT '省',
  `city_id` int(10) NOT NULL COMMENT '市',
  `country_id` int(10) NOT NULL COMMENT '镇/街道',
  `area_id` int(10) NOT NULL COMMENT '区',
  `address` varchar(200) NOT NULL COMMENT '详细地址',
  `lon` float NOT NULL DEFAULT '0' COMMENT '经度',
  `lat` float NOT NULL DEFAULT '0' COMMENT '纬度',
  `state` tinyint(1) NOT NULL COMMENT '状态0 未审核，1已审核,2未审核',
  `enable` tinyint(1) DEFAULT '1' COMMENT '禁用状态 0：禁用 1：启用',
  `rest` tinyint(1) DEFAULT '0' COMMENT '团长休息',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '0非默认，1 默认，只有开启单团长模式下可用',
  `apptime` int(10) NOT NULL COMMENT '申请时间',
  `is_modify_shipping_method` tinyint(1) DEFAULT '0' COMMENT '0跟随系统，1，开启，2关闭',
  `is_modify_shipping_fare` tinyint(1) DEFAULT '0' COMMENT '是否开启自定义运费',
  `shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '配送费',
  `addtime` int(10) NOT NULL COMMENT '成为团长时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`member_id`),
  KEY `lon` (`lon`,`lat`),
  KEY `agent_id` (`agent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4559 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购团长表';

");

if(!pdo_fieldexists('lionfish_community_head','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_community_head','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_head','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_community_head','agent_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `agent_id` int(10) DEFAULT '0' COMMENT '推荐人id'");}
if(!pdo_fieldexists('lionfish_community_head','level_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `level_id` int(10) DEFAULT '0' COMMENT '团长等级id'");}
if(!pdo_fieldexists('lionfish_community_head','community_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `community_name` varchar(255) DEFAULT NULL COMMENT '小区名称'");}
if(!pdo_fieldexists('lionfish_community_head','head_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `head_name` varchar(100) NOT NULL COMMENT '团长名称'");}
if(!pdo_fieldexists('lionfish_community_head','head_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `head_mobile` varchar(50) NOT NULL COMMENT '团长手机号'");}
if(!pdo_fieldexists('lionfish_community_head','groupid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `groupid` int(10) DEFAULT '0' COMMENT '分组id'");}
if(!pdo_fieldexists('lionfish_community_head','wechat')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `wechat` varchar(100) DEFAULT NULL COMMENT '微信号'");}
if(!pdo_fieldexists('lionfish_community_head','province_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `province_id` int(10) NOT NULL COMMENT '省'");}
if(!pdo_fieldexists('lionfish_community_head','city_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `city_id` int(10) NOT NULL COMMENT '市'");}
if(!pdo_fieldexists('lionfish_community_head','country_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `country_id` int(10) NOT NULL COMMENT '镇/街道'");}
if(!pdo_fieldexists('lionfish_community_head','area_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `area_id` int(10) NOT NULL COMMENT '区'");}
if(!pdo_fieldexists('lionfish_community_head','address')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `address` varchar(200) NOT NULL COMMENT '详细地址'");}
if(!pdo_fieldexists('lionfish_community_head','lon')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `lon` float NOT NULL DEFAULT '0' COMMENT '经度'");}
if(!pdo_fieldexists('lionfish_community_head','lat')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `lat` float NOT NULL DEFAULT '0' COMMENT '纬度'");}
if(!pdo_fieldexists('lionfish_community_head','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态0 未审核，1已审核,2未审核'");}
if(!pdo_fieldexists('lionfish_community_head','enable')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `enable` tinyint(1) DEFAULT '1' COMMENT '禁用状态 0：禁用 1：启用'");}
if(!pdo_fieldexists('lionfish_community_head','rest')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `rest` tinyint(1) DEFAULT '0' COMMENT '团长休息'");}
if(!pdo_fieldexists('lionfish_community_head','is_default')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `is_default` tinyint(1) DEFAULT '0' COMMENT '0非默认，1 默认，只有开启单团长模式下可用'");}
if(!pdo_fieldexists('lionfish_community_head','apptime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `apptime` int(10) NOT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('lionfish_community_head','is_modify_shipping_method')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `is_modify_shipping_method` tinyint(1) DEFAULT '0' COMMENT '0跟随系统，1，开启，2关闭'");}
if(!pdo_fieldexists('lionfish_community_head','is_modify_shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `is_modify_shipping_fare` tinyint(1) DEFAULT '0' COMMENT '是否开启自定义运费'");}
if(!pdo_fieldexists('lionfish_community_head','shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '配送费'");}
if(!pdo_fieldexists('lionfish_community_head','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   `addtime` int(10) NOT NULL COMMENT '成为团长时间'");}
if(!pdo_fieldexists('lionfish_community_head','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_head','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   KEY `uniacid` (`uniacid`,`member_id`)");}
if(!pdo_fieldexists('lionfish_community_head','lon')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head')." ADD   KEY `lon` (`lon`,`lat`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head_commiss` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `head_id` int(10) NOT NULL COMMENT '团长ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额',
  `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额',
  `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额',
  `bankname` varchar(200) DEFAULT NULL COMMENT '银行名称',
  `bankaccount` varchar(200) DEFAULT NULL COMMENT '卡号',
  `bankusername` varchar(200) DEFAULT NULL COMMENT '持卡人姓名',
  `share_avatar` varchar(200) DEFAULT NULL COMMENT '团长群分享头像',
  `share_wxcode` varchar(200) DEFAULT NULL COMMENT '团长群分享二维码',
  `share_title` varchar(200) DEFAULT NULL COMMENT '团长群分享标题',
  `share_desc` varchar(255) DEFAULT NULL COMMENT '团长群分享描述',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`,`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4280 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_community_head_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `member_id` int(10) NOT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `head_id` int(10) NOT NULL COMMENT '团长ID'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','dongmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','getmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','bankname')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `bankname` varchar(200) DEFAULT NULL COMMENT '银行名称'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','bankaccount')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `bankaccount` varchar(200) DEFAULT NULL COMMENT '卡号'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','bankusername')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `bankusername` varchar(200) DEFAULT NULL COMMENT '持卡人姓名'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','share_avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `share_avatar` varchar(200) DEFAULT NULL COMMENT '团长群分享头像'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','share_wxcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `share_wxcode` varchar(200) DEFAULT NULL COMMENT '团长群分享二维码'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','share_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `share_title` varchar(200) DEFAULT NULL COMMENT '团长群分享标题'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','share_desc')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   `share_desc` varchar(255) DEFAULT NULL COMMENT '团长群分享描述'");}
if(!pdo_fieldexists('lionfish_community_head_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_head_commiss','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   KEY `member_id` (`member_id`)");}
if(!pdo_fieldexists('lionfish_community_head_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head_commiss_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `head_id` int(10) NOT NULL COMMENT '团长id',
  `child_head_id` int(10) DEFAULT '0' COMMENT '下级团长id',
  `type` enum('orderbuy','commiss','tuijian') NOT NULL DEFAULT 'orderbuy' COMMENT 'orderbuy 团员订单购买 ,commiss 下级的会员订单购买,tuijian 推荐团长的佣金',
  `fen_type` tinyint(1) DEFAULT '0' COMMENT '0,按照比例计算的，1按照实际金额计算的',
  `bili` float(10,2) DEFAULT '0.00' COMMENT '分销分佣比例',
  `level` int(10) DEFAULT '0' COMMENT '第几层分佣',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额',
  `add_shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '团长配送费',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`,`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185485 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_community_head_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `head_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','child_head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `child_head_id` int(10) DEFAULT '0' COMMENT '下级团长id'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `type` enum('orderbuy','commiss','tuijian') NOT NULL DEFAULT 'orderbuy' COMMENT 'orderbuy 团员订单购买 ,commiss 下级的会员订单购买,tuijian 推荐团长的佣金'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','fen_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `fen_type` tinyint(1) DEFAULT '0' COMMENT '0,按照比例计算的，1按照实际金额计算的'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','bili')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `bili` float(10,2) DEFAULT '0.00' COMMENT '分销分佣比例'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','level')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `level` int(10) DEFAULT '0' COMMENT '第几层分佣'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单ID'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','add_shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `add_shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '团长配送费'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   KEY `member_id` (`member_id`)");}
if(!pdo_fieldexists('lionfish_community_head_commiss_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_commiss_order')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `head_id` int(10) NOT NULL COMMENT '团长id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`head_id`),
  KEY `head_id` (`head_id`,`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41587 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购，团长商品表';

");

if(!pdo_fieldexists('lionfish_community_head_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_community_head_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_head_goods','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD   `head_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_community_head_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_community_head_goods','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_community_head_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_head_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_goods')." ADD   KEY `uniacid` (`uniacid`,`head_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `groupname` varchar(255) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='商城团长分组表';

");

if(!pdo_fieldexists('lionfish_community_head_group','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_group')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_community_head_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_group')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_head_group','groupname')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_group')." ADD   `groupname` varchar(255) NOT NULL COMMENT '分组名称'");}
if(!pdo_fieldexists('lionfish_community_head_group','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_group')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head_invite_recod` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `head_id` int(10) NOT NULL COMMENT '团长id',
  `agent_member_id` int(10) NOT NULL COMMENT '推荐人id',
  `money` float(10,2) NOT NULL COMMENT '奖励金额',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`head_id`),
  KEY `agent_member_id` (`agent_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8 COMMENT='团长推荐团长记录表';

");

if(!pdo_fieldexists('lionfish_community_head_invite_recod','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   `head_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','agent_member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   `agent_member_id` int(10) NOT NULL COMMENT '推荐人id'");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   `money` float(10,2) NOT NULL COMMENT '奖励金额'");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_head_invite_recod','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_invite_recod')." ADD   KEY `uniacid` (`uniacid`,`head_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_head_tixian_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL,
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `head_id` int(10) NOT NULL COMMENT '团长id',
  `money` float(10,2) NOT NULL COMMENT '提现金额',
  `service_charge` float(10,2) DEFAULT '0.00' COMMENT '手续费',
  `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败',
  `shentime` int(10) NOT NULL COMMENT '审核时间',
  `is_send_fail` tinyint(1) DEFAULT '0' COMMENT '是否发送是否，1是，0否',
  `fail_msg` varchar(255) DEFAULT NULL COMMENT '发送失败原因',
  `type` tinyint(1) DEFAULT '0' COMMENT '0 原系统参数，1 余额，2微信，3支付宝，4 银行卡',
  `bankname` varchar(100) DEFAULT NULL COMMENT '银行卡名称',
  `bankaccount` varchar(100) DEFAULT NULL COMMENT '卡号',
  `bankusername` varchar(100) DEFAULT NULL COMMENT '持卡人姓名',
  `addtime` int(10) NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`,`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1107 DEFAULT CHARSET=utf8 COMMENT='分销佣金提现表';

");

if(!pdo_fieldexists('lionfish_community_head_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `uniacid` int(10) NOT NULL");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `head_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `money` float(10,2) NOT NULL COMMENT '提现金额'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','service_charge')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `service_charge` float(10,2) DEFAULT '0.00' COMMENT '手续费'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','shentime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `shentime` int(10) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','is_send_fail')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `is_send_fail` tinyint(1) DEFAULT '0' COMMENT '是否发送是否，1是，0否'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','fail_msg')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `fail_msg` varchar(255) DEFAULT NULL COMMENT '发送失败原因'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '0 原系统参数，1 余额，2微信，3支付宝，4 银行卡'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','bankname')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `bankname` varchar(100) DEFAULT NULL COMMENT '银行卡名称'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','bankaccount')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `bankaccount` varchar(100) DEFAULT NULL COMMENT '卡号'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','bankusername')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `bankusername` varchar(100) DEFAULT NULL COMMENT '持卡人姓名'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   `addtime` int(10) NOT NULL COMMENT '提交时间'");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_head_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_head_tixian_order')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_community_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `head_id` int(10) NOT NULL COMMENT '社区id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`head_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=102216 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购上个小区历史记录表';

");

if(!pdo_fieldexists('lionfish_community_history','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_community_history','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_community_history','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_community_history','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD   `head_id` int(10) NOT NULL COMMENT '社区id'");}
if(!pdo_fieldexists('lionfish_community_history','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_community_history','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_community_history','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_community_history')." ADD   KEY `member_id` (`member_id`,`head_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `name` varchar(100) NOT NULL COMMENT '收货人姓名',
  `telephone` varchar(50) NOT NULL COMMENT '电话',
  `address` varchar(255) NOT NULL COMMENT '收货地址',
  `address_name` enum('HOME','WORK') NOT NULL DEFAULT 'HOME' COMMENT '收货地址类型',
  `lon_lat` varchar(30) DEFAULT '' COMMENT '地址经纬度',
  `lou_meng_hao` varchar(100) DEFAULT '' COMMENT '门牌号-楼号',
  `is_default` tinyint(1) NOT NULL COMMENT '是否默认的收货地址',
  `city_id` int(10) NOT NULL COMMENT '市',
  `country_id` int(10) NOT NULL COMMENT '县乡',
  `province_id` int(10) NOT NULL COMMENT '省',
  PRIMARY KEY (`address_id`),
  KEY `uniacid` (`uniacid`,`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5303 DEFAULT CHARSET=utf8mb4 COMMENT='商城地址库';

");

if(!pdo_fieldexists('lionfish_comshop_address','address_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD 
  `address_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_address','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_address','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `member_id` int(11) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_address','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `name` varchar(100) NOT NULL COMMENT '收货人姓名'");}
if(!pdo_fieldexists('lionfish_comshop_address','telephone')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `telephone` varchar(50) NOT NULL COMMENT '电话'");}
if(!pdo_fieldexists('lionfish_comshop_address','address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `address` varchar(255) NOT NULL COMMENT '收货地址'");}
if(!pdo_fieldexists('lionfish_comshop_address','address_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `address_name` enum('HOME','WORK') NOT NULL DEFAULT 'HOME' COMMENT '收货地址类型'");}
if(!pdo_fieldexists('lionfish_comshop_address','lon_lat')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `lon_lat` varchar(30) DEFAULT '' COMMENT '地址经纬度'");}
if(!pdo_fieldexists('lionfish_comshop_address','lou_meng_hao')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `lou_meng_hao` varchar(100) DEFAULT '' COMMENT '门牌号-楼号'");}
if(!pdo_fieldexists('lionfish_comshop_address','is_default')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `is_default` tinyint(1) NOT NULL COMMENT '是否默认的收货地址'");}
if(!pdo_fieldexists('lionfish_comshop_address','city_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `city_id` int(10) NOT NULL COMMENT '市'");}
if(!pdo_fieldexists('lionfish_comshop_address','country_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `country_id` int(10) NOT NULL COMMENT '县乡'");}
if(!pdo_fieldexists('lionfish_comshop_address','province_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   `province_id` int(10) NOT NULL COMMENT '省'");}
if(!pdo_fieldexists('lionfish_comshop_address','address_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_address')." ADD   PRIMARY KEY (`address_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `advname` varchar(200) NOT NULL COMMENT '广告名称',
  `thumb` varchar(255) NOT NULL COMMENT '图片地址',
  `link` varchar(255) NOT NULL COMMENT '广告链接地址',
  `appid` varchar(255) DEFAULT NULL COMMENT '外部小程序appid',
  `linktype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0外链 1小程序内 2外部小程序',
  `type` enum('slider','nav','pintuan','recipe') NOT NULL COMMENT '广告类型，slider幻灯，nav导航小图',
  `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面',
  `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效',
  `addtime` int(10) NOT NULL COMMENT '添加广告时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=324 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品广告表';

");

if(!pdo_fieldexists('lionfish_comshop_adv','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_adv','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_adv','advname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `advname` varchar(200) NOT NULL COMMENT '广告名称'");}
if(!pdo_fieldexists('lionfish_comshop_adv','thumb')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `thumb` varchar(255) NOT NULL COMMENT '图片地址'");}
if(!pdo_fieldexists('lionfish_comshop_adv','link')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `link` varchar(255) NOT NULL COMMENT '广告链接地址'");}
if(!pdo_fieldexists('lionfish_comshop_adv','appid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `appid` varchar(255) DEFAULT NULL COMMENT '外部小程序appid'");}
if(!pdo_fieldexists('lionfish_comshop_adv','linktype')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `linktype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0外链 1小程序内 2外部小程序'");}
if(!pdo_fieldexists('lionfish_comshop_adv','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `type` enum('slider','nav','pintuan','recipe') NOT NULL COMMENT '广告类型，slider幻灯，nav导航小图'");}
if(!pdo_fieldexists('lionfish_comshop_adv','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面'");}
if(!pdo_fieldexists('lionfish_comshop_adv','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效'");}
if(!pdo_fieldexists('lionfish_comshop_adv','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   `addtime` int(10) NOT NULL COMMENT '添加广告时间'");}
if(!pdo_fieldexists('lionfish_comshop_adv','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_adv')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '11',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `name` varchar(100) NOT NULL COMMENT '地区名称',
  `pid` int(11) NOT NULL COMMENT '上级id',
  `code` varchar(20) NOT NULL COMMENT '地区编号',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=5606 DEFAULT CHARSET=utf8mb4 COMMENT='商城地区配置表';

");

if(!pdo_fieldexists('lionfish_comshop_area','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '11'");}
if(!pdo_fieldexists('lionfish_comshop_area','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_area','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD   `name` varchar(100) NOT NULL COMMENT '地区名称'");}
if(!pdo_fieldexists('lionfish_comshop_area','pid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD   `pid` int(11) NOT NULL COMMENT '上级id'");}
if(!pdo_fieldexists('lionfish_comshop_area','code')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD   `code` varchar(20) NOT NULL COMMENT '地区编号'");}
if(!pdo_fieldexists('lionfish_comshop_area','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_area','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_area')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面',
  `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COMMENT='商城文章列表';

");

if(!pdo_fieldexists('lionfish_comshop_article','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_article','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_article','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   `title` varchar(100) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('lionfish_comshop_article','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   `content` text NOT NULL COMMENT '内容'");}
if(!pdo_fieldexists('lionfish_comshop_article','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面'");}
if(!pdo_fieldexists('lionfish_comshop_article','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效'");}
if(!pdo_fieldexists('lionfish_comshop_article','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_article','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_article')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_car` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `token` varchar(200) NOT NULL COMMENT '客户端token,会员身份',
  `community_id` int(10) NOT NULL COMMENT '团长id',
  `carkey` varchar(255) NOT NULL COMMENT '购物车key',
  `format_data` text NOT NULL COMMENT '购物车商品参数',
  `modifytime` int(10) DEFAULT '0' COMMENT '加入购物车时间',
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `uniacid` (`uniacid`),
  KEY `community_id` (`community_id`)
) ENGINE=InnoDB AUTO_INCREMENT=327202 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='客户端购物车表';

");

if(!pdo_fieldexists('lionfish_comshop_car','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_car','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_car','token')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   `token` varchar(200) NOT NULL COMMENT '客户端token,会员身份'");}
if(!pdo_fieldexists('lionfish_comshop_car','community_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   `community_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_comshop_car','carkey')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   `carkey` varchar(255) NOT NULL COMMENT '购物车key'");}
if(!pdo_fieldexists('lionfish_comshop_car','format_data')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   `format_data` text NOT NULL COMMENT '购物车商品参数'");}
if(!pdo_fieldexists('lionfish_comshop_car','modifytime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   `modifytime` int(10) DEFAULT '0' COMMENT '加入购物车时间'");}
if(!pdo_fieldexists('lionfish_comshop_car','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_car','token')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   KEY `token` (`token`)");}
if(!pdo_fieldexists('lionfish_comshop_car','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_car')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_chargetype` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `money` float(10,2) NOT NULL COMMENT '充值金额',
  `send_money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送金额',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='会员充值选项表';

");

if(!pdo_fieldexists('lionfish_comshop_chargetype','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_chargetype')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_chargetype','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_chargetype')." ADD   `money` float(10,2) NOT NULL COMMENT '充值金额'");}
if(!pdo_fieldexists('lionfish_comshop_chargetype','send_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_chargetype')." ADD   `send_money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送金额'");}
if(!pdo_fieldexists('lionfish_comshop_chargetype','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_chargetype')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_chargetype','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_chargetype')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_chargetype','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_chargetype')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_commission_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `levelname` varchar(50) NOT NULL,
  `commission1` decimal(10,2) DEFAULT '0.00',
  `commission2` decimal(10,2) DEFAULT '0.00',
  `commission3` decimal(10,2) DEFAULT '0.00',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城分销等级表';

");

if(!pdo_fieldexists('lionfish_comshop_commission_level','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','levelname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   `levelname` varchar(50) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','commission1')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   `commission1` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','commission2')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   `commission2` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','commission3')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   `commission3` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','ordermoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   `ordermoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('lionfish_comshop_commission_level','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_commission_level')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_community_head_level` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `levelname` varchar(255) NOT NULL COMMENT '等级名称',
  `commission` float(10,2) DEFAULT '0.00' COMMENT '分销比例',
  `auto_upgrade` tinyint(1) DEFAULT '0' COMMENT '自动升级： 0 否，1 是',
  `condition_type` tinyint(1) DEFAULT NULL COMMENT '0 . 累计社区用户完成订单总金额, 1 .累计社区用户数量',
  `condition_one` float(10,2) DEFAULT '0.00' COMMENT '累计社区用户完成订单总金额',
  `condition_two` int(10) DEFAULT '0' COMMENT '累计社区用户数量',
  `commission2` float(10,2) DEFAULT '0.00' COMMENT '2级分销比例',
  `commission3` float(10,2) DEFAULT '0.00' COMMENT '3级分销比例',
  `condition_order_total` int(11) NOT NULL DEFAULT '0' COMMENT '团长升级订单满数升级条件',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COMMENT='商城团长等级表';

");

if(!pdo_fieldexists('lionfish_comshop_community_head_level','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','levelname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `levelname` varchar(255) NOT NULL COMMENT '等级名称'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','commission')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `commission` float(10,2) DEFAULT '0.00' COMMENT '分销比例'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','auto_upgrade')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `auto_upgrade` tinyint(1) DEFAULT '0' COMMENT '自动升级： 0 否，1 是'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','condition_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `condition_type` tinyint(1) DEFAULT NULL COMMENT '0 . 累计社区用户完成订单总金额, 1 .累计社区用户数量'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','condition_one')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `condition_one` float(10,2) DEFAULT '0.00' COMMENT '累计社区用户完成订单总金额'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','condition_two')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `condition_two` int(10) DEFAULT '0' COMMENT '累计社区用户数量'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','commission2')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `commission2` float(10,2) DEFAULT '0.00' COMMENT '2级分销比例'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','commission3')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `commission3` float(10,2) DEFAULT '0.00' COMMENT '3级分销比例'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','condition_order_total')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   `condition_order_total` int(11) NOT NULL DEFAULT '0' COMMENT '团长升级订单满数升级条件'");}
if(!pdo_fieldexists('lionfish_comshop_community_head_level','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_head_level')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_community_pickup_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `community_id` int(10) NOT NULL COMMENT '社区id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0未审核，1审核通过',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`member_id`),
  KEY `community_id` (`community_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COMMENT='核销人员表';

");

if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','community_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   `community_id` int(10) NOT NULL COMMENT '社区id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0未审核，1审核通过'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','remark')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   `remark` varchar(255) DEFAULT NULL COMMENT '备注'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member')." ADD   KEY `uniacid` (`uniacid`,`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_community_pickup_member_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_sn` varchar(100) DEFAULT NULL COMMENT '订单编号',
  `community_id` int(10) NOT NULL COMMENT '社区id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=718 DEFAULT CHARSET=utf8 COMMENT='核销员核销记录';

");

if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','order_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   `order_sn` varchar(100) DEFAULT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','community_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   `community_id` int(10) NOT NULL COMMENT '社区id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_community_pickup_member_record','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_community_pickup_member_record')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7483 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_config','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_config')." ADD 
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_config','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_config')." ADD   `uniacid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_config','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_config')." ADD   `name` varchar(100) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_config','value')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_config')." ADD   `value` text NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_config','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_config')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_config','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_config')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `catid` int(10) DEFAULT NULL COMMENT '分类id',
  `voucher_title` varchar(100) NOT NULL COMMENT '优惠券名称',
  `thumb` varchar(255) NOT NULL COMMENT '图片',
  `credit` float(10,2) NOT NULL COMMENT '代金额',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型。0店铺专用，1所有通用',
  `is_index_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐首页，供用户领取',
  `is_index_alert` tinyint(1) DEFAULT '0' COMMENT '是否首页弹窗，0否，1是',
  `is_new_man` tinyint(1) DEFAULT '0' COMMENT '是否新人券，0非，1新人',
  `is_share_doubling` tinyint(1) DEFAULT '0' COMMENT '是否分享翻倍，0：不翻倍，1翻倍',
  `get_over_hour` float(10,2) DEFAULT '0.00' COMMENT '领券后过期时间',
  `is_limit_goods_buy` tinyint(1) DEFAULT '0' COMMENT '是否限制商品购买，0不限制，1限制',
  `limit_goods_list` text COMMENT '限制的商品id',
  `goodscates` int(10) DEFAULT '0' COMMENT '限制商品分类使用',
  `share_title` varchar(255) DEFAULT NULL COMMENT '优惠券分享标题',
  `share_desc` varchar(255) DEFAULT NULL COMMENT '优惠券分享描述',
  `share_logo` varchar(255) DEFAULT NULL COMMENT '分享logo',
  `timelimit` tinyint(1) NOT NULL COMMENT '0 获得后，1在日期',
  `person_limit_count` int(10) DEFAULT '0' COMMENT '每人限领数量，0表示不限制',
  `limit_money` float(10,2) NOT NULL COMMENT '订单金额满多少可用',
  `total_count` int(11) NOT NULL COMMENT '总生成张数',
  `send_count` int(11) NOT NULL COMMENT '已发送张数',
  `begin_time` int(10) NOT NULL COMMENT '可以使用开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  `displayorder` int(10) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_coupon','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','catid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `catid` int(10) DEFAULT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','voucher_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `voucher_title` varchar(100) NOT NULL COMMENT '优惠券名称'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','thumb')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `thumb` varchar(255) NOT NULL COMMENT '图片'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','credit')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `credit` float(10,2) NOT NULL COMMENT '代金额'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型。0店铺专用，1所有通用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','is_index_show')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `is_index_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐首页，供用户领取'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','is_index_alert')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `is_index_alert` tinyint(1) DEFAULT '0' COMMENT '是否首页弹窗，0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','is_new_man')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `is_new_man` tinyint(1) DEFAULT '0' COMMENT '是否新人券，0非，1新人'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','is_share_doubling')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `is_share_doubling` tinyint(1) DEFAULT '0' COMMENT '是否分享翻倍，0：不翻倍，1翻倍'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','get_over_hour')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `get_over_hour` float(10,2) DEFAULT '0.00' COMMENT '领券后过期时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','is_limit_goods_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `is_limit_goods_buy` tinyint(1) DEFAULT '0' COMMENT '是否限制商品购买，0不限制，1限制'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','limit_goods_list')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `limit_goods_list` text COMMENT '限制的商品id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','goodscates')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `goodscates` int(10) DEFAULT '0' COMMENT '限制商品分类使用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','share_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `share_title` varchar(255) DEFAULT NULL COMMENT '优惠券分享标题'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','share_desc')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `share_desc` varchar(255) DEFAULT NULL COMMENT '优惠券分享描述'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','share_logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `share_logo` varchar(255) DEFAULT NULL COMMENT '分享logo'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','timelimit')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `timelimit` tinyint(1) NOT NULL COMMENT '0 获得后，1在日期'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','person_limit_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `person_limit_count` int(10) DEFAULT '0' COMMENT '每人限领数量，0表示不限制'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','limit_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `limit_money` float(10,2) NOT NULL COMMENT '订单金额满多少可用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','total_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `total_count` int(11) NOT NULL COMMENT '总生成张数'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','send_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `send_count` int(11) NOT NULL COMMENT '已发送张数'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `begin_time` int(10) NOT NULL COMMENT '可以使用开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `end_time` int(10) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `add_time` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   `displayorder` int(10) DEFAULT NULL COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_coupon','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_coupon_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `merchid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_coupon_category','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   `uniacid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   `name` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   `displayorder` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','status')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   `status` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','merchid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   `merchid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_coupon_category','idx_displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_category')." ADD   KEY `idx_displayorder` (`displayorder`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_coupon_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `voucher_id` int(11) NOT NULL COMMENT '优惠券id',
  `voucher_title` varchar(100) NOT NULL COMMENT '优惠券标题',
  `store_id` int(10) NOT NULL COMMENT '商家id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '领取的用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0店铺专用，1所有通用',
  `ordersn` varchar(255) DEFAULT NULL COMMENT '订单编号',
  `credit` float(10,2) NOT NULL COMMENT '优惠金额',
  `usetime` int(10) DEFAULT NULL COMMENT '使用时间',
  `limit_money` float(10,2) NOT NULL COMMENT '满多少可用',
  `is_limit_goods_buy` tinyint(1) DEFAULT '0' COMMENT '是否限制商品购买，0不限制，1限制',
  `limit_goods_list` text COMMENT '限制的商品id',
  `goodscates` int(10) DEFAULT '0' COMMENT '限制商品分类使用',
  `consume` enum('N','Y') NOT NULL COMMENT '是否使用',
  `gettype` tinyint(1) DEFAULT '1' COMMENT '领取方式，1首页领取',
  `begin_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `voucher_id` (`voucher_id`),
  KEY `user_id` (`user_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=6415 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_coupon_list','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','voucher_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `voucher_id` int(11) NOT NULL COMMENT '优惠券id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','voucher_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `voucher_title` varchar(100) NOT NULL COMMENT '优惠券标题'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `store_id` int(10) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','user_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '领取的用户id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0店铺专用，1所有通用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','ordersn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `ordersn` varchar(255) DEFAULT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','credit')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `credit` float(10,2) NOT NULL COMMENT '优惠金额'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','usetime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `usetime` int(10) DEFAULT NULL COMMENT '使用时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','limit_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `limit_money` float(10,2) NOT NULL COMMENT '满多少可用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','is_limit_goods_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `is_limit_goods_buy` tinyint(1) DEFAULT '0' COMMENT '是否限制商品购买，0不限制，1限制'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','limit_goods_list')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `limit_goods_list` text COMMENT '限制的商品id'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','goodscates')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `goodscates` int(10) DEFAULT '0' COMMENT '限制商品分类使用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','consume')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `consume` enum('N','Y') NOT NULL COMMENT '是否使用'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','gettype')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `gettype` tinyint(1) DEFAULT '1' COMMENT '领取方式，1首页领取'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `begin_time` int(10) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `end_time` int(10) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   `add_time` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','voucher_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   KEY `voucher_id` (`voucher_id`)");}
if(!pdo_fieldexists('lionfish_comshop_coupon_list','user_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_coupon_list')." ADD   KEY `user_id` (`user_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_cube` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `thumb` text NOT NULL COMMENT '图片',
  `num` tinyint(1) NOT NULL COMMENT '图片个数',
  `appid` varchar(255) DEFAULT NULL COMMENT '外部小程序appid',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '类型',
  `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面',
  `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效',
  `linktype` tinyint(1) NOT NULL COMMENT '0外链 1小程序内 2外部小程序',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='魔方图片';

");

if(!pdo_fieldexists('lionfish_comshop_cube','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_cube','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_cube','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `name` varchar(255) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('lionfish_comshop_cube','thumb')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `thumb` text NOT NULL COMMENT '图片'");}
if(!pdo_fieldexists('lionfish_comshop_cube','num')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `num` tinyint(1) NOT NULL COMMENT '图片个数'");}
if(!pdo_fieldexists('lionfish_comshop_cube','appid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `appid` varchar(255) DEFAULT NULL COMMENT '外部小程序appid'");}
if(!pdo_fieldexists('lionfish_comshop_cube','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '类型'");}
if(!pdo_fieldexists('lionfish_comshop_cube','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面'");}
if(!pdo_fieldexists('lionfish_comshop_cube','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效'");}
if(!pdo_fieldexists('lionfish_comshop_cube','linktype')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `linktype` tinyint(1) NOT NULL COMMENT '0外链 1小程序内 2外部小程序'");}
if(!pdo_fieldexists('lionfish_comshop_cube','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_cube')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_delivery_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '收货地址id',
  `uniacid` int(11) DEFAULT '0' COMMENT '小程序id',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `name` varchar(20) DEFAULT NULL COMMENT '联系人',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `city` varchar(30) DEFAULT NULL COMMENT '市',
  `area` varchar(30) DEFAULT NULL COMMENT '区',
  `address` varchar(300) DEFAULT NULL COMMENT '详细地址',
  `isdefault` tinyint(1) DEFAULT '0' COMMENT '是否默认',
  `zipcode` varchar(20) DEFAULT NULL COMMENT '邮编',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家收货地址';

");

if(!pdo_fieldexists('lionfish_comshop_delivery_address','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '收货地址id'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '小程序id'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `title` varchar(20) DEFAULT NULL COMMENT '标题'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `name` varchar(20) DEFAULT NULL COMMENT '联系人'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','tel')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `tel` varchar(20) DEFAULT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `mobile` varchar(11) DEFAULT NULL COMMENT '手机'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','province')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `province` varchar(30) DEFAULT NULL COMMENT '省'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','city')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `city` varchar(30) DEFAULT NULL COMMENT '市'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','area')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `area` varchar(30) DEFAULT NULL COMMENT '区'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `address` varchar(300) DEFAULT NULL COMMENT '详细地址'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','isdefault')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `isdefault` tinyint(1) DEFAULT '0' COMMENT '是否默认'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','zipcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   `zipcode` varchar(20) DEFAULT NULL COMMENT '邮编'");}
if(!pdo_fieldexists('lionfish_comshop_delivery_address','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_delivery_address')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_deliveryclerk` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `line_id` int(10) NOT NULL COMMENT '线路id',
  `name` varchar(100) NOT NULL COMMENT '配送员名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '头像',
  `mobile` varchar(50) NOT NULL COMMENT '配送员手机号',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`line_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购配送员表';

");

if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','line_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   `line_id` int(10) NOT NULL COMMENT '线路id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   `name` varchar(100) NOT NULL COMMENT '配送员名称'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   `logo` varchar(255) DEFAULT NULL COMMENT '头像'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   `mobile` varchar(50) NOT NULL COMMENT '配送员手机号'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryclerk','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryclerk')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_deliveryline` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `name` varchar(255) NOT NULL COMMENT '线路名称',
  `clerk_id` int(10) NOT NULL COMMENT '配送员id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`clerk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购配送线路';

");

if(!pdo_fieldexists('lionfish_comshop_deliveryline','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline')." ADD   `name` varchar(255) NOT NULL COMMENT '线路名称'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline','clerk_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline')." ADD   `clerk_id` int(10) NOT NULL COMMENT '配送员id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_deliveryline_headrelative` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `line_id` int(10) NOT NULL COMMENT '线路id',
  `head_id` int(11) NOT NULL COMMENT '团长id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`line_id`),
  KEY `head_id` (`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=751 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购线路团长关联表';

");

if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','line_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD   `line_id` int(10) NOT NULL COMMENT '线路id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD   `head_id` int(11) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD   `addtime` int(11) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_deliveryline_headrelative','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliveryline_headrelative')." ADD   KEY `uniacid` (`uniacid`,`line_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_deliverylist` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `list_sn` varchar(50) NOT NULL COMMENT '编号',
  `head_id` int(10) DEFAULT NULL COMMENT '团长id',
  `head_name` varchar(100) NOT NULL COMMENT '团长名称',
  `head_mobile` varchar(20) DEFAULT NULL COMMENT '团长手机',
  `head_address` varchar(255) NOT NULL COMMENT '团长地址',
  `line_id` int(10) NOT NULL COMMENT '线路id',
  `line_name` varchar(200) DEFAULT NULL COMMENT '线路名称',
  `clerk_id` int(10) DEFAULT NULL COMMENT '配送员id',
  `clerk_name` varchar(100) DEFAULT NULL COMMENT '配送员名称',
  `clerk_mobile` varchar(50) DEFAULT NULL COMMENT '配送员手机号',
  `state` tinyint(1) NOT NULL COMMENT '配送状态：0未配送，1配送中，2已配送',
  `goods_count` int(10) NOT NULL COMMENT '商品数量',
  `express_time` int(10) NOT NULL COMMENT '配送时间',
  `head_get_time` int(10) DEFAULT NULL COMMENT '清单配送到时间',
  `create_time` int(10) NOT NULL COMMENT '创建清单时间',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`line_id`),
  KEY `clerk_id` (`clerk_id`),
  KEY `head_id` (`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11943 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购清单表';

");

if(!pdo_fieldexists('lionfish_comshop_deliverylist','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','list_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `list_sn` varchar(50) NOT NULL COMMENT '编号'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `head_id` int(10) DEFAULT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','head_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `head_name` varchar(100) NOT NULL COMMENT '团长名称'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','head_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `head_mobile` varchar(20) DEFAULT NULL COMMENT '团长手机'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','head_address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `head_address` varchar(255) NOT NULL COMMENT '团长地址'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','line_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `line_id` int(10) NOT NULL COMMENT '线路id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','line_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `line_name` varchar(200) DEFAULT NULL COMMENT '线路名称'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','clerk_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `clerk_id` int(10) DEFAULT NULL COMMENT '配送员id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','clerk_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `clerk_name` varchar(100) DEFAULT NULL COMMENT '配送员名称'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','clerk_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `clerk_mobile` varchar(50) DEFAULT NULL COMMENT '配送员手机号'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `state` tinyint(1) NOT NULL COMMENT '配送状态：0未配送，1配送中，2已配送'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','goods_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `goods_count` int(10) NOT NULL COMMENT '商品数量'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','express_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `express_time` int(10) NOT NULL COMMENT '配送时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','head_get_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `head_get_time` int(10) DEFAULT NULL COMMENT '清单配送到时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','create_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `create_time` int(10) NOT NULL COMMENT '创建清单时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   `addtime` int(11) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   KEY `uniacid` (`uniacid`,`line_id`)");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist','clerk_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist')." ADD   KEY `clerk_id` (`clerk_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_deliverylist_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `list_id` int(10) NOT NULL COMMENT '清单id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `goods_name` varchar(255) NOT NULL COMMENT '商品标题',
  `rela_goodsoption_valueid` varchar(100) NOT NULL COMMENT '商品规格',
  `sku_str` varchar(255) DEFAULT NULL COMMENT '标签名称',
  `goods_image` varchar(255) NOT NULL COMMENT '商品图片',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `goods_count` int(10) NOT NULL COMMENT '商品数量',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `list_id` (`list_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91179 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购';

");

if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','list_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `list_id` int(10) NOT NULL COMMENT '清单id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','goods_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `goods_name` varchar(255) NOT NULL COMMENT '商品标题'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','rela_goodsoption_valueid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `rela_goodsoption_valueid` varchar(100) NOT NULL COMMENT '商品规格'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','sku_str')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `sku_str` varchar(255) DEFAULT NULL COMMENT '标签名称'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','goods_image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `goods_image` varchar(255) NOT NULL COMMENT '商品图片'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','goods_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   `goods_count` int(10) NOT NULL COMMENT '商品数量'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_goods','list_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_goods')." ADD   KEY `list_id` (`list_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_deliverylist_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `list_id` int(10) NOT NULL COMMENT '清单id',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`list_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92681 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购清单订单关联表';

");

if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','list_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD   `list_id` int(10) NOT NULL COMMENT '清单id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD   `order_id` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD   `addtime` int(11) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_deliverylist_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_deliverylist_order')." ADD   KEY `uniacid` (`uniacid`,`list_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_diypage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `lastedittime` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `diymenu` int(11) NOT NULL DEFAULT '0',
  `merch` int(11) NOT NULL DEFAULT '0',
  `diyadv` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_keyword` (`keyword`),
  KEY `idx_lastedittime` (`lastedittime`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_diypage','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_diypage','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `type` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `name` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('lionfish_comshop_diypage','data')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `data` longtext NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_diypage','createtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `createtime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','lastedittime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `lastedittime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','keyword')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `keyword` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('lionfish_comshop_diypage','diymenu')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `diymenu` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','merch')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `merch` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','diyadv')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   `diyadv` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_diypage','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_diypage','idx_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   KEY `idx_type` (`type`)");}
if(!pdo_fieldexists('lionfish_comshop_diypage','idx_keyword')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   KEY `idx_keyword` (`keyword`)");}
if(!pdo_fieldexists('lionfish_comshop_diypage','idx_lastedittime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage')." ADD   KEY `idx_lastedittime` (`lastedittime`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_diypage_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `preview` varchar(255) NOT NULL DEFAULT '',
  `tplid` int(11) DEFAULT '0',
  `cate` int(11) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `merch` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_cate` (`cate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_diypage_template','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `type` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `name` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','data')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `data` longtext NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','preview')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `preview` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','tplid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `tplid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','cate')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `cate` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','deleted')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `deleted` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','merch')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   `merch` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_diypage_template','idx_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_diypage_template')." ADD   KEY `idx_type` (`type`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `name` varchar(100) NOT NULL COMMENT '快递名称',
  `simplecode` varchar(100) NOT NULL COMMENT '快递简码',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COMMENT='商城快递表';

");

if(!pdo_fieldexists('lionfish_comshop_express','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_express')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_express','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_express')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_express','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_express')." ADD   `name` varchar(100) NOT NULL COMMENT '快递名称'");}
if(!pdo_fieldexists('lionfish_comshop_express','simplecode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_express')." ADD   `simplecode` varchar(100) NOT NULL COMMENT '快递简码'");}
if(!pdo_fieldexists('lionfish_comshop_express','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_express')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_good_commiss` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `nocommission` tinyint(1) NOT NULL COMMENT '0参与分销，1不参与分销',
  `hascommission` tinyint(1) NOT NULL COMMENT '启用独立佣金比例，1启动独立分销',
  `commission_type` tinyint(1) NOT NULL COMMENT '统一分销佣金',
  `commission1_rate` int(10) NOT NULL COMMENT '一级分销比例',
  `commission1_pay` decimal(10,2) NOT NULL COMMENT '一级分销金额',
  `commission2_rate` int(10) NOT NULL COMMENT '二级分销比例',
  `commission2_pay` decimal(10,2) NOT NULL COMMENT '二级分销金额',
  `commission3_rate` int(10) NOT NULL COMMENT '三级分销比例',
  `commission3_pay` decimal(10,2) NOT NULL COMMENT '三级分销金额',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41151 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品分销配置表';

");

if(!pdo_fieldexists('lionfish_comshop_good_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','nocommission')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `nocommission` tinyint(1) NOT NULL COMMENT '0参与分销，1不参与分销'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','hascommission')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `hascommission` tinyint(1) NOT NULL COMMENT '启用独立佣金比例，1启动独立分销'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission_type` tinyint(1) NOT NULL COMMENT '统一分销佣金'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission1_rate')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission1_rate` int(10) NOT NULL COMMENT '一级分销比例'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission1_pay')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission1_pay` decimal(10,2) NOT NULL COMMENT '一级分销金额'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission2_rate')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission2_rate` int(10) NOT NULL COMMENT '二级分销比例'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission2_pay')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission2_pay` decimal(10,2) NOT NULL COMMENT '二级分销金额'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission3_rate')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission3_rate` int(10) NOT NULL COMMENT '三级分销比例'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','commission3_pay')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   `commission3_pay` decimal(10,2) NOT NULL COMMENT '三级分销金额'");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_good_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_commiss')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_good_common` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `quality` varchar(50) NOT NULL COMMENT '正品保证',
  `seven` varchar(50) NOT NULL COMMENT '7天无理由退换',
  `repair` varchar(50) NOT NULL COMMENT '保修',
  `labelname` text NOT NULL COMMENT '自定义标签名称',
  `per_number` int(10) DEFAULT '0' COMMENT '商品限购数量，0 不限制。',
  `supply_id` int(10) DEFAULT '0' COMMENT '供货商id',
  `pick_just` tinyint(1) DEFAULT '0' COMMENT '仅自提',
  `pick_up_type` tinyint(1) DEFAULT NULL COMMENT '1 当日达，2次日达，3隔日达，4自定义',
  `pick_up_modify` varchar(255) DEFAULT NULL COMMENT '自定义自提日期信息',
  `one_limit_count` int(10) DEFAULT '0' COMMENT '单次最多购买次数，0不限制',
  `total_limit_count` int(10) DEFAULT '0' COMMENT '总的最多购买次数，0不限制',
  `community_head_commission` float(10,2) DEFAULT '0.00' COMMENT '团长佣金比例',
  `is_modify_head_commission` tinyint(1) DEFAULT '0' COMMENT '是否开启独立配置团长等级分佣',
  `community_head_commission_modify` text COMMENT '自定义团长等级分佣配置信息',
  `begin_time` int(10) NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `share_title` varchar(255) NOT NULL COMMENT '分享标题',
  `print_sub_title` varchar(200) DEFAULT NULL COMMENT '商品打印简短标题',
  `share_description` varchar(255) NOT NULL COMMENT '分享描述',
  `wepro_qrcode_image` varchar(255) DEFAULT NULL COMMENT '商品小程序分享背景图',
  `content` text NOT NULL COMMENT '商品内容',
  `big_img` varchar(255) DEFAULT NULL COMMENT '首页列表大图',
  `video` varchar(255) DEFAULT NULL COMMENT '商品视频',
  `goods_share_image` varchar(255) DEFAULT NULL COMMENT '商品分享图',
  `is_take_fullreduction` tinyint(1) DEFAULT '1' COMMENT '是否参加满减',
  `is_new_buy` tinyint(1) DEFAULT '0' COMMENT '是否新人购买，0否，1 是',
  `is_limit_levelunbuy` tinyint(1) DEFAULT '0' COMMENT '默认等级不能买',
  `is_limit_vipmember_buy` tinyint(1) DEFAULT '0' COMMENT '非VIP 不能购买',
  `is_spike_buy` tinyint(1) DEFAULT '0' COMMENT '是否限时秒杀，0不是，1是',
  `is_mb_level_buy` tinyint(1) DEFAULT '1' COMMENT '是否支持会员折扣购买',
  `is_modify_sendscore` tinyint(1) DEFAULT '0' COMMENT '是否自定义赠送积分',
  `send_socre` int(10) DEFAULT '0' COMMENT '买一个商品赠送积分多少',
  `is_only_express` tinyint(1) DEFAULT '0' COMMENT '是否仅快递',
  `is_only_hexiao` tinyint(1) DEFAULT '0' COMMENT '是否仅核销，0否，1是',
  `relative_goods_list` text COMMENT '关联的商品id',
  `is_show_arrive` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品预达简介',
  `diy_arrive_switch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '预达简介开关',
  `diy_arrive_details` varchar(200) DEFAULT NULL COMMENT '预达简介内容',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `goods_id` (`goods_id`),
  KEY `supply_id` (`supply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7983 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品详情表';

");

if(!pdo_fieldexists('lionfish_comshop_good_common','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','quality')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `quality` varchar(50) NOT NULL COMMENT '正品保证'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','seven')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `seven` varchar(50) NOT NULL COMMENT '7天无理由退换'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','repair')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `repair` varchar(50) NOT NULL COMMENT '保修'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','labelname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `labelname` text NOT NULL COMMENT '自定义标签名称'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','per_number')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `per_number` int(10) DEFAULT '0' COMMENT '商品限购数量，0 不限制。'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `supply_id` int(10) DEFAULT '0' COMMENT '供货商id'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','pick_just')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `pick_just` tinyint(1) DEFAULT '0' COMMENT '仅自提'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','pick_up_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `pick_up_type` tinyint(1) DEFAULT NULL COMMENT '1 当日达，2次日达，3隔日达，4自定义'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','pick_up_modify')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `pick_up_modify` varchar(255) DEFAULT NULL COMMENT '自定义自提日期信息'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','one_limit_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `one_limit_count` int(10) DEFAULT '0' COMMENT '单次最多购买次数，0不限制'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','total_limit_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `total_limit_count` int(10) DEFAULT '0' COMMENT '总的最多购买次数，0不限制'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','community_head_commission')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `community_head_commission` float(10,2) DEFAULT '0.00' COMMENT '团长佣金比例'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_modify_head_commission')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_modify_head_commission` tinyint(1) DEFAULT '0' COMMENT '是否开启独立配置团长等级分佣'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','community_head_commission_modify')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `community_head_commission_modify` text COMMENT '自定义团长等级分佣配置信息'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `begin_time` int(10) NOT NULL DEFAULT '0' COMMENT '活动开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '活动结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','share_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `share_title` varchar(255) NOT NULL COMMENT '分享标题'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','print_sub_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `print_sub_title` varchar(200) DEFAULT NULL COMMENT '商品打印简短标题'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','share_description')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `share_description` varchar(255) NOT NULL COMMENT '分享描述'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','wepro_qrcode_image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `wepro_qrcode_image` varchar(255) DEFAULT NULL COMMENT '商品小程序分享背景图'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `content` text NOT NULL COMMENT '商品内容'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','big_img')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `big_img` varchar(255) DEFAULT NULL COMMENT '首页列表大图'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','video')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `video` varchar(255) DEFAULT NULL COMMENT '商品视频'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','goods_share_image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `goods_share_image` varchar(255) DEFAULT NULL COMMENT '商品分享图'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_take_fullreduction')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_take_fullreduction` tinyint(1) DEFAULT '1' COMMENT '是否参加满减'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_new_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_new_buy` tinyint(1) DEFAULT '0' COMMENT '是否新人购买，0否，1 是'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_limit_levelunbuy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_limit_levelunbuy` tinyint(1) DEFAULT '0' COMMENT '默认等级不能买'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_limit_vipmember_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_limit_vipmember_buy` tinyint(1) DEFAULT '0' COMMENT '非VIP 不能购买'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_spike_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_spike_buy` tinyint(1) DEFAULT '0' COMMENT '是否限时秒杀，0不是，1是'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_mb_level_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_mb_level_buy` tinyint(1) DEFAULT '1' COMMENT '是否支持会员折扣购买'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_modify_sendscore')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_modify_sendscore` tinyint(1) DEFAULT '0' COMMENT '是否自定义赠送积分'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','send_socre')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `send_socre` int(10) DEFAULT '0' COMMENT '买一个商品赠送积分多少'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_only_express')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_only_express` tinyint(1) DEFAULT '0' COMMENT '是否仅快递'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_only_hexiao')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_only_hexiao` tinyint(1) DEFAULT '0' COMMENT '是否仅核销，0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','relative_goods_list')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `relative_goods_list` text COMMENT '关联的商品id'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','is_show_arrive')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `is_show_arrive` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品预达简介'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','diy_arrive_switch')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `diy_arrive_switch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '预达简介开关'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','diy_arrive_details')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   `diy_arrive_details` varchar(200) DEFAULT NULL COMMENT '预达简介内容'");}
if(!pdo_fieldexists('lionfish_comshop_good_common','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_good_common','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_good_common','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_common')." ADD   KEY `goods_id` (`goods_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_good_lottery` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `is_open_lottery` tinyint(1) NOT NULL COMMENT '是否开奖，0未开奖，1已经开奖',
  `is_auto_open` tinyint(1) NOT NULL COMMENT '是否自动开奖，0手动，1自动',
  `win_quantity` int(10) NOT NULL COMMENT '中奖人数数量',
  `real_win_quantity` int(10) NOT NULL COMMENT '实际中奖人数',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城抽奖商品表';

");

if(!pdo_fieldexists('lionfish_comshop_good_lottery','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','is_open_lottery')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   `is_open_lottery` tinyint(1) NOT NULL COMMENT '是否开奖，0未开奖，1已经开奖'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','is_auto_open')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   `is_auto_open` tinyint(1) NOT NULL COMMENT '是否自动开奖，0手动，1自动'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','win_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   `win_quantity` int(10) NOT NULL COMMENT '中奖人数数量'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','real_win_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   `real_win_quantity` int(10) NOT NULL COMMENT '实际中奖人数'");}
if(!pdo_fieldexists('lionfish_comshop_good_lottery','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_lottery')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_good_pin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `pinprice` decimal(10,2) NOT NULL COMMENT '拼团价格',
  `pin_count` int(10) NOT NULL COMMENT '拼团人数',
  `pin_hour` decimal(10,2) NOT NULL COMMENT '拼团小时，可以小数点',
  `begin_time` int(10) NOT NULL COMMENT '拼团开始时间',
  `end_time` int(10) NOT NULL COMMENT '拼团结束时间',
  `is_commiss_tuan` tinyint(1) DEFAULT '0' COMMENT '是否佣金团，0否，1是',
  `is_zero_open` tinyint(1) DEFAULT '0' COMMENT '是否0元开团：0否，1是',
  `is_newman` tinyint(1) DEFAULT '0' COMMENT '是否仅新人参加：0否，1是',
  `commiss_type` tinyint(1) DEFAULT '0' COMMENT '0百分比，1固定金额',
  `commiss_money` float(10,2) DEFAULT '0.00' COMMENT '具体佣金比例或佣金',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COMMENT='商城拼团商品表';

");

if(!pdo_fieldexists('lionfish_comshop_good_pin','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','pinprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `pinprice` decimal(10,2) NOT NULL COMMENT '拼团价格'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','pin_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `pin_count` int(10) NOT NULL COMMENT '拼团人数'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','pin_hour')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `pin_hour` decimal(10,2) NOT NULL COMMENT '拼团小时，可以小数点'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `begin_time` int(10) NOT NULL COMMENT '拼团开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `end_time` int(10) NOT NULL COMMENT '拼团结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','is_commiss_tuan')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `is_commiss_tuan` tinyint(1) DEFAULT '0' COMMENT '是否佣金团，0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','is_zero_open')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `is_zero_open` tinyint(1) DEFAULT '0' COMMENT '是否0元开团：0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','is_newman')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `is_newman` tinyint(1) DEFAULT '0' COMMENT '是否仅新人参加：0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','commiss_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `commiss_type` tinyint(1) DEFAULT '0' COMMENT '0百分比，1固定金额'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','commiss_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   `commiss_money` float(10,2) DEFAULT '0.00' COMMENT '具体佣金比例或佣金'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_good_pin_ladder` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `ladder_count` int(10) NOT NULL COMMENT '阶梯团几人',
  `price` decimal(10,2) NOT NULL COMMENT '阶梯价格',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城阶梯团详情表';

");

if(!pdo_fieldexists('lionfish_comshop_good_pin_ladder','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin_ladder')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin_ladder','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin_ladder')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin_ladder','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin_ladder')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin_ladder','ladder_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin_ladder')." ADD   `ladder_count` int(10) NOT NULL COMMENT '阶梯团几人'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin_ladder','price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin_ladder')." ADD   `price` decimal(10,2) NOT NULL COMMENT '阶梯价格'");}
if(!pdo_fieldexists('lionfish_comshop_good_pin_ladder','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_good_pin_ladder')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goodsname` varchar(255) NOT NULL COMMENT '商品名称',
  `subtitle` varchar(255) NOT NULL COMMENT '商品简介',
  `grounding` tinyint(1) NOT NULL COMMENT '商品状态，1上架，0下架， 2 仓库中， 3 回收站',
  `price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `costprice` float(10,2) DEFAULT '0.00' COMMENT '成本价',
  `card_price` float(10,2) DEFAULT '0.00' COMMENT '会员卡商品购买价格',
  `is_take_vipcard` tinyint(1) DEFAULT '0' COMMENT '是否参加会员卡，1参加，0不参加',
  `productprice` decimal(10,2) NOT NULL COMMENT '商品原价',
  `sales` int(10) NOT NULL COMMENT '已出售数',
  `seller_count` int(10) DEFAULT '0' COMMENT '实际销量',
  `type` enum('normal','pin','lottery','oldman','newman','commiss','ladder','flash','spike','integral') DEFAULT 'normal' COMMENT '商品类型',
  `showsales` tinyint(1) NOT NULL COMMENT '显示销量',
  `dispatchtype` tinyint(1) NOT NULL COMMENT '运费类型：0运费模板，1统一运费',
  `dispatchid` int(10) NOT NULL COMMENT '运费模板id',
  `dispatchprice` decimal(10,2) NOT NULL COMMENT '运费价格',
  `codes` varchar(100) NOT NULL COMMENT '商品编码',
  `weight` int(10) NOT NULL COMMENT '商品重量，单位g',
  `total` int(10) NOT NULL COMMENT '商品库存',
  `hasoption` tinyint(1) NOT NULL COMMENT '启用规格，0否，1是',
  `is_index_show` tinyint(1) DEFAULT '0' COMMENT '是否首页推荐，0否，1是',
  `is_seckill` tinyint(1) DEFAULT '0' COMMENT '是否秒杀商品，1是，0否',
  `index_sort` int(10) DEFAULT '0' COMMENT '商品排序',
  `is_all_sale` tinyint(1) DEFAULT '0' COMMENT '是否全部团长可销售',
  `credit` int(10) NOT NULL COMMENT '积分赠送，多少积分',
  `buyagain` int(10) NOT NULL COMMENT '复购折扣',
  `buyagain_condition` tinyint(1) NOT NULL COMMENT '0.订单付款后， 1订单完成后',
  `buyagain_sale` tinyint(1) NOT NULL COMMENT '重复购买时可以使用优惠，1是，0否',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `istop` int(1) DEFAULT '0' COMMENT '是否置顶',
  `settoptime` int(10) DEFAULT '0' COMMENT '置顶时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=7987 DEFAULT CHARSET=utf8mb4 COMMENT='商城普通商品表';

");

if(!pdo_fieldexists('lionfish_comshop_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods','goodsname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `goodsname` varchar(255) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('lionfish_comshop_goods','subtitle')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `subtitle` varchar(255) NOT NULL COMMENT '商品简介'");}
if(!pdo_fieldexists('lionfish_comshop_goods','grounding')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `grounding` tinyint(1) NOT NULL COMMENT '商品状态，1上架，0下架， 2 仓库中， 3 回收站'");}
if(!pdo_fieldexists('lionfish_comshop_goods','price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `price` decimal(10,2) NOT NULL COMMENT '商品价格'");}
if(!pdo_fieldexists('lionfish_comshop_goods','costprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `costprice` float(10,2) DEFAULT '0.00' COMMENT '成本价'");}
if(!pdo_fieldexists('lionfish_comshop_goods','card_price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `card_price` float(10,2) DEFAULT '0.00' COMMENT '会员卡商品购买价格'");}
if(!pdo_fieldexists('lionfish_comshop_goods','is_take_vipcard')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `is_take_vipcard` tinyint(1) DEFAULT '0' COMMENT '是否参加会员卡，1参加，0不参加'");}
if(!pdo_fieldexists('lionfish_comshop_goods','productprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `productprice` decimal(10,2) NOT NULL COMMENT '商品原价'");}
if(!pdo_fieldexists('lionfish_comshop_goods','sales')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `sales` int(10) NOT NULL COMMENT '已出售数'");}
if(!pdo_fieldexists('lionfish_comshop_goods','seller_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `seller_count` int(10) DEFAULT '0' COMMENT '实际销量'");}
if(!pdo_fieldexists('lionfish_comshop_goods','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `type` enum('normal','pin','lottery','oldman','newman','commiss','ladder','flash','spike','integral') DEFAULT 'normal' COMMENT '商品类型'");}
if(!pdo_fieldexists('lionfish_comshop_goods','showsales')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `showsales` tinyint(1) NOT NULL COMMENT '显示销量'");}
if(!pdo_fieldexists('lionfish_comshop_goods','dispatchtype')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `dispatchtype` tinyint(1) NOT NULL COMMENT '运费类型：0运费模板，1统一运费'");}
if(!pdo_fieldexists('lionfish_comshop_goods','dispatchid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `dispatchid` int(10) NOT NULL COMMENT '运费模板id'");}
if(!pdo_fieldexists('lionfish_comshop_goods','dispatchprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `dispatchprice` decimal(10,2) NOT NULL COMMENT '运费价格'");}
if(!pdo_fieldexists('lionfish_comshop_goods','codes')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `codes` varchar(100) NOT NULL COMMENT '商品编码'");}
if(!pdo_fieldexists('lionfish_comshop_goods','weight')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `weight` int(10) NOT NULL COMMENT '商品重量，单位g'");}
if(!pdo_fieldexists('lionfish_comshop_goods','total')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `total` int(10) NOT NULL COMMENT '商品库存'");}
if(!pdo_fieldexists('lionfish_comshop_goods','hasoption')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `hasoption` tinyint(1) NOT NULL COMMENT '启用规格，0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_goods','is_index_show')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `is_index_show` tinyint(1) DEFAULT '0' COMMENT '是否首页推荐，0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_goods','is_seckill')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `is_seckill` tinyint(1) DEFAULT '0' COMMENT '是否秒杀商品，1是，0否'");}
if(!pdo_fieldexists('lionfish_comshop_goods','index_sort')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `index_sort` int(10) DEFAULT '0' COMMENT '商品排序'");}
if(!pdo_fieldexists('lionfish_comshop_goods','is_all_sale')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `is_all_sale` tinyint(1) DEFAULT '0' COMMENT '是否全部团长可销售'");}
if(!pdo_fieldexists('lionfish_comshop_goods','credit')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `credit` int(10) NOT NULL COMMENT '积分赠送，多少积分'");}
if(!pdo_fieldexists('lionfish_comshop_goods','buyagain')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `buyagain` int(10) NOT NULL COMMENT '复购折扣'");}
if(!pdo_fieldexists('lionfish_comshop_goods','buyagain_condition')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `buyagain_condition` tinyint(1) NOT NULL COMMENT '0.订单付款后， 1订单完成后'");}
if(!pdo_fieldexists('lionfish_comshop_goods','buyagain_sale')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `buyagain_sale` tinyint(1) NOT NULL COMMENT '重复购买时可以使用优惠，1是，0否'");}
if(!pdo_fieldexists('lionfish_comshop_goods','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_goods','istop')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `istop` int(1) DEFAULT '0' COMMENT '是否置顶'");}
if(!pdo_fieldexists('lionfish_comshop_goods','settoptime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   `settoptime` int(10) DEFAULT '0' COMMENT '置顶时间'");}
if(!pdo_fieldexists('lionfish_comshop_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `pid` int(11) NOT NULL COMMENT '父类id',
  `is_hot` tinyint(1) NOT NULL COMMENT '推荐首页显示，0否，1是',
  `name` varchar(255) NOT NULL COMMENT '分类名称',
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL COMMENT '顶部主题图',
  `sort_order` int(10) NOT NULL COMMENT '排序，越大排序越前面',
  `cate_type` enum('normal','pintuan','recipe') DEFAULT 'normal' COMMENT '普通商品分类：normal,拼团商品。pintuan',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类是否显示',
  `is_show_topic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示3*3布局',
  `is_type_show` tinyint(1) DEFAULT '0' COMMENT '分类显示',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=877 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品分类表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_category','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','pid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `pid` int(11) NOT NULL COMMENT '父类id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','is_hot')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `is_hot` tinyint(1) NOT NULL COMMENT '推荐首页显示，0否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `name` varchar(255) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `logo` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','banner')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `banner` varchar(255) DEFAULT NULL COMMENT '顶部主题图'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','sort_order')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `sort_order` int(10) NOT NULL COMMENT '排序，越大排序越前面'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','cate_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `cate_type` enum('normal','pintuan','recipe') DEFAULT 'normal' COMMENT '普通商品分类：normal,拼团商品。pintuan'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','is_show')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类是否显示'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','is_show_topic')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `is_show_topic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示3*3布局'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','is_type_show')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   `is_type_show` tinyint(1) DEFAULT '0' COMMENT '分类显示'");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_category')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `image` varchar(255) NOT NULL COMMENT '图片',
  `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=107214 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品图片表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_images','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_images','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_images','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_images','image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD   `image` varchar(255) NOT NULL COMMENT '图片'");}
if(!pdo_fieldexists('lionfish_comshop_goods_images','thumb')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD   `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图'");}
if(!pdo_fieldexists('lionfish_comshop_goods_images','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_images','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_images')." ADD   KEY `goods_id` (`goods_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `title` varchar(100) NOT NULL COMMENT '规格名称',
  `displayorder` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4876 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品规格关联表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_option','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option')." ADD   `title` varchar(100) NOT NULL COMMENT '规格名称'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_option_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_option_id` int(11) NOT NULL COMMENT '商品规格表',
  `title` varchar(200) NOT NULL COMMENT '规格值标题',
  `thumb` varchar(255) NOT NULL COMMENT '规格值图片',
  `displayorder` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`goods_id`),
  KEY `goods_option_id` (`goods_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12408 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品规格选项表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_option_item','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','goods_option_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   `goods_option_id` int(11) NOT NULL COMMENT '商品规格表'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   `title` varchar(200) NOT NULL COMMENT '规格值标题'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','thumb')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   `thumb` varchar(255) NOT NULL COMMENT '规格值图片'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item')." ADD   KEY `uniacid` (`uniacid`,`goods_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_option_item_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `option_item_ids` varchar(100) NOT NULL COMMENT '规格值关联id',
  `productprice` decimal(10,2) NOT NULL COMMENT '商品原价',
  `marketprice` decimal(10,2) NOT NULL COMMENT '商品价格',
  `pinprice` decimal(10,2) DEFAULT '0.00' COMMENT '拼团价格',
  `card_price` float(10,2) DEFAULT '0.00' COMMENT '会员卡购买价格',
  `stock` int(10) NOT NULL COMMENT '库存',
  `costprice` decimal(10,2) NOT NULL COMMENT '商品成本价',
  `goodssn` varchar(100) NOT NULL COMMENT '商品编码',
  `weight` int(10) NOT NULL COMMENT '商品重量',
  `title` varchar(200) NOT NULL COMMENT '商品规格关联标题',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15419 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品规格值关联表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','option_item_ids')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `option_item_ids` varchar(100) NOT NULL COMMENT '规格值关联id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','productprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `productprice` decimal(10,2) NOT NULL COMMENT '商品原价'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','marketprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `marketprice` decimal(10,2) NOT NULL COMMENT '商品价格'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','pinprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `pinprice` decimal(10,2) DEFAULT '0.00' COMMENT '拼团价格'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','card_price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `card_price` float(10,2) DEFAULT '0.00' COMMENT '会员卡购买价格'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','stock')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `stock` int(10) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','costprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `costprice` decimal(10,2) NOT NULL COMMENT '商品成本价'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','goodssn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `goodssn` varchar(100) NOT NULL COMMENT '商品编码'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','weight')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `weight` int(10) NOT NULL COMMENT '商品重量'");}
if(!pdo_fieldexists('lionfish_comshop_goods_option_item_value','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_option_item_value')." ADD   `title` varchar(200) NOT NULL COMMENT '商品规格关联标题'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_salesroom_limit` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `salesroom_id` int(10) NOT NULL COMMENT '门店id',
  `smember_id` int(10) NOT NULL COMMENT '门店下核销员id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `goods_id` (`goods_id`),
  KEY `salesroom_id` (`salesroom_id`),
  KEY `smember_id` (`smember_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城商品核销限制表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','salesroom_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   `salesroom_id` int(10) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','smember_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   `smember_id` int(10) NOT NULL COMMENT '门店下核销员id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   KEY `goods_id` (`goods_id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroom_limit','salesroom_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroom_limit')." ADD   KEY `salesroom_id` (`salesroom_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_salesroombase` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `is_open_hexiao` tinyint(1) NOT NULL COMMENT '是否开启核销，1开启，0未开启',
  `hexiao_method_way` tinyint(1) NOT NULL COMMENT '0：订单核销，1 核销次数',
  `one_hexiao_count` int(10) NOT NULL COMMENT '单个商品核销次数',
  `hexiao_effect_day_type` tinyint(1) NOT NULL COMMENT '0：购买后有效期，1：指定过期时间',
  `hexiao_effect_day` int(10) NOT NULL COMMENT '有效天数',
  `hexiao_effect_begin_time` int(10) NOT NULL COMMENT '过期日期：开始',
  `hexiao_effect_end_time` int(10) NOT NULL COMMENT '过期日期：结束',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniacid` (`uniacid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城商品核销基础表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','is_open_hexiao')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `is_open_hexiao` tinyint(1) NOT NULL COMMENT '是否开启核销，1开启，0未开启'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','hexiao_method_way')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `hexiao_method_way` tinyint(1) NOT NULL COMMENT '0：订单核销，1 核销次数'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','one_hexiao_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `one_hexiao_count` int(10) NOT NULL COMMENT '单个商品核销次数'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','hexiao_effect_day_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `hexiao_effect_day_type` tinyint(1) NOT NULL COMMENT '0：购买后有效期，1：指定过期时间'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','hexiao_effect_day')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `hexiao_effect_day` int(10) NOT NULL COMMENT '有效天数'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','hexiao_effect_begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `hexiao_effect_begin_time` int(10) NOT NULL COMMENT '过期日期：开始'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','hexiao_effect_end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `hexiao_effect_end_time` int(10) NOT NULL COMMENT '过期日期：结束'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_salesroombase','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_salesroombase')." ADD   UNIQUE KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_share_image` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `image_path` varchar(255) NOT NULL COMMENT '图片路径',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`goods_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=9453 DEFAULT CHARSET=utf8 COMMENT='会员商品分享图';

");

if(!pdo_fieldexists('lionfish_comshop_goods_share_image','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','image_path')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   `image_path` varchar(255) NOT NULL COMMENT '图片路径'");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_goods_share_image','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_share_image')." ADD   KEY `member_id` (`member_id`,`goods_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `tagname` varchar(255) NOT NULL COMMENT '标签组名称',
  `type` tinyint(1) DEFAULT '0' COMMENT '类型：0文字 1图片',
  `tag_type` enum('normal','pintuan') DEFAULT 'normal' COMMENT '商品标签类型',
  `tagcontent` text NOT NULL COMMENT '标签名',
  `state` tinyint(1) NOT NULL COMMENT '状态',
  `sort_order` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品标签表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_tags','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','tagname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `tagname` varchar(255) NOT NULL COMMENT '标签组名称'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '类型：0文字 1图片'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','tag_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `tag_type` enum('normal','pintuan') DEFAULT 'normal' COMMENT '商品标签类型'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','tagcontent')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `tagcontent` text NOT NULL COMMENT '标签名'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','sort_order')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   `sort_order` int(10) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_goods_tags','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_tags')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_goods_to_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `cate_id` int(11) NOT NULL COMMENT '分类表',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45829 DEFAULT CHARSET=utf8mb4 COMMENT='商城商品分类关联表';

");

if(!pdo_fieldexists('lionfish_comshop_goods_to_category','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_to_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_to_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_to_category')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_to_category','cate_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_to_category')." ADD   `cate_id` int(11) NOT NULL COMMENT '分类表'");}
if(!pdo_fieldexists('lionfish_comshop_goods_to_category','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_to_category')." ADD   `goods_id` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_goods_to_category','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_goods_to_category')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) DEFAULT '0' COMMENT '小程序id',
  `seller_id` int(10) NOT NULL COMMENT '所属商家',
  `title` varchar(255) NOT NULL COMMENT '圈子标题',
  `post_count` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1，正常。0禁止',
  `member_count` int(10) NOT NULL COMMENT '会员数量',
  `quan_share` varchar(255) DEFAULT NULL COMMENT '圈子分享标题',
  `is_synchro` tinyint(1) DEFAULT '1' COMMENT '同步评论到圈子',
  `limit_send_member` tinyint(1) DEFAULT NULL COMMENT '是否限制会员发布',
  `quan_logo` varchar(255) DEFAULT NULL COMMENT '圈子logo',
  `quan_banner` varchar(255) NOT NULL COMMENT '圈子banner图片',
  `quan_share_desc` varchar(255) NOT NULL COMMENT '圈子分享描述',
  `member_ids` text COMMENT '限制的会员发布id',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子群组表';

");

if(!pdo_fieldexists('lionfish_comshop_group','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '小程序id'");}
if(!pdo_fieldexists('lionfish_comshop_group','seller_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `seller_id` int(10) NOT NULL COMMENT '所属商家'");}
if(!pdo_fieldexists('lionfish_comshop_group','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `title` varchar(255) NOT NULL COMMENT '圈子标题'");}
if(!pdo_fieldexists('lionfish_comshop_group','post_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `post_count` int(10) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_group','status')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `status` tinyint(1) NOT NULL COMMENT '1，正常。0禁止'");}
if(!pdo_fieldexists('lionfish_comshop_group','member_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `member_count` int(10) NOT NULL COMMENT '会员数量'");}
if(!pdo_fieldexists('lionfish_comshop_group','quan_share')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `quan_share` varchar(255) DEFAULT NULL COMMENT '圈子分享标题'");}
if(!pdo_fieldexists('lionfish_comshop_group','is_synchro')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `is_synchro` tinyint(1) DEFAULT '1' COMMENT '同步评论到圈子'");}
if(!pdo_fieldexists('lionfish_comshop_group','limit_send_member')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `limit_send_member` tinyint(1) DEFAULT NULL COMMENT '是否限制会员发布'");}
if(!pdo_fieldexists('lionfish_comshop_group','quan_logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `quan_logo` varchar(255) DEFAULT NULL COMMENT '圈子logo'");}
if(!pdo_fieldexists('lionfish_comshop_group','quan_banner')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `quan_banner` varchar(255) NOT NULL COMMENT '圈子banner图片'");}
if(!pdo_fieldexists('lionfish_comshop_group','quan_share_desc')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `quan_share_desc` varchar(255) NOT NULL COMMENT '圈子分享描述'");}
if(!pdo_fieldexists('lionfish_comshop_group','member_ids')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `member_ids` text COMMENT '限制的会员发布id'");}
if(!pdo_fieldexists('lionfish_comshop_group','create_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   `create_time` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_group','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_group','seller_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group')." ADD   KEY `seller_id` (`seller_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_group_lzl_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号id',
  `post_id` int(10) NOT NULL COMMENT '贴子id',
  `content` text NOT NULL COMMENT '回复内容',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `to_member_id` int(10) NOT NULL COMMENT '回复对象用户id',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群组帖子楼中楼回复表';

");

if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','post_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `post_id` int(10) NOT NULL COMMENT '贴子id'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `content` text NOT NULL COMMENT '回复内容'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `member_id` int(10) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','to_member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `to_member_id` int(10) NOT NULL COMMENT '回复对象用户id'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','status')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `status` tinyint(1) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('lionfish_comshop_group_lzl_reply','create_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_lzl_reply')." ADD   `create_time` int(10) NOT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_group_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号id',
  `group_id` int(10) NOT NULL COMMENT '群组id',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL COMMENT '状态，1正常',
  `last_view` int(10) NOT NULL COMMENT '上次进入该群组时间',
  `position` tinyint(1) NOT NULL COMMENT '1为普通成员，2为管理员，3为创建者',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群组成员表';

");

if(!pdo_fieldexists('lionfish_comshop_group_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','group_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `group_id` int(10) NOT NULL COMMENT '群组id'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `member_id` int(10) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','status')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `status` tinyint(1) NOT NULL COMMENT '状态，1正常'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','last_view')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `last_view` int(10) NOT NULL COMMENT '上次进入该群组时间'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','position')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `position` tinyint(1) NOT NULL COMMENT '1为普通成员，2为管理员，3为创建者'");}
if(!pdo_fieldexists('lionfish_comshop_group_member','create_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_member')." ADD   `create_time` int(10) NOT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_group_post` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `is_vir` tinyint(1) DEFAULT '0' COMMENT '是否虚拟评价同步',
  `avatar` varchar(255) DEFAULT NULL COMMENT '机器人头像',
  `user_name` varchar(255) DEFAULT NULL COMMENT '用户名称',
  `group_id` int(10) NOT NULL COMMENT '群组id',
  `goods_id` int(10) DEFAULT '0' COMMENT '动态商品id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '帖子内容',
  `status` tinyint(1) NOT NULL COMMENT '状态 1正常，0被删除',
  `last_reply_time` int(10) NOT NULL COMMENT '上次回复时间',
  `fav_count` int(10) NOT NULL COMMENT '喜欢量',
  `reply_count` int(10) NOT NULL COMMENT '回复量',
  `is_share` tinyint(1) DEFAULT '0' COMMENT '是否分享',
  `link` varchar(255) DEFAULT NULL COMMENT '跳转地址',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群组帖子表';

");

if(!pdo_fieldexists('lionfish_comshop_group_post','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','is_vir')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `is_vir` tinyint(1) DEFAULT '0' COMMENT '是否虚拟评价同步'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `avatar` varchar(255) DEFAULT NULL COMMENT '机器人头像'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','user_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `user_name` varchar(255) DEFAULT NULL COMMENT '用户名称'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','group_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `group_id` int(10) NOT NULL COMMENT '群组id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `goods_id` int(10) DEFAULT '0' COMMENT '动态商品id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `title` varchar(255) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `content` text NOT NULL COMMENT '帖子内容'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','status')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `status` tinyint(1) NOT NULL COMMENT '状态 1正常，0被删除'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','last_reply_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `last_reply_time` int(10) NOT NULL COMMENT '上次回复时间'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','fav_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `fav_count` int(10) NOT NULL COMMENT '喜欢量'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','reply_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `reply_count` int(10) NOT NULL COMMENT '回复量'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','is_share')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `is_share` tinyint(1) DEFAULT '0' COMMENT '是否分享'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','link')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `link` varchar(255) DEFAULT NULL COMMENT '跳转地址'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','create_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   `create_time` int(10) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('lionfish_comshop_group_post','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_group_post','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post')." ADD   KEY `member_id` (`member_id`,`group_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_group_post_fav` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `post_id` int(10) NOT NULL COMMENT '帖子id',
  `fav_time` int(10) NOT NULL COMMENT '喜欢时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子喜欢记录表';

");

if(!pdo_fieldexists('lionfish_comshop_group_post_fav','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post_fav')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post_fav','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post_fav')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post_fav','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post_fav')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post_fav','post_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post_fav')." ADD   `post_id` int(10) NOT NULL COMMENT '帖子id'");}
if(!pdo_fieldexists('lionfish_comshop_group_post_fav','fav_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post_fav')." ADD   `fav_time` int(10) NOT NULL COMMENT '喜欢时间'");}
if(!pdo_fieldexists('lionfish_comshop_group_post_fav','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_group_post_fav')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_jiapinorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `pin_id` int(10) NOT NULL COMMENT '拼团id',
  `uname` varchar(255) NOT NULL COMMENT '昵称',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `order_sn` varchar(100) NOT NULL COMMENT '订单编号',
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `addtime` int(10) DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_jiapinorder','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `pin_id` int(10) NOT NULL COMMENT '拼团id'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','uname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `uname` varchar(255) NOT NULL COMMENT '昵称'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `avatar` varchar(255) NOT NULL COMMENT '头像'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','order_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `order_sn` varchar(100) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `mobile` varchar(20) NOT NULL COMMENT '手机号'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   `addtime` int(10) DEFAULT NULL COMMENT '加入时间'");}
if(!pdo_fieldexists('lionfish_comshop_jiapinorder','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiapinorder')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_jiauser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `username` varchar(32) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `mobile` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_jiauser','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiauser')." ADD 
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_jiauser','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiauser')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_jiauser','username')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiauser')." ADD   `username` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_jiauser','avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiauser')." ADD   `avatar` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_jiauser','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiauser')." ADD   `mobile` varchar(16) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_jiauser','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_jiauser')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `openid` varchar(255) NOT NULL COMMENT '微信openid',
  `we_openid` varchar(255) NOT NULL COMMENT '小程序openid',
  `unionid` varchar(200) NOT NULL COMMENT '联合id',
  `share_id` int(10) DEFAULT NULL COMMENT '分享人id',
  `agentid` int(10) NOT NULL DEFAULT '0' COMMENT '推荐人id',
  `comsiss_flag` tinyint(1) DEFAULT '0' COMMENT '是否分销商，0 非，1是',
  `comsiss_state` tinyint(1) DEFAULT '0' COMMENT '分销商状态',
  `commission_level_id` int(10) DEFAULT '0' COMMENT '分销等级id',
  `comsiss_time` int(10) DEFAULT NULL COMMENT '成为分销商时间',
  `groupid` int(10) DEFAULT '0' COMMENT '会员分组id',
  `level_id` int(10) DEFAULT '0' COMMENT '会员等级',
  `isblack` tinyint(1) DEFAULT '0' COMMENT '是否黑名单',
  `account_money` float(10,2) DEFAULT '0.00' COMMENT '会员账户余额',
  `score` float(10,2) DEFAULT '0.00' COMMENT '积分',
  `reg_type` enum('weixin','weprogram') NOT NULL COMMENT '会员注册类型',
  `username` varchar(200) NOT NULL COMMENT '会员名称',
  `realname` varchar(100) DEFAULT NULL COMMENT '会员真实姓名',
  `telephone` varchar(30) DEFAULT NULL COMMENT '电话',
  `avatar` varchar(255) NOT NULL COMMENT '会员头像',
  `wepro_qrcode` varchar(255) DEFAULT NULL COMMENT '分销商小程序二维码图',
  `hexiao_qrcod` varchar(255) DEFAULT NULL COMMENT '提货核销二维码',
  `commiss_qrcode` varchar(255) DEFAULT NULL COMMENT '分销商二维码',
  `content` varchar(255) DEFAULT NULL COMMENT '备注',
  `last_login_time` int(10) DEFAULT NULL COMMENT '最后一次登录时间',
  `create_time` int(10) NOT NULL COMMENT '注册时间',
  `last_login_ip` varchar(50) DEFAULT NULL COMMENT '最后一次登录ip',
  `pickup_id` int(10) DEFAULT '0' COMMENT '自提关联id',
  `full_user_name` varchar(200) NOT NULL COMMENT '带logo头像',
  `is_writecommiss_form` tinyint(1) DEFAULT '0' COMMENT '是否收集过表单',
  `is_share_tj` tinyint(1) DEFAULT '0' COMMENT '是否分销条件中，分享成为',
  `commiss_formcontent` text COMMENT '表单收集的内容',
  `card_id` int(10) DEFAULT '0' COMMENT '会员卡id',
  `card_begin_time` int(10) DEFAULT '0' COMMENT '会员卡开始时间',
  `card_end_time` int(10) DEFAULT '0' COMMENT '会员卡结束时间',
  PRIMARY KEY (`member_id`),
  KEY `openid` (`openid`(191)),
  KEY `we_openid` (`we_openid`(191)),
  KEY `agentid` (`agentid`)
) ENGINE=InnoDB AUTO_INCREMENT=89848 DEFAULT CHARSET=utf8mb4 COMMENT='商城会员表';

");

if(!pdo_fieldexists('lionfish_comshop_member','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD 
  `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member','openid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `openid` varchar(255) NOT NULL COMMENT '微信openid'");}
if(!pdo_fieldexists('lionfish_comshop_member','we_openid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `we_openid` varchar(255) NOT NULL COMMENT '小程序openid'");}
if(!pdo_fieldexists('lionfish_comshop_member','unionid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `unionid` varchar(200) NOT NULL COMMENT '联合id'");}
if(!pdo_fieldexists('lionfish_comshop_member','share_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `share_id` int(10) DEFAULT NULL COMMENT '分享人id'");}
if(!pdo_fieldexists('lionfish_comshop_member','agentid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `agentid` int(10) NOT NULL DEFAULT '0' COMMENT '推荐人id'");}
if(!pdo_fieldexists('lionfish_comshop_member','comsiss_flag')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `comsiss_flag` tinyint(1) DEFAULT '0' COMMENT '是否分销商，0 非，1是'");}
if(!pdo_fieldexists('lionfish_comshop_member','comsiss_state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `comsiss_state` tinyint(1) DEFAULT '0' COMMENT '分销商状态'");}
if(!pdo_fieldexists('lionfish_comshop_member','commission_level_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `commission_level_id` int(10) DEFAULT '0' COMMENT '分销等级id'");}
if(!pdo_fieldexists('lionfish_comshop_member','comsiss_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `comsiss_time` int(10) DEFAULT NULL COMMENT '成为分销商时间'");}
if(!pdo_fieldexists('lionfish_comshop_member','groupid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `groupid` int(10) DEFAULT '0' COMMENT '会员分组id'");}
if(!pdo_fieldexists('lionfish_comshop_member','level_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `level_id` int(10) DEFAULT '0' COMMENT '会员等级'");}
if(!pdo_fieldexists('lionfish_comshop_member','isblack')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `isblack` tinyint(1) DEFAULT '0' COMMENT '是否黑名单'");}
if(!pdo_fieldexists('lionfish_comshop_member','account_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `account_money` float(10,2) DEFAULT '0.00' COMMENT '会员账户余额'");}
if(!pdo_fieldexists('lionfish_comshop_member','score')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `score` float(10,2) DEFAULT '0.00' COMMENT '积分'");}
if(!pdo_fieldexists('lionfish_comshop_member','reg_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `reg_type` enum('weixin','weprogram') NOT NULL COMMENT '会员注册类型'");}
if(!pdo_fieldexists('lionfish_comshop_member','username')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `username` varchar(200) NOT NULL COMMENT '会员名称'");}
if(!pdo_fieldexists('lionfish_comshop_member','realname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `realname` varchar(100) DEFAULT NULL COMMENT '会员真实姓名'");}
if(!pdo_fieldexists('lionfish_comshop_member','telephone')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `telephone` varchar(30) DEFAULT NULL COMMENT '电话'");}
if(!pdo_fieldexists('lionfish_comshop_member','avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `avatar` varchar(255) NOT NULL COMMENT '会员头像'");}
if(!pdo_fieldexists('lionfish_comshop_member','wepro_qrcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `wepro_qrcode` varchar(255) DEFAULT NULL COMMENT '分销商小程序二维码图'");}
if(!pdo_fieldexists('lionfish_comshop_member','hexiao_qrcod')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `hexiao_qrcod` varchar(255) DEFAULT NULL COMMENT '提货核销二维码'");}
if(!pdo_fieldexists('lionfish_comshop_member','commiss_qrcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `commiss_qrcode` varchar(255) DEFAULT NULL COMMENT '分销商二维码'");}
if(!pdo_fieldexists('lionfish_comshop_member','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `content` varchar(255) DEFAULT NULL COMMENT '备注'");}
if(!pdo_fieldexists('lionfish_comshop_member','last_login_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `last_login_time` int(10) DEFAULT NULL COMMENT '最后一次登录时间'");}
if(!pdo_fieldexists('lionfish_comshop_member','create_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `create_time` int(10) NOT NULL COMMENT '注册时间'");}
if(!pdo_fieldexists('lionfish_comshop_member','last_login_ip')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `last_login_ip` varchar(50) DEFAULT NULL COMMENT '最后一次登录ip'");}
if(!pdo_fieldexists('lionfish_comshop_member','pickup_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `pickup_id` int(10) DEFAULT '0' COMMENT '自提关联id'");}
if(!pdo_fieldexists('lionfish_comshop_member','full_user_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `full_user_name` varchar(200) NOT NULL COMMENT '带logo头像'");}
if(!pdo_fieldexists('lionfish_comshop_member','is_writecommiss_form')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `is_writecommiss_form` tinyint(1) DEFAULT '0' COMMENT '是否收集过表单'");}
if(!pdo_fieldexists('lionfish_comshop_member','is_share_tj')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `is_share_tj` tinyint(1) DEFAULT '0' COMMENT '是否分销条件中，分享成为'");}
if(!pdo_fieldexists('lionfish_comshop_member','commiss_formcontent')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `commiss_formcontent` text COMMENT '表单收集的内容'");}
if(!pdo_fieldexists('lionfish_comshop_member','card_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `card_id` int(10) DEFAULT '0' COMMENT '会员卡id'");}
if(!pdo_fieldexists('lionfish_comshop_member','card_begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `card_begin_time` int(10) DEFAULT '0' COMMENT '会员卡开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_member','card_end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   `card_end_time` int(10) DEFAULT '0' COMMENT '会员卡结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_member','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   PRIMARY KEY (`member_id`)");}
if(!pdo_fieldexists('lionfish_comshop_member','openid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   KEY `openid` (`openid`(191))");}
if(!pdo_fieldexists('lionfish_comshop_member','we_openid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member')." ADD   KEY `we_openid` (`we_openid`(191))");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_card` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `cardname` varchar(255) NOT NULL COMMENT '会员卡名称',
  `orignprice` float(10,2) NOT NULL COMMENT '原价',
  `price` float(10,2) NOT NULL COMMENT '实际购买价格',
  `expire_day` float(10,2) NOT NULL COMMENT '有效期天数，支持小数点',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='付费会员卡表';

");

if(!pdo_fieldexists('lionfish_comshop_member_card','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','cardname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   `cardname` varchar(255) NOT NULL COMMENT '会员卡名称'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','orignprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   `orignprice` float(10,2) NOT NULL COMMENT '原价'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   `price` float(10,2) NOT NULL COMMENT '实际购买价格'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','expire_day')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   `expire_day` float(10,2) NOT NULL COMMENT '有效期天数，支持小数点'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_card','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_card_equity` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `equity_name` varchar(255) NOT NULL COMMENT '权益名称',
  `image` varchar(255) NOT NULL COMMENT '权益图标',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='会员卡权益';

");

if(!pdo_fieldexists('lionfish_comshop_member_card_equity','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_equity')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_equity','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_equity')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_equity','equity_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_equity')." ADD   `equity_name` varchar(255) NOT NULL COMMENT '权益名称'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_equity','image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_equity')." ADD   `image` varchar(255) NOT NULL COMMENT '权益图标'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_equity','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_equity')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_equity','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_equity')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_card_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_sn` varchar(50) NOT NULL COMMENT '订单编号',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `pay_type` enum('weixin','yuer') NOT NULL COMMENT '支付类型，微信，余额',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0未支付，1已支付',
  `car_id` int(10) NOT NULL COMMENT '购买的 会员卡id',
  `trans_id` varchar(255) DEFAULT NULL COMMENT '微信交易单号',
  `formid` varchar(255) DEFAULT NULL COMMENT '支付formid',
  `expire_day` float(10,2) NOT NULL COMMENT '有效期天数，支持小数点',
  `price` float(10,2) NOT NULL COMMENT '购买价格',
  `order_type` tinyint(1) DEFAULT '1' COMMENT '购买类型：1首次购买，2有效期内续期，3过期后续费',
  `begin_time` int(10) DEFAULT '0' COMMENT '有效期开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '有效期截止时间点',
  `pay_time` int(10) DEFAULT '0' COMMENT '支付的时间点',
  `addtime` int(10) NOT NULL COMMENT '下单的时间',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`),
  KEY `car_id` (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='付费会员卡 购买订单表';

");

if(!pdo_fieldexists('lionfish_comshop_member_card_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','order_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `order_sn` varchar(50) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','pay_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `pay_type` enum('weixin','yuer') NOT NULL COMMENT '支付类型，微信，余额'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0未支付，1已支付'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','car_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `car_id` int(10) NOT NULL COMMENT '购买的 会员卡id'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','trans_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `trans_id` varchar(255) DEFAULT NULL COMMENT '微信交易单号'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','formid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `formid` varchar(255) DEFAULT NULL COMMENT '支付formid'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','expire_day')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `expire_day` float(10,2) NOT NULL COMMENT '有效期天数，支持小数点'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `price` float(10,2) NOT NULL COMMENT '购买价格'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','order_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `order_type` tinyint(1) DEFAULT '1' COMMENT '购买类型：1首次购买，2有效期内续期，3过期后续费'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `begin_time` int(10) DEFAULT '0' COMMENT '有效期开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '有效期截止时间点'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','pay_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `pay_time` int(10) DEFAULT '0' COMMENT '支付的时间点'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   `addtime` int(10) NOT NULL COMMENT '下单的时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','order_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   KEY `order_sn` (`order_sn`)");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_member_card_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_card_order')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_charge_flow` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `formid` varchar(255) DEFAULT NULL COMMENT '充值时候的prepayid',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `trans_id` varchar(100) DEFAULT NULL COMMENT '交易号',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '订单商品id',
  `money` decimal(10,2) NOT NULL COMMENT '充值金额',
  `give_money` float(10,2) DEFAULT '0.00' COMMENT '赠送金额',
  `operate_end_yuer` float(10,2) DEFAULT '0.00' COMMENT '当前操作结束后余额',
  `state` tinyint(1) NOT NULL COMMENT '0，未支付，1已支付,3余额付款，4退款到余额，5后台充值,6 裂变红包,7 会员升级，8后台扣款',
  `charge_time` int(10) NOT NULL COMMENT '充值时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '充值描述',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=817 DEFAULT CHARSET=utf8 COMMENT='商城会员充值记录表';

");

if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','formid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `formid` varchar(255) DEFAULT NULL COMMENT '充值时候的prepayid'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','trans_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `trans_id` varchar(100) DEFAULT NULL COMMENT '交易号'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '订单商品id'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `money` decimal(10,2) NOT NULL COMMENT '充值金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','give_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `give_money` float(10,2) DEFAULT '0.00' COMMENT '赠送金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','operate_end_yuer')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `operate_end_yuer` float(10,2) DEFAULT '0.00' COMMENT '当前操作结束后余额'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `state` tinyint(1) NOT NULL COMMENT '0，未支付，1已支付,3余额付款，4退款到余额，5后台充值,6 裂变红包,7 会员升级，8后台扣款'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','charge_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `charge_time` int(10) NOT NULL COMMENT '充值时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','remark')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `remark` varchar(255) DEFAULT NULL COMMENT '充值描述'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   `add_time` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_member_charge_flow','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_charge_flow')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_commiss` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额',
  `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额',
  `type` tinyint(1) DEFAULT '0' COMMENT '1余额，2 weixin，3alipay，4bank ',
  `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额',
  `bankname` varchar(200) DEFAULT NULL COMMENT '银行名称',
  `bankaccount` varchar(200) DEFAULT NULL COMMENT '卡号',
  `bankusername` varchar(200) DEFAULT NULL COMMENT '持卡人姓名',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_member_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `member_id` int(10) NOT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','dongmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '1余额，2 weixin，3alipay，4bank '");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','getmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','bankname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `bankname` varchar(200) DEFAULT NULL COMMENT '银行名称'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','bankaccount')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `bankaccount` varchar(200) DEFAULT NULL COMMENT '卡号'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','bankusername')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   `bankusername` varchar(200) DEFAULT NULL COMMENT '持卡人姓名'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_commiss_apply` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL,
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `state` tinyint(1) NOT NULL COMMENT '审核状态，0申请中，1已通过，2拒绝',
  `addtime` int(10) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_member_commiss_apply','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_apply')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_apply','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_apply')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_apply','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_apply')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_apply','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_apply')." ADD   `state` tinyint(1) NOT NULL COMMENT '审核状态，0申请中，1已通过，2拒绝'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_apply','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_apply')." ADD   `addtime` int(10) NOT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_apply','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_apply')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_commiss_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `child_member_id` int(10) NOT NULL COMMENT '下级会员ID',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id',
  `level` int(10) DEFAULT '1' COMMENT '第几层分佣',
  `type` tinyint(1) DEFAULT '1' COMMENT '1按照比例。2按照固定金额',
  `bili` float(10,2) DEFAULT '0.00' COMMENT '比例，如果是固定金额，那么这里就是固定金额',
  `commission_level_id` int(10) DEFAULT '0' COMMENT '分销等级id',
  `store_id` int(10) NOT NULL COMMENT '店铺ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','child_member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `child_member_id` int(10) NOT NULL COMMENT '下级会员ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','level')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `level` int(10) DEFAULT '1' COMMENT '第几层分佣'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `type` tinyint(1) DEFAULT '1' COMMENT '1按照比例。2按照固定金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','bili')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `bili` float(10,2) DEFAULT '0.00' COMMENT '比例，如果是固定金额，那么这里就是固定金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','commission_level_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `commission_level_id` int(10) DEFAULT '0' COMMENT '分销等级id'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `store_id` int(10) NOT NULL COMMENT '店铺ID'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_member_commiss_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_commiss_order')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_formid` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `state` tinyint(1) NOT NULL COMMENT '状态，0未使用，已使用',
  `formid` varchar(255) NOT NULL COMMENT '用来群发使用的',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=1288431 DEFAULT CHARSET=utf8 COMMENT='会员的群发formid表';

");

if(!pdo_fieldexists('lionfish_comshop_member_formid','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态，0未使用，已使用'");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','formid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   `formid` varchar(255) NOT NULL COMMENT '用来群发使用的'");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_member_formid','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_formid')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `groupname` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_member_group','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_group')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_member_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_group')." ADD   `uniacid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_member_group','groupname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_group')." ADD   `groupname` varchar(255) DEFAULT ''");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_integral_flow` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `in_out` enum('in','out') NOT NULL COMMENT '增加积分，还是减少积分',
  `type` enum('goodsbuy','refundorder','system_add','system_del','orderbuy','signin_send','integral_exchange') NOT NULL COMMENT '积分获赠/减少 类型',
  `order_id` int(10) DEFAULT NULL COMMENT '订单id',
  `order_goods_id` int(10) DEFAULT NULL COMMENT '订单商品id',
  `score` float(10,2) DEFAULT '0.00' COMMENT '积分数量',
  `after_operate_score` float(10,2) DEFAULT '0.00' COMMENT '操作后的积分',
  `state` tinyint(1) DEFAULT '0' COMMENT '0 还未赠送，1处理成功，',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=30777 DEFAULT CHARSET=utf8 COMMENT='积分流水记录表';

");

if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','in_out')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `in_out` enum('in','out') NOT NULL COMMENT '增加积分，还是减少积分'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `type` enum('goodsbuy','refundorder','system_add','system_del','orderbuy','signin_send','integral_exchange') NOT NULL COMMENT '积分获赠/减少 类型'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `order_id` int(10) DEFAULT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `order_goods_id` int(10) DEFAULT NULL COMMENT '订单商品id'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','score')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `score` float(10,2) DEFAULT '0.00' COMMENT '积分数量'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','after_operate_score')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `after_operate_score` float(10,2) DEFAULT '0.00' COMMENT '操作后的积分'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `state` tinyint(1) DEFAULT '0' COMMENT '0 还未赠送，1处理成功，'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','remark')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `remark` varchar(255) DEFAULT NULL COMMENT '说明'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_integral_flow','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_integral_flow')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_level` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `level` int(11) NOT NULL COMMENT '等级',
  `levelname` varchar(100) NOT NULL COMMENT '等级名称',
  `level_money` decimal(10,2) DEFAULT '0.00' COMMENT '会员升级金额',
  `is_auto_grade` tinyint(1) DEFAULT '0' COMMENT '是否自动升级，0 否，1是',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣',
  `logo` varchar(255) DEFAULT NULL COMMENT '会员等级logo',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='会员等级表';

");

if(!pdo_fieldexists('lionfish_comshop_member_level','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','level')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `level` int(11) NOT NULL COMMENT '等级'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','levelname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `levelname` varchar(100) NOT NULL COMMENT '等级名称'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','level_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `level_money` decimal(10,2) DEFAULT '0.00' COMMENT '会员升级金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','is_auto_grade')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `is_auto_grade` tinyint(1) DEFAULT '0' COMMENT '是否自动升级，0 否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','discount')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   `logo` varchar(255) DEFAULT NULL COMMENT '会员等级logo'");}
if(!pdo_fieldexists('lionfish_comshop_member_level','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_level')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_member_tixian_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL,
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `money` float(10,2) NOT NULL COMMENT '提现金额',
  `service_charge` float(10,2) DEFAULT '0.00' COMMENT '提现手续费比例',
  `service_charge_money` float(10,2) DEFAULT '0.00' COMMENT '提现后实际到账金额',
  `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败',
  `shentime` int(10) NOT NULL COMMENT '审核时间',
  `type` tinyint(1) DEFAULT '0' COMMENT '1余额，2微信 3支付宝 4 银行卡',
  `bankname` varchar(255) DEFAULT NULL COMMENT '银行卡名称',
  `bankaccount` varchar(255) DEFAULT NULL COMMENT '卡号',
  `bankusername` varchar(255) DEFAULT NULL COMMENT '持卡人姓名',
  `addtime` int(10) NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='分销佣金提现表';

");

if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `uniacid` int(10) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `money` float(10,2) NOT NULL COMMENT '提现金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','service_charge')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `service_charge` float(10,2) DEFAULT '0.00' COMMENT '提现手续费比例'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','service_charge_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `service_charge_money` float(10,2) DEFAULT '0.00' COMMENT '提现后实际到账金额'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','shentime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `shentime` int(10) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '1余额，2微信 3支付宝 4 银行卡'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','bankname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `bankname` varchar(255) DEFAULT NULL COMMENT '银行卡名称'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','bankaccount')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `bankaccount` varchar(255) DEFAULT NULL COMMENT '卡号'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','bankusername')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `bankusername` varchar(255) DEFAULT NULL COMMENT '持卡人姓名'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   `addtime` int(10) NOT NULL COMMENT '提交时间'");}
if(!pdo_fieldexists('lionfish_comshop_member_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_member_tixian_order')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_navigat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `navname` varchar(200) DEFAULT NULL COMMENT '名称',
  `thumb` varchar(255) NOT NULL COMMENT '图片地址',
  `link` varchar(255) NOT NULL COMMENT '链接地址',
  `appid` varchar(255) NOT NULL COMMENT '外部小程序appid',
  `type` tinyint(1) NOT NULL COMMENT '0外链 1小程序内链 2外部小程序',
  `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面',
  `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效',
  `addtime` int(10) NOT NULL COMMENT '添加广告时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8 COMMENT='导航图标';

");

if(!pdo_fieldexists('lionfish_comshop_navigat','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','navname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `navname` varchar(200) DEFAULT NULL COMMENT '名称'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','thumb')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `thumb` varchar(255) NOT NULL COMMENT '图片地址'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','link')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `link` varchar(255) NOT NULL COMMENT '链接地址'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','appid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `appid` varchar(255) NOT NULL COMMENT '外部小程序appid'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `type` tinyint(1) NOT NULL COMMENT '0外链 1小程序内链 2外部小程序'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   `addtime` int(10) NOT NULL COMMENT '添加广告时间'");}
if(!pdo_fieldexists('lionfish_comshop_navigat','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_navigat')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `content` varchar(200) NOT NULL COMMENT '公告内容',
  `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面',
  `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COMMENT='商城公告表';

");

if(!pdo_fieldexists('lionfish_comshop_notice','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_notice','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_notice','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD   `content` varchar(200) NOT NULL COMMENT '公告内容'");}
if(!pdo_fieldexists('lionfish_comshop_notice','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD   `displayorder` int(10) NOT NULL COMMENT '排序，越大排序越前面'");}
if(!pdo_fieldexists('lionfish_comshop_notice','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD   `enabled` tinyint(1) NOT NULL COMMENT '是否生效，1生效，0不生效'");}
if(!pdo_fieldexists('lionfish_comshop_notice','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_notice','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notice')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_notify_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `username` varchar(255) NOT NULL COMMENT '会员名称',
  `member_id` int(10) DEFAULT '0',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `order_time` int(10) NOT NULL COMMENT '购买时间',
  `order_id` int(10) DEFAULT '0' COMMENT '订单id',
  `state` int(10) NOT NULL COMMENT '状态，0未发送，1已发送',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  `order_url` varchar(100) NOT NULL COMMENT '订单id',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`),
  KEY `rds_idx_0` (`uniacid`,`state`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=123016 DEFAULT CHARSET=utf8 COMMENT='最新订单消息通知';

");

if(!pdo_fieldexists('lionfish_comshop_notify_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','username')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `username` varchar(255) NOT NULL COMMENT '会员名称'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `member_id` int(10) DEFAULT '0'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `avatar` varchar(255) NOT NULL COMMENT '头像'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','order_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `order_time` int(10) NOT NULL COMMENT '购买时间'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `order_id` int(10) DEFAULT '0' COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `state` int(10) NOT NULL COMMENT '状态，0未发送，1已发送'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `add_time` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','order_url')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   `order_url` varchar(100) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   KEY `member_id` (`member_id`)");}
if(!pdo_fieldexists('lionfish_comshop_notify_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_notify_order')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_num_alias` varchar(40) NOT NULL COMMENT '订单编号',
  `member_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `head_id` int(10) DEFAULT '0' COMMENT '团长id',
  `supply_id` int(10) DEFAULT '0' COMMENT '供应商id',
  `is_vipcard_buy` tinyint(1) DEFAULT '0' COMMENT '是否会员卡购买，0 否，1是',
  `is_level_buy` tinyint(1) DEFAULT '0' COMMENT '是否会员等级购买，1是，0 不是',
  `type` enum('pintuan','normal','lottery','virtual','bargain','integral','community','ignore') DEFAULT 'normal',
  `charge_mobile` varchar(30) DEFAULT NULL COMMENT '充值的手机号',
  `is_pin` tinyint(1) DEFAULT '0' COMMENT '是否拼团，0表示否，1表示是',
  `from_type` enum('weixin','wepro') DEFAULT 'weixin' COMMENT '订单来源：weixin表示服务号，wepro表示小程序',
  `perpay_id` varchar(255) DEFAULT NULL COMMENT '预支付id',
  `name` varchar(32) NOT NULL COMMENT '购买的会员名字',
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL COMMENT '下单会员电话',
  `delivery` enum('express','pickup','tuanz_send') DEFAULT 'express' COMMENT '配送类型',
  `shipping_name` varchar(32) NOT NULL COMMENT '收货人姓名',
  `address_id` int(11) NOT NULL,
  `shipping_tel` varchar(20) NOT NULL COMMENT '收货人电话',
  `shipping_city_id` int(11) NOT NULL,
  `shipping_country_id` int(11) NOT NULL,
  `shipping_stree_id` int(10) DEFAULT NULL COMMENT '街道id',
  `shipping_province_id` int(11) NOT NULL,
  `shipping_address` varchar(255) NOT NULL COMMENT '快递详细地址',
  `tuan_send_address` varchar(255) DEFAULT NULL COMMENT '团长配送，送货详细地址',
  `ziti_name` varchar(255) DEFAULT NULL COMMENT '自提团长姓名',
  `ziti_mobile` varchar(50) DEFAULT NULL COMMENT '自提团长电话',
  `shipping_method` int(11) NOT NULL DEFAULT '0' COMMENT '货运方式，快递类型',
  `dispatchname` varchar(100) DEFAULT NULL COMMENT '配送的快递名称',
  `shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '运费',
  `is_free_shipping_fare` tinyint(1) DEFAULT '0' COMMENT '满金额减运费',
  `fare_shipping_free` float(10,2) DEFAULT '0.00' COMMENT '满x减免运费',
  `man_e_money` float(10,2) DEFAULT '0.00' COMMENT '满多少',
  `old_shipping_fare` decimal(10,2) DEFAULT '0.00' COMMENT '原来运费',
  `changedshipping_fare` decimal(10,2) DEFAULT '0.00' COMMENT '更改的运费',
  `shipping_no` varchar(255) DEFAULT '0' COMMENT '快递单号',
  `shipping_traces` text COMMENT '物流轨迹',
  `payment_code` varchar(128) NOT NULL COMMENT '支付方式',
  `score_for_money` float(10,2) DEFAULT '0.00' COMMENT '积分抵扣金额',
  `transaction_id` varchar(100) NOT NULL COMMENT '微信支付交易号',
  `comment` text NOT NULL,
  `remarksaler` varchar(255) DEFAULT NULL COMMENT '商家备注',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `old_price` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `changedtotal` decimal(10,2) DEFAULT '0.00' COMMENT '更改的订单总价',
  `voucher_id` int(11) NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `voucher_credit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券金额',
  `fullreduction_money` float(10,2) DEFAULT '0.00' COMMENT '满减金额',
  `order_status_id` int(11) NOT NULL,
  `last_refund_order_status_id` int(10) DEFAULT '0' COMMENT '申请退款时，该订单的上一订单状态',
  `is_balance` tinyint(1) NOT NULL DEFAULT '0' COMMENT '未结算',
  `lottery_win` tinyint(1) DEFAULT '0' COMMENT '0未中奖，1中奖',
  `ip` varchar(40) NOT NULL,
  `is_zhuli` tinyint(1) DEFAULT '0' COMMENT '是否砍价，0标识否，2标识砍价',
  `soli_id` int(10) DEFAULT NULL COMMENT '接龙id',
  `is_commission` tinyint(1) DEFAULT '0' COMMENT '是否分销订单',
  `day_paixu` int(10) DEFAULT '0' COMMENT '每日订单排序',
  `is_delivery_flag` tinyint(1) DEFAULT '0' COMMENT '是否已经打包',
  `is_print_suc` tinyint(1) DEFAULT '1' COMMENT '是否打印成功。',
  `ip_region` varchar(20) NOT NULL,
  `user_agent` varchar(255) NOT NULL COMMENT '用户系统信息',
  `date_added` int(11) NOT NULL,
  `date_modified` int(11) NOT NULL,
  `pay_time` int(11) NOT NULL COMMENT '付款时间',
  `canceltime` int(10) DEFAULT NULL COMMENT '取消订单时间',
  `receive_time` int(10) DEFAULT NULL COMMENT '确认收货时间',
  `finishtime` int(10) DEFAULT NULL COMMENT '订单完成时间',
  `express_time` int(10) DEFAULT '0' COMMENT '发货时间',
  `express_tuanz_time` int(10) DEFAULT NULL COMMENT '送达团长时间',
  `shipping_cha_time` int(10) DEFAULT '0' COMMENT '查询时间',
  PRIMARY KEY (`order_id`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`),
  KEY `order_status_id` (`order_status_id`),
  KEY `date_added` (`date_added`),
  KEY `pay_time` (`pay_time`),
  KEY `head_id` (`head_id`),
  KEY `supply_id` (`supply_id`),
  KEY `order_num_alias` (`order_num_alias`),
  KEY `soli_id` (`soli_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130435 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD 
  `order_id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order','order_num_alias')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `order_num_alias` varchar(40) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `member_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id'");}
if(!pdo_fieldexists('lionfish_comshop_order','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `head_id` int(10) DEFAULT '0' COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_comshop_order','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `supply_id` int(10) DEFAULT '0' COMMENT '供应商id'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_vipcard_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_vipcard_buy` tinyint(1) DEFAULT '0' COMMENT '是否会员卡购买，0 否，1是'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_level_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_level_buy` tinyint(1) DEFAULT '0' COMMENT '是否会员等级购买，1是，0 不是'");}
if(!pdo_fieldexists('lionfish_comshop_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `type` enum('pintuan','normal','lottery','virtual','bargain','integral','community','ignore') DEFAULT 'normal'");}
if(!pdo_fieldexists('lionfish_comshop_order','charge_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `charge_mobile` varchar(30) DEFAULT NULL COMMENT '充值的手机号'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_pin')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_pin` tinyint(1) DEFAULT '0' COMMENT '是否拼团，0表示否，1表示是'");}
if(!pdo_fieldexists('lionfish_comshop_order','from_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `from_type` enum('weixin','wepro') DEFAULT 'weixin' COMMENT '订单来源：weixin表示服务号，wepro表示小程序'");}
if(!pdo_fieldexists('lionfish_comshop_order','perpay_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `perpay_id` varchar(255) DEFAULT NULL COMMENT '预支付id'");}
if(!pdo_fieldexists('lionfish_comshop_order','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `name` varchar(32) NOT NULL COMMENT '购买的会员名字'");}
if(!pdo_fieldexists('lionfish_comshop_order','email')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `email` varchar(96) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','telephone')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `telephone` varchar(32) NOT NULL COMMENT '下单会员电话'");}
if(!pdo_fieldexists('lionfish_comshop_order','delivery')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `delivery` enum('express','pickup','tuanz_send') DEFAULT 'express' COMMENT '配送类型'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_name` varchar(32) NOT NULL COMMENT '收货人姓名'");}
if(!pdo_fieldexists('lionfish_comshop_order','address_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `address_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_tel')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_tel` varchar(20) NOT NULL COMMENT '收货人电话'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_city_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_city_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_country_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_country_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_stree_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_stree_id` int(10) DEFAULT NULL COMMENT '街道id'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_province_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_province_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_address` varchar(255) NOT NULL COMMENT '快递详细地址'");}
if(!pdo_fieldexists('lionfish_comshop_order','tuan_send_address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `tuan_send_address` varchar(255) DEFAULT NULL COMMENT '团长配送，送货详细地址'");}
if(!pdo_fieldexists('lionfish_comshop_order','ziti_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `ziti_name` varchar(255) DEFAULT NULL COMMENT '自提团长姓名'");}
if(!pdo_fieldexists('lionfish_comshop_order','ziti_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `ziti_mobile` varchar(50) DEFAULT NULL COMMENT '自提团长电话'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_method')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_method` int(11) NOT NULL DEFAULT '0' COMMENT '货运方式，快递类型'");}
if(!pdo_fieldexists('lionfish_comshop_order','dispatchname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `dispatchname` varchar(100) DEFAULT NULL COMMENT '配送的快递名称'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '运费'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_free_shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_free_shipping_fare` tinyint(1) DEFAULT '0' COMMENT '满金额减运费'");}
if(!pdo_fieldexists('lionfish_comshop_order','fare_shipping_free')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `fare_shipping_free` float(10,2) DEFAULT '0.00' COMMENT '满x减免运费'");}
if(!pdo_fieldexists('lionfish_comshop_order','man_e_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `man_e_money` float(10,2) DEFAULT '0.00' COMMENT '满多少'");}
if(!pdo_fieldexists('lionfish_comshop_order','old_shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `old_shipping_fare` decimal(10,2) DEFAULT '0.00' COMMENT '原来运费'");}
if(!pdo_fieldexists('lionfish_comshop_order','changedshipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `changedshipping_fare` decimal(10,2) DEFAULT '0.00' COMMENT '更改的运费'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_no')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_no` varchar(255) DEFAULT '0' COMMENT '快递单号'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_traces')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_traces` text COMMENT '物流轨迹'");}
if(!pdo_fieldexists('lionfish_comshop_order','payment_code')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `payment_code` varchar(128) NOT NULL COMMENT '支付方式'");}
if(!pdo_fieldexists('lionfish_comshop_order','score_for_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `score_for_money` float(10,2) DEFAULT '0.00' COMMENT '积分抵扣金额'");}
if(!pdo_fieldexists('lionfish_comshop_order','transaction_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `transaction_id` varchar(100) NOT NULL COMMENT '微信支付交易号'");}
if(!pdo_fieldexists('lionfish_comshop_order','comment')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `comment` text NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','remarksaler')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `remarksaler` varchar(255) DEFAULT NULL COMMENT '商家备注'");}
if(!pdo_fieldexists('lionfish_comshop_order','total')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `total` decimal(15,4) NOT NULL DEFAULT '0.0000'");}
if(!pdo_fieldexists('lionfish_comshop_order','old_price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `old_price` decimal(10,2) DEFAULT '0.00' COMMENT '原价'");}
if(!pdo_fieldexists('lionfish_comshop_order','changedtotal')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `changedtotal` decimal(10,2) DEFAULT '0.00' COMMENT '更改的订单总价'");}
if(!pdo_fieldexists('lionfish_comshop_order','voucher_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `voucher_id` int(11) NOT NULL DEFAULT '0' COMMENT '优惠券id'");}
if(!pdo_fieldexists('lionfish_comshop_order','voucher_credit')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `voucher_credit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券金额'");}
if(!pdo_fieldexists('lionfish_comshop_order','fullreduction_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `fullreduction_money` float(10,2) DEFAULT '0.00' COMMENT '满减金额'");}
if(!pdo_fieldexists('lionfish_comshop_order','order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `order_status_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','last_refund_order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `last_refund_order_status_id` int(10) DEFAULT '0' COMMENT '申请退款时，该订单的上一订单状态'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_balance')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_balance` tinyint(1) NOT NULL DEFAULT '0' COMMENT '未结算'");}
if(!pdo_fieldexists('lionfish_comshop_order','lottery_win')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `lottery_win` tinyint(1) DEFAULT '0' COMMENT '0未中奖，1中奖'");}
if(!pdo_fieldexists('lionfish_comshop_order','ip')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `ip` varchar(40) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','is_zhuli')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_zhuli` tinyint(1) DEFAULT '0' COMMENT '是否砍价，0标识否，2标识砍价'");}
if(!pdo_fieldexists('lionfish_comshop_order','soli_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `soli_id` int(10) DEFAULT NULL COMMENT '接龙id'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_commission')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_commission` tinyint(1) DEFAULT '0' COMMENT '是否分销订单'");}
if(!pdo_fieldexists('lionfish_comshop_order','day_paixu')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `day_paixu` int(10) DEFAULT '0' COMMENT '每日订单排序'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_delivery_flag')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_delivery_flag` tinyint(1) DEFAULT '0' COMMENT '是否已经打包'");}
if(!pdo_fieldexists('lionfish_comshop_order','is_print_suc')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `is_print_suc` tinyint(1) DEFAULT '1' COMMENT '是否打印成功。'");}
if(!pdo_fieldexists('lionfish_comshop_order','ip_region')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `ip_region` varchar(20) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','user_agent')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `user_agent` varchar(255) NOT NULL COMMENT '用户系统信息'");}
if(!pdo_fieldexists('lionfish_comshop_order','date_added')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `date_added` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','date_modified')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `date_modified` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order','pay_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `pay_time` int(11) NOT NULL COMMENT '付款时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','canceltime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `canceltime` int(10) DEFAULT NULL COMMENT '取消订单时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','receive_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `receive_time` int(10) DEFAULT NULL COMMENT '确认收货时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','finishtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `finishtime` int(10) DEFAULT NULL COMMENT '订单完成时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','express_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `express_time` int(10) DEFAULT '0' COMMENT '发货时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','express_tuanz_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `express_tuanz_time` int(10) DEFAULT NULL COMMENT '送达团长时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','shipping_cha_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   `shipping_cha_time` int(10) DEFAULT '0' COMMENT '查询时间'");}
if(!pdo_fieldexists('lionfish_comshop_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   PRIMARY KEY (`order_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `member_id` (`member_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order','order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `order_status_id` (`order_status_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order','date_added')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `date_added` (`date_added`)");}
if(!pdo_fieldexists('lionfish_comshop_order','pay_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `pay_time` (`pay_time`)");}
if(!pdo_fieldexists('lionfish_comshop_order','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `head_id` (`head_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `supply_id` (`supply_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order','order_num_alias')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order')." ADD   KEY `order_num_alias` (`order_num_alias`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_all` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id，',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '所属会员',
  `is_pin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否拼团订单',
  `total_money` float(10,2) DEFAULT '0.00' COMMENT '总需要支付金额',
  `order_num_alias` varchar(200) DEFAULT NULL COMMENT '支付的订单号',
  `order_status_id` int(10) NOT NULL COMMENT '订单状态id',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT '微信交易号',
  `out_trade_no` varchar(100) DEFAULT '' COMMENT '提交给微信的订单号',
  `paytime` int(10) NOT NULL COMMENT '支付时间',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=136967 DEFAULT CHARSET=utf8 COMMENT='订单总表，支付时候使用';

");

if(!pdo_fieldexists('lionfish_comshop_order_all','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id，'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `member_id` int(10) NOT NULL COMMENT '所属会员'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','is_pin')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `is_pin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否拼团订单'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','total_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `total_money` float(10,2) DEFAULT '0.00' COMMENT '总需要支付金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','order_num_alias')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `order_num_alias` varchar(200) DEFAULT NULL COMMENT '支付的订单号'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `order_status_id` int(10) NOT NULL COMMENT '订单状态id'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','transaction_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `transaction_id` varchar(255) DEFAULT NULL COMMENT '微信交易号'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','out_trade_no')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `out_trade_no` varchar(100) DEFAULT '' COMMENT '提交给微信的订单号'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','paytime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `paytime` int(10) NOT NULL COMMENT '支付时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_all','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_all','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_all')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_comment` (
  `comment_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单id',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品id',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示，0审核中，1：显示',
  `member_id` int(10) NOT NULL DEFAULT '0' COMMENT '会员id',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `goods_image` text COMMENT '商品图片',
  `order_num_alias` varchar(100) DEFAULT NULL COMMENT '订单编号',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `user_name` varchar(100) DEFAULT NULL COMMENT '昵称',
  `type` tinyint(1) DEFAULT '0' COMMENT '0真实评价，1虚拟评价',
  `gd_type` enum('normal','pintuan') DEFAULT 'normal' COMMENT 'normal普通商品。pintuan 拼团商品',
  `star` int(10) NOT NULL DEFAULT '5' COMMENT '评价星级',
  `star3` int(10) DEFAULT NULL COMMENT '质量满意',
  `star2` int(10) DEFAULT NULL COMMENT '价格合理',
  `images` text COMMENT '图片',
  `is_picture` tinyint(1) DEFAULT '0' COMMENT '是否有图片',
  `content` text NOT NULL COMMENT '评价内容',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '评价时间',
  PRIMARY KEY (`comment_id`),
  KEY `goods_id` (`goods_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=794 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_order_comment','comment_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD 
  `comment_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '评论id'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示，0审核中，1：显示'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `member_id` int(10) NOT NULL DEFAULT '0' COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','goods_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','goods_image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `goods_image` text COMMENT '商品图片'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','order_num_alias')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `order_num_alias` varchar(100) DEFAULT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','avatar')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `avatar` varchar(255) DEFAULT NULL COMMENT '头像'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','user_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `user_name` varchar(100) DEFAULT NULL COMMENT '昵称'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '0真实评价，1虚拟评价'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','gd_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `gd_type` enum('normal','pintuan') DEFAULT 'normal' COMMENT 'normal普通商品。pintuan 拼团商品'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','star')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `star` int(10) NOT NULL DEFAULT '5' COMMENT '评价星级'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','star3')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `star3` int(10) DEFAULT NULL COMMENT '质量满意'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','star2')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `star2` int(10) DEFAULT NULL COMMENT '价格合理'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','images')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `images` text COMMENT '图片'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','is_picture')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `is_picture` tinyint(1) DEFAULT '0' COMMENT '是否有图片'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `content` text NOT NULL COMMENT '评价内容'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '评价时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','comment_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   PRIMARY KEY (`comment_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_comment','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_comment')." ADD   KEY `goods_id` (`goods_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_goods` (
  `order_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号id',
  `goods_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `supply_id` int(10) DEFAULT NULL COMMENT '供应商id',
  `is_pin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否拼团商品订单，0：单独购买，1：拼团商品',
  `pin_id` int(10) DEFAULT NULL,
  `shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '运费',
  `fare_shipping_free` float(10,2) DEFAULT '0.00' COMMENT '满x金额减运费',
  `name` varchar(255) NOT NULL,
  `head_disc` int(10) DEFAULT '100' COMMENT '团长折扣',
  `member_disc` int(10) DEFAULT '100' COMMENT '会员折扣，100 表示不打折',
  `level_name` varchar(200) DEFAULT NULL COMMENT '会员折扣等级名称',
  `goods_images` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `goods_type` enum('pintuan','normal') DEFAULT 'normal' COMMENT '商品类型',
  `model` varchar(64) NOT NULL,
  `quantity` int(4) NOT NULL,
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `changeprice` decimal(10,2) DEFAULT '0.00' COMMENT '更改价格',
  `oldprice` decimal(10,2) DEFAULT '0.00' COMMENT '旧的商品总价',
  `fullreduction_money` float(10,2) DEFAULT '0.00' COMMENT '满减金额',
  `voucher_credit` float(10,2) DEFAULT '0.00' COMMENT '优惠券优惠金额',
  `score_for_money` float(10,2) DEFAULT '0.00' COMMENT '积分抵扣金额',
  `fenbi_li` float(10,2) DEFAULT '0.00' COMMENT '订单中金额占比',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `has_refund_money` float(10,2) DEFAULT '0.00' COMMENT '已经退款金额',
  `has_refund_quantity` int(10) DEFAULT '0' COMMENT '已经退款数量',
  `is_vipcard_buy` tinyint(1) DEFAULT '0' COMMENT '0非vip价格购买，1是',
  `is_level_buy` tinyint(1) DEFAULT '0' COMMENT '是否会员等级购买，1是，0 不是',
  `old_total` float(10,2) DEFAULT '0.00' COMMENT '原始总价',
  `rela_goodsoption_valueid` varchar(50) DEFAULT NULL COMMENT '关联属性id',
  `is_refund_state` tinyint(1) DEFAULT '0' COMMENT '是否退款，0未退款，1已退款',
  `is_statements_state` tinyint(1) DEFAULT '1' COMMENT '是否结算',
  `statements_end_time` int(10) DEFAULT '0' COMMENT '结算时间',
  `comment` varchar(255) DEFAULT NULL COMMENT '会员留言',
  `commiss_one_money` float(10,2) DEFAULT '0.00' COMMENT '一级分佣金额',
  `commiss_two_money` float(10,2) DEFAULT '0.00' COMMENT '二级分佣金额',
  `commiss_three_money` float(10,2) DEFAULT '0.00' COMMENT '三级分佣金额',
  `commiss_fen_one_money` float(10,2) DEFAULT '0.00' COMMENT '分享一级佣金',
  `commiss_fen_two_money` float(10,2) DEFAULT '0.00' COMMENT '分享二级佣金',
  `commiss_fen_three_money` float(10,2) DEFAULT '0.00' COMMENT '分享三级佣金',
  `addtime` int(11) DEFAULT '1538487932' COMMENT '订单生成时间',
  PRIMARY KEY (`order_goods_id`),
  KEY `store_id` (`store_id`),
  KEY `order_id` (`order_id`),
  KEY `pin_id` (`pin_id`),
  KEY `uniacid` (`uniacid`),
  KEY `supply_id` (`supply_id`),
  KEY `goods_id` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_2` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_3` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_4` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_5` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_6` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_7` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_8` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_9` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_10` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_11` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_12` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_13` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_14` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_15` (`goods_id`,`rela_goodsoption_valueid`),
  KEY `goods_id_16` (`goods_id`,`rela_goodsoption_valueid`)
) ENGINE=InnoDB AUTO_INCREMENT=189307 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_order_goods','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD 
  `order_goods_id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `order_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `goods_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `supply_id` int(10) DEFAULT NULL COMMENT '供应商id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','is_pin')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `is_pin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否拼团商品订单，0：单独购买，1：拼团商品'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `pin_id` int(10) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','shipping_fare')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `shipping_fare` float(10,2) DEFAULT '0.00' COMMENT '运费'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','fare_shipping_free')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `fare_shipping_free` float(10,2) DEFAULT '0.00' COMMENT '满x金额减运费'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `name` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','head_disc')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `head_disc` int(10) DEFAULT '100' COMMENT '团长折扣'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','member_disc')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `member_disc` int(10) DEFAULT '100' COMMENT '会员折扣，100 表示不打折'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','level_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `level_name` varchar(200) DEFAULT NULL COMMENT '会员折扣等级名称'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_images')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `goods_images` varchar(255) DEFAULT NULL COMMENT '商品图片'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `goods_type` enum('pintuan','normal') DEFAULT 'normal' COMMENT '商品类型'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','model')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `model` varchar(64) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `quantity` int(4) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','price')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `price` decimal(15,4) NOT NULL DEFAULT '0.0000'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','changeprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `changeprice` decimal(10,2) DEFAULT '0.00' COMMENT '更改价格'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','oldprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `oldprice` decimal(10,2) DEFAULT '0.00' COMMENT '旧的商品总价'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','fullreduction_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `fullreduction_money` float(10,2) DEFAULT '0.00' COMMENT '满减金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','voucher_credit')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `voucher_credit` float(10,2) DEFAULT '0.00' COMMENT '优惠券优惠金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','score_for_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `score_for_money` float(10,2) DEFAULT '0.00' COMMENT '积分抵扣金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','fenbi_li')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `fenbi_li` float(10,2) DEFAULT '0.00' COMMENT '订单中金额占比'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','total')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `total` decimal(15,4) NOT NULL DEFAULT '0.0000'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','has_refund_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `has_refund_money` float(10,2) DEFAULT '0.00' COMMENT '已经退款金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','has_refund_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `has_refund_quantity` int(10) DEFAULT '0' COMMENT '已经退款数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','is_vipcard_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `is_vipcard_buy` tinyint(1) DEFAULT '0' COMMENT '0非vip价格购买，1是'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','is_level_buy')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `is_level_buy` tinyint(1) DEFAULT '0' COMMENT '是否会员等级购买，1是，0 不是'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','old_total')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `old_total` float(10,2) DEFAULT '0.00' COMMENT '原始总价'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','rela_goodsoption_valueid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `rela_goodsoption_valueid` varchar(50) DEFAULT NULL COMMENT '关联属性id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','is_refund_state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `is_refund_state` tinyint(1) DEFAULT '0' COMMENT '是否退款，0未退款，1已退款'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','is_statements_state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `is_statements_state` tinyint(1) DEFAULT '1' COMMENT '是否结算'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','statements_end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `statements_end_time` int(10) DEFAULT '0' COMMENT '结算时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','comment')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `comment` varchar(255) DEFAULT NULL COMMENT '会员留言'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','commiss_one_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `commiss_one_money` float(10,2) DEFAULT '0.00' COMMENT '一级分佣金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','commiss_two_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `commiss_two_money` float(10,2) DEFAULT '0.00' COMMENT '二级分佣金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','commiss_three_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `commiss_three_money` float(10,2) DEFAULT '0.00' COMMENT '三级分佣金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','commiss_fen_one_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `commiss_fen_one_money` float(10,2) DEFAULT '0.00' COMMENT '分享一级佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','commiss_fen_two_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `commiss_fen_two_money` float(10,2) DEFAULT '0.00' COMMENT '分享二级佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','commiss_fen_three_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `commiss_fen_three_money` float(10,2) DEFAULT '0.00' COMMENT '分享三级佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   `addtime` int(11) DEFAULT '1538487932' COMMENT '订单生成时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   PRIMARY KEY (`order_goods_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `store_id` (`store_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `order_id` (`order_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `pin_id` (`pin_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `supply_id` (`supply_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_2')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_2` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_3')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_3` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_4')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_4` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_5')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_5` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_6')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_6` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_7')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_7` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_8')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_8` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_9')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_9` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_10')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_10` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_11')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_11` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_12')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_12` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_13')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_13` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_14')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_14` (`goods_id`,`rela_goodsoption_valueid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods','goods_id_15')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods')." ADD   KEY `goods_id_15` (`goods_id`,`rela_goodsoption_valueid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_goods_refund` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_goods_id` int(10) NOT NULL COMMENT '商品订单id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `real_refund_quantity` int(10) DEFAULT '0' COMMENT '实际退款的数量',
  `quantity` int(10) NOT NULL COMMENT '退款数量',
  `money` float(10,2) NOT NULL COMMENT '退款金额',
  `order_status_id` int(10) NOT NULL COMMENT '退款时，订单状态',
  `is_back_quantity` tinyint(1) DEFAULT NULL COMMENT '是否退商品库存',
  `back_score_for_money` float(10,2) NOT NULL COMMENT '退还积分兑换商品的积分',
  `back_send_score` float(10,2) NOT NULL COMMENT '退还赠送积分',
  `back_head_orderbuycommiss` float(10,2) NOT NULL COMMENT '退还团长佣金',
  `back_head_supplycommiss` float(10,2) DEFAULT '0.00' COMMENT '退还供应商佣金',
  `back_head_commiss_1` float(10,2) NOT NULL COMMENT '退1级团长佣金',
  `back_head_commiss_2` float(10,2) NOT NULL COMMENT '退2级团长佣金',
  `back_head_commiss_3` float(10,2) NOT NULL COMMENT '退3级团长佣金',
  `back_member_commiss_1` float(10,2) NOT NULL COMMENT '退会员1级佣金',
  `back_member_commiss_2` float(10,2) NOT NULL COMMENT '退会员2级佣金',
  `back_member_commiss_3` float(10,2) NOT NULL COMMENT '退会员3级佣金',
  `addtime` int(10) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `order_goods_id` (`order_goods_id`),
  KEY `order_id` (`order_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COMMENT='商城订单商品退款记录表';

");

if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `order_goods_id` int(10) NOT NULL COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','real_refund_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `real_refund_quantity` int(10) DEFAULT '0' COMMENT '实际退款的数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `quantity` int(10) NOT NULL COMMENT '退款数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `money` float(10,2) NOT NULL COMMENT '退款金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `order_status_id` int(10) NOT NULL COMMENT '退款时，订单状态'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','is_back_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `is_back_quantity` tinyint(1) DEFAULT NULL COMMENT '是否退商品库存'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_score_for_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_score_for_money` float(10,2) NOT NULL COMMENT '退还积分兑换商品的积分'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_send_score')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_send_score` float(10,2) NOT NULL COMMENT '退还赠送积分'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_head_orderbuycommiss')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_head_orderbuycommiss` float(10,2) NOT NULL COMMENT '退还团长佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_head_supplycommiss')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_head_supplycommiss` float(10,2) DEFAULT '0.00' COMMENT '退还供应商佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_head_commiss_1')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_head_commiss_1` float(10,2) NOT NULL COMMENT '退1级团长佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_head_commiss_2')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_head_commiss_2` float(10,2) NOT NULL COMMENT '退2级团长佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_head_commiss_3')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_head_commiss_3` float(10,2) NOT NULL COMMENT '退3级团长佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_member_commiss_1')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_member_commiss_1` float(10,2) NOT NULL COMMENT '退会员1级佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_member_commiss_2')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_member_commiss_2` float(10,2) NOT NULL COMMENT '退会员2级佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','back_member_commiss_3')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `back_member_commiss_3` float(10,2) NOT NULL COMMENT '退会员3级佣金'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   `addtime` int(10) NOT NULL COMMENT '操作时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   KEY `order_goods_id` (`order_goods_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_refund','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_refund')." ADD   KEY `order_id` (`order_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_goods_saleshexiao` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_goods_id` int(10) NOT NULL COMMENT '商品订单id',
  `goods_id` int(10) NOT NULL COMMENT '商品id ',
  `hexiao_count` int(10) NOT NULL COMMENT '总的可核销数量',
  `del_hexiao_count` int(10) NOT NULL COMMENT '剩余可核销数量',
  `is_hexiao_over` tinyint(1) NOT NULL COMMENT '是否核销完毕，0未，1是',
  `effect_begin_time` int(10) NOT NULL COMMENT '有效时间开始',
  `effect_end_time` int(10) NOT NULL COMMENT '有效时间结束',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `order_id` (`order_id`),
  KEY `order_goods_id` (`order_goods_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城订单核销信息表';

");

if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `order_goods_id` int(10) NOT NULL COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id '");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','hexiao_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `hexiao_count` int(10) NOT NULL COMMENT '总的可核销数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','del_hexiao_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `del_hexiao_count` int(10) NOT NULL COMMENT '剩余可核销数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','is_hexiao_over')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `is_hexiao_over` tinyint(1) NOT NULL COMMENT '是否核销完毕，0未，1是'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','effect_begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `effect_begin_time` int(10) NOT NULL COMMENT '有效时间开始'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','effect_end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `effect_end_time` int(10) NOT NULL COMMENT '有效时间结束'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   KEY `order_id` (`order_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao')." ADD   KEY `order_goods_id` (`order_goods_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_goods_saleshexiao_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_goods_id` int(10) NOT NULL COMMENT '商品订单id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `hexiao_count` int(10) NOT NULL COMMENT '本次核销数量',
  `salesroom_id` int(10) NOT NULL COMMENT '核销门店id',
  `salesroom_name` varchar(255) NOT NULL COMMENT '核销门店名称',
  `salesmember_id` int(10) NOT NULL COMMENT '核销员id',
  `smember_name` varchar(200) NOT NULL COMMENT '核销员名称',
  `member_id` int(10) NOT NULL COMMENT '核销员会员id',
  `addtime` int(10) NOT NULL COMMENT '核销时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `order_id` (`order_id`),
  KEY `order_goods_id` (`order_goods_id`),
  KEY `goods_id` (`goods_id`),
  KEY `salesroom_id` (`salesroom_id`),
  KEY `salesmember_id` (`salesmember_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城订单核销记录表';

");

if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `order_goods_id` int(10) NOT NULL COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','hexiao_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `hexiao_count` int(10) NOT NULL COMMENT '本次核销数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','salesroom_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `salesroom_id` int(10) NOT NULL COMMENT '核销门店id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','salesroom_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `salesroom_name` varchar(255) NOT NULL COMMENT '核销门店名称'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','salesmember_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `salesmember_id` int(10) NOT NULL COMMENT '核销员id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','smember_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `smember_name` varchar(200) NOT NULL COMMENT '核销员名称'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `member_id` int(10) NOT NULL COMMENT '核销员会员id'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   `addtime` int(10) NOT NULL COMMENT '核销时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   KEY `order_id` (`order_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   KEY `order_goods_id` (`order_goods_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   KEY `goods_id` (`goods_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','salesroom_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   KEY `salesroom_id` (`salesroom_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_goods_saleshexiao_record','salesmember_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_goods_saleshexiao_record')." ADD   KEY `salesmember_id` (`salesmember_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_history` (
  `order_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(11) NOT NULL,
  `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id',
  `order_status_id` int(5) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`order_history_id`),
  KEY `uniacid` (`uniacid`),
  KEY `order_id` (`order_id`,`order_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=567913 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_order_history','order_history_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD 
  `order_history_id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_order_history','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_history','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `order_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_history','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_history','order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `order_status_id` int(5) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_history','notify')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `notify` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_history','comment')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `comment` text NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_history','date_added')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   `date_added` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_history','order_history_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   PRIMARY KEY (`order_history_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_history','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_history')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_option` (
  `order_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(11) NOT NULL,
  `order_goods_id` int(11) NOT NULL,
  `goods_option_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`order_option_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=4967 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_order_option','order_option_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD 
  `order_option_id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_order_option','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_option','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   `order_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_option','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   `order_goods_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_option','goods_option_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   `goods_option_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_option','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   `name` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_option','value')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   `value` text NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_option','order_option_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_option')." ADD   PRIMARY KEY (`order_option_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_quantity_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `quantity` int(10) NOT NULL COMMENT '数量',
  `last_quantity` int(10) DEFAULT NULL COMMENT '上一次库存',
  `rela_goodsoption_value_id` int(10) NOT NULL COMMENT '相关商品规格',
  `addtime` int(10) NOT NULL COMMENT '库存变更时间',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0减少库存，1增加库存',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=193494 DEFAULT CHARSET=utf8 COMMENT='库存记录表';

");

if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `quantity` int(10) NOT NULL COMMENT '数量'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','last_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `last_quantity` int(10) DEFAULT NULL COMMENT '上一次库存'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','rela_goodsoption_value_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `rela_goodsoption_value_id` int(10) NOT NULL COMMENT '相关商品规格'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `addtime` int(10) NOT NULL COMMENT '库存变更时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0减少库存，1增加库存'");}
if(!pdo_fieldexists('lionfish_comshop_order_quantity_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_quantity_order')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_refund` (
  `ref_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '退款自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `store_id` int(10) DEFAULT '0' COMMENT '供应商id',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '退款子订单编号',
  `real_refund_quantity` int(10) DEFAULT '0' COMMENT '退款几个商品',
  `head_id` int(10) DEFAULT '0' COMMENT '团长id',
  `ref_type` tinyint(1) NOT NULL COMMENT '退款类型：1仅退款，2退款退货',
  `ref_money` float(10,2) NOT NULL COMMENT '退款金额',
  `ref_member_id` int(10) NOT NULL COMMENT '会员id',
  `ref_name` varchar(255) NOT NULL COMMENT '退款原因',
  `ref_mobile` varchar(50) NOT NULL COMMENT '联系人手机号',
  `complaint_name` varchar(255) DEFAULT NULL COMMENT '联系人姓名',
  `ref_description` text COMMENT '问题描述',
  `state` tinyint(1) NOT NULL COMMENT '退款状态：0申请中，1商家拒绝，2平台介入，3退款成功，4退款失败,5:撤销申请',
  `remarkrefund` varchar(255) DEFAULT NULL COMMENT '管理员备注',
  `modify_time` int(10) DEFAULT '0' COMMENT '退款订单管理员修改时间',
  `addtime` int(10) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`ref_id`),
  KEY `ref_member_id` (`ref_member_id`),
  KEY `uniacid` (`uniacid`),
  KEY `head_id` (`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2010 DEFAULT CHARSET=utf8 COMMENT='用户退款表';

");

if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD 
  `ref_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '退款自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `store_id` int(10) DEFAULT '0' COMMENT '供应商id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '退款子订单编号'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','real_refund_quantity')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `real_refund_quantity` int(10) DEFAULT '0' COMMENT '退款几个商品'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `head_id` int(10) DEFAULT '0' COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `ref_type` tinyint(1) NOT NULL COMMENT '退款类型：1仅退款，2退款退货'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `ref_money` float(10,2) NOT NULL COMMENT '退款金额'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `ref_member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `ref_name` varchar(255) NOT NULL COMMENT '退款原因'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `ref_mobile` varchar(50) NOT NULL COMMENT '联系人手机号'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','complaint_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `complaint_name` varchar(255) DEFAULT NULL COMMENT '联系人姓名'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_description')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `ref_description` text COMMENT '问题描述'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `state` tinyint(1) NOT NULL COMMENT '退款状态：0申请中，1商家拒绝，2平台介入，3退款成功，4退款失败,5:撤销申请'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','remarkrefund')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `remarkrefund` varchar(255) DEFAULT NULL COMMENT '管理员备注'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','modify_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `modify_time` int(10) DEFAULT '0' COMMENT '退款订单管理员修改时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   `addtime` int(10) NOT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   PRIMARY KEY (`ref_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','ref_member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   KEY `ref_member_id` (`ref_member_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_refund_disable` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `ref_id` int(10) NOT NULL COMMENT '退款的id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_goods_id` int(10) NOT NULL COMMENT '子订单id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `ref_id` (`ref_id`),
  KEY `order_id` (`order_id`,`order_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='禁止申请退款的订单';

");

if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   `ref_id` int(10) NOT NULL COMMENT '退款的id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   `order_goods_id` int(10) NOT NULL COMMENT '子订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_disable','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_disable')." ADD   KEY `ref_id` (`ref_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_refund_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `ref_id` int(10) DEFAULT NULL COMMENT '退款记录id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '订单商品id',
  `message` text COMMENT '处理内容',
  `type` tinyint(1) NOT NULL COMMENT '类型：1用户反馈，2商家反馈',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `uniacid` (`uniacid`),
  KEY `ref_id` (`ref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2806 DEFAULT CHARSET=utf8 COMMENT='退款订单处理历史表';

");

if(!pdo_fieldexists('lionfish_comshop_order_refund_history','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `ref_id` int(10) DEFAULT NULL COMMENT '退款记录id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '订单商品id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','message')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `message` text COMMENT '处理内容'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `type` tinyint(1) NOT NULL COMMENT '类型：1用户反馈，2商家反馈'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   KEY `order_id` (`order_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_refund_history_image` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `orh_id` int(10) NOT NULL COMMENT '处理记录id',
  `image` varchar(255) NOT NULL COMMENT '图片地址',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `orh_id` (`orh_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款处理记录图片表';

");

if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','orh_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD   `orh_id` int(10) NOT NULL COMMENT '处理记录id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD   `image` varchar(255) NOT NULL COMMENT '图片地址'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_history_image','orh_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_history_image')." ADD   KEY `orh_id` (`orh_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_refund_image` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '退款图片自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `ref_id` int(10) NOT NULL COMMENT '退款id',
  `image` varchar(255) NOT NULL COMMENT '图片地址',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `ref_id` (`ref_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8 COMMENT='order_refund_image';

");

if(!pdo_fieldexists('lionfish_comshop_order_refund_image','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '退款图片自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_image','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_image','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD   `ref_id` int(10) NOT NULL COMMENT '退款id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_image','image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD   `image` varchar(255) NOT NULL COMMENT '图片地址'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_image','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_image','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_image','ref_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_image')." ADD   KEY `ref_id` (`ref_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_refund_reson` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `title` varchar(255) NOT NULL COMMENT '退款原因',
  `sortby` int(10) NOT NULL COMMENT '排序',
  `state` tinyint(1) NOT NULL COMMENT '状态，0未开启，1开启',
  `addtime` int(10) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城订单退款原因表';

");

if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD   `title` varchar(255) NOT NULL COMMENT '退款原因'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','sortby')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD   `sortby` int(10) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态，0未开启，1开启'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD   `addtime` int(10) NOT NULL COMMENT '时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_refund_reson','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_refund_reson')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_relate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_all_id` int(10) NOT NULL COMMENT '订单总表id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `order_all_id` (`order_all_id`,`order_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=137106 DEFAULT CHARSET=utf8 COMMENT='订单总表跟商家订单关联表';

");

if(!pdo_fieldexists('lionfish_comshop_order_relate','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_order_relate','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_relate','order_all_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD   `order_all_id` int(10) NOT NULL COMMENT '订单总表id'");}
if(!pdo_fieldexists('lionfish_comshop_order_relate','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_order_relate','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_order_relate','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_relate','order_all_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_relate')." ADD   KEY `order_all_id` (`order_all_id`,`order_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_status` (
  `order_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`order_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='订单状态';

");

if(!pdo_fieldexists('lionfish_comshop_order_status','order_status_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_status')." ADD 
  `order_status_id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_order_status','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_status')." ADD   `name` varchar(32) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_order_total` (
  `order_total_id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int(3) NOT NULL,
  PRIMARY KEY (`order_total_id`),
  KEY `order_id` (`order_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=391300 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_order_total','order_total_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD 
  `order_total_id` int(10) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_order_total','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_order_total','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `order_id` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_total','code')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `code` varchar(32) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_total','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `title` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_total','text')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `text` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_total','value')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `value` decimal(15,4) NOT NULL DEFAULT '0.0000'");}
if(!pdo_fieldexists('lionfish_comshop_order_total','sort_order')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   `sort_order` int(3) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_order_total','order_total_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   PRIMARY KEY (`order_total_id`)");}
if(!pdo_fieldexists('lionfish_comshop_order_total','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_order_total')." ADD   KEY `order_id` (`order_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_perm_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(11) NOT NULL COMMENT '管理员id',
  `uniacid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `op` text NOT NULL,
  `createtime` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=159428 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='商城系统日志表';

");

if(!pdo_fieldexists('lionfish_comshop_perm_log','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','uid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `uid` int(11) NOT NULL COMMENT '管理员id'");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `name` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `type` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','op')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `op` text NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','createtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','ip')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   `ip` varchar(255) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_perm_log','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_perm_log')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pick_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `pick_sn` varchar(50) NOT NULL COMMENT '自提sn码，用于核销',
  `pick_id` int(10) NOT NULL COMMENT '自提id',
  `pick_member_id` int(10) DEFAULT '0' COMMENT '核销人员id',
  `ziti_name` varchar(100) DEFAULT NULL COMMENT '自提人名称',
  `ziti_mobile` varchar(50) DEFAULT NULL COMMENT '自提人手机',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `state` tinyint(1) NOT NULL COMMENT '核销状态，0待核销，1已核销',
  `tihuo_time` int(10) DEFAULT '0' COMMENT '提货时间',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '核销的小程序二维码',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `pick_sn` (`pick_sn`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自提订单表';

");

if(!pdo_fieldexists('lionfish_comshop_pick_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','pick_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `pick_sn` varchar(50) NOT NULL COMMENT '自提sn码，用于核销'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','pick_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `pick_id` int(10) NOT NULL COMMENT '自提id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','pick_member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `pick_member_id` int(10) DEFAULT '0' COMMENT '核销人员id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','ziti_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `ziti_name` varchar(100) DEFAULT NULL COMMENT '自提人名称'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','ziti_mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `ziti_mobile` varchar(50) DEFAULT NULL COMMENT '自提人手机'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `state` tinyint(1) NOT NULL COMMENT '核销状态，0待核销，1已核销'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','tihuo_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `tihuo_time` int(10) DEFAULT '0' COMMENT '提货时间'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','qrcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   `qrcode` varchar(255) DEFAULT NULL COMMENT '核销的小程序二维码'");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   KEY `order_id` (`order_id`)");}
if(!pdo_fieldexists('lionfish_comshop_pick_order','pick_sn')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_order')." ADD   KEY `pick_sn` (`pick_sn`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pick_up` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `pick_name` varchar(255) NOT NULL COMMENT '自提点名称',
  `pick_pos` varchar(200) DEFAULT NULL COMMENT '自提点位置信息',
  `store_id` int(10) NOT NULL COMMENT '所属卖家',
  `passwd` varchar(200) DEFAULT NULL COMMENT '核销密码',
  `telephone` varchar(50) NOT NULL COMMENT '自提点电话',
  `lng` varchar(20) NOT NULL COMMENT '精度',
  `lat` varchar(20) NOT NULL COMMENT '纬度',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `we_qrcode` varchar(255) DEFAULT NULL COMMENT '门店核销二维码',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自提点表';

");

if(!pdo_fieldexists('lionfish_comshop_pick_up','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','pick_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `pick_name` varchar(255) NOT NULL COMMENT '自提点名称'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','pick_pos')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `pick_pos` varchar(200) DEFAULT NULL COMMENT '自提点位置信息'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `store_id` int(10) NOT NULL COMMENT '所属卖家'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','passwd')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `passwd` varchar(200) DEFAULT NULL COMMENT '核销密码'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','telephone')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `telephone` varchar(50) NOT NULL COMMENT '自提点电话'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','lng')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `lng` varchar(20) NOT NULL COMMENT '精度'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','lat')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `lat` varchar(20) NOT NULL COMMENT '纬度'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','we_qrcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   `we_qrcode` varchar(255) DEFAULT NULL COMMENT '门店核销二维码'");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_pick_up','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pick_up')." ADD   KEY `store_id` (`store_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pin` (
  `pin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '拼团id，自增',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id',
  `need_count` int(11) NOT NULL DEFAULT '0' COMMENT '拼团人数',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '拼团状态，0拼团中，1拼团成功，2拼团失败并退款',
  `is_commiss_tuan` tinyint(1) DEFAULT '0' COMMENT '是否佣金团',
  `is_newman_takein` tinyint(1) DEFAULT '0' COMMENT '是否仅新人参加',
  `lottery_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '抽奖状态，0抽奖中，1可退款，2已退款送券。',
  `is_lottery` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否抽奖拼团，0正常订单，1抽奖订单',
  `begin_time` int(11) NOT NULL COMMENT '拼团开始时间',
  `end_time` int(11) NOT NULL COMMENT '拼团结束时间',
  `add_time` int(11) NOT NULL COMMENT ' 添加时间',
  `success_time` int(10) DEFAULT NULL COMMENT '拼团成功时间',
  `is_jiqi` tinyint(1) DEFAULT '0' COMMENT '是否机器人成团',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  PRIMARY KEY (`pin_id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_pin','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD 
  `pin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '拼团id，自增'");}
if(!pdo_fieldexists('lionfish_comshop_pin','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_pin','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_pin','user_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_pin','need_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `need_count` int(11) NOT NULL DEFAULT '0' COMMENT '拼团人数'");}
if(!pdo_fieldexists('lionfish_comshop_pin','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '拼团状态，0拼团中，1拼团成功，2拼团失败并退款'");}
if(!pdo_fieldexists('lionfish_comshop_pin','is_commiss_tuan')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `is_commiss_tuan` tinyint(1) DEFAULT '0' COMMENT '是否佣金团'");}
if(!pdo_fieldexists('lionfish_comshop_pin','is_newman_takein')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `is_newman_takein` tinyint(1) DEFAULT '0' COMMENT '是否仅新人参加'");}
if(!pdo_fieldexists('lionfish_comshop_pin','lottery_state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `lottery_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '抽奖状态，0抽奖中，1可退款，2已退款送券。'");}
if(!pdo_fieldexists('lionfish_comshop_pin','is_lottery')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `is_lottery` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否抽奖拼团，0正常订单，1抽奖订单'");}
if(!pdo_fieldexists('lionfish_comshop_pin','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `begin_time` int(11) NOT NULL COMMENT '拼团开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_pin','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `end_time` int(11) NOT NULL COMMENT '拼团结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_pin','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `add_time` int(11) NOT NULL COMMENT ' 添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_pin','success_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `success_time` int(10) DEFAULT NULL COMMENT '拼团成功时间'");}
if(!pdo_fieldexists('lionfish_comshop_pin','is_jiqi')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `is_jiqi` tinyint(1) DEFAULT '0' COMMENT '是否机器人成团'");}
if(!pdo_fieldexists('lionfish_comshop_pin','qrcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码'");}
if(!pdo_fieldexists('lionfish_comshop_pin','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   PRIMARY KEY (`pin_id`)");}
if(!pdo_fieldexists('lionfish_comshop_pin','user_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   KEY `user_id` (`user_id`)");}
if(!pdo_fieldexists('lionfish_comshop_pin','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin')." ADD   KEY `order_id` (`order_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pin_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `pin_id` int(11) NOT NULL COMMENT '拼团id',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pin_id` (`pin_id`),
  KEY `order_id` (`order_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_pin_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   `pin_id` int(11) NOT NULL COMMENT '拼团id'");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   `order_id` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   `add_time` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','pin_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   KEY `pin_id` (`pin_id`)");}
if(!pdo_fieldexists('lionfish_comshop_pin_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pin_order')." ADD   KEY `order_id` (`order_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pintuan_commiss` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额',
  `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额',
  `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`) USING BTREE,
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=186 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   `member_id` int(10) NOT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','dongmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','getmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss')." ADD   KEY `member_id` (`member_id`) USING BTREE");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pintuan_commiss_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员ID',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id',
  `type` tinyint(1) DEFAULT '1' COMMENT '1按照比例。2按照固定金额',
  `bili` float(10,2) DEFAULT '0.00' COMMENT '比例，如果是固定金额，那么这里就是固定金额',
  `store_id` int(10) NOT NULL COMMENT '店铺ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消\r\n3、已打款',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额',
  `statement_time` int(10) DEFAULT '0' COMMENT '结算时间',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`) USING BTREE,
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单ID'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `type` tinyint(1) DEFAULT '1' COMMENT '1按照比例。2按照固定金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','bili')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `bili` float(10,2) DEFAULT '0.00' COMMENT '比例，如果是固定金额，那么这里就是固定金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','store_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `store_id` int(10) NOT NULL COMMENT '店铺ID'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消\r\n3、已打款'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','statement_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `statement_time` int(10) DEFAULT '0' COMMENT '结算时间'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_commiss_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_commiss_order')." ADD   KEY `member_id` (`member_id`) USING BTREE");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_pintuan_tixian_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL,
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `money` float(10,2) NOT NULL COMMENT '提现金额',
  `service_charge` float(10,2) DEFAULT '0.00' COMMENT '提现手续费比例',
  `service_charge_money` float(10,2) DEFAULT '0.00' COMMENT '提现后实际到账金额',
  `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败',
  `shentime` int(10) NOT NULL COMMENT '审核时间',
  `type` tinyint(1) DEFAULT '0' COMMENT '1余额，2微信 3支付宝 4 银行卡',
  `bankname` varchar(255) DEFAULT NULL COMMENT '银行卡名称',
  `bankaccount` varchar(255) DEFAULT NULL COMMENT '卡号',
  `bankusername` varchar(255) DEFAULT NULL COMMENT '持卡人姓名',
  `addtime` int(10) NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销佣金提现表';

");

if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `uniacid` int(10) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `money` float(10,2) NOT NULL COMMENT '提现金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','service_charge')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `service_charge` float(10,2) DEFAULT '0.00' COMMENT '提现手续费比例'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','service_charge_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `service_charge_money` float(10,2) DEFAULT '0.00' COMMENT '提现后实际到账金额'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','shentime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `shentime` int(10) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '1余额，2微信 3支付宝 4 银行卡'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','bankname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `bankname` varchar(255) DEFAULT NULL COMMENT '银行卡名称'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','bankaccount')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `bankaccount` varchar(255) DEFAULT NULL COMMENT '卡号'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','bankusername')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `bankusername` varchar(255) DEFAULT NULL COMMENT '持卡人姓名'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   `addtime` int(10) NOT NULL COMMENT '提交时间'");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_pintuan_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_pintuan_tixian_order')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_recipe` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `recipe_name` varchar(255) NOT NULL COMMENT '菜谱名称',
  `sub_name` varchar(255) DEFAULT NULL COMMENT '菜谱描述',
  `images` varchar(255) DEFAULT NULL COMMENT '菜谱图片',
  `video` varchar(255) DEFAULT NULL COMMENT '菜谱视频',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `cate_id` int(10) NOT NULL COMMENT '分类id',
  `make_time` varchar(100) DEFAULT NULL COMMENT '制作时间',
  `diff_type` tinyint(1) NOT NULL COMMENT '难易程度，1简单，2容易，3困难',
  `state` tinyint(1) DEFAULT NULL COMMENT '0禁用，1启用',
  `fav_count` int(10) DEFAULT '0' COMMENT '喜欢数',
  `content` text NOT NULL COMMENT '菜谱内容',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='商城菜谱表';

");

if(!pdo_fieldexists('lionfish_comshop_recipe','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','recipe_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `recipe_name` varchar(255) NOT NULL COMMENT '菜谱名称'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','sub_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `sub_name` varchar(255) DEFAULT NULL COMMENT '菜谱描述'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','images')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `images` varchar(255) DEFAULT NULL COMMENT '菜谱图片'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','video')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `video` varchar(255) DEFAULT NULL COMMENT '菜谱视频'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','cate_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `cate_id` int(10) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','make_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `make_time` varchar(100) DEFAULT NULL COMMENT '制作时间'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','diff_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `diff_type` tinyint(1) NOT NULL COMMENT '难易程度，1简单，2容易，3困难'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `state` tinyint(1) DEFAULT NULL COMMENT '0禁用，1启用'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','fav_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `fav_count` int(10) DEFAULT '0' COMMENT '喜欢数'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `content` text NOT NULL COMMENT '菜谱内容'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_recipe','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_recipe','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_recipe_fav` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `recipe_id` int(10) NOT NULL COMMENT '菜谱id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `recipe_id` (`recipe_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='商城菜谱喜欢表';

");

if(!pdo_fieldexists('lionfish_comshop_recipe_fav','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','recipe_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   `recipe_id` int(10) NOT NULL COMMENT '菜谱id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_recipe_fav','recipe_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_fav')." ADD   KEY `recipe_id` (`recipe_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_recipe_ingredients` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `recipe_id` int(10) NOT NULL COMMENT '菜谱id',
  `goods_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '商品id,字符串多个逗号隔开 .',
  `title` varchar(255) NOT NULL COMMENT '食材标题',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `recipe_id` (`recipe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='商城食材表';

");

if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','recipe_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   `recipe_id` int(10) NOT NULL COMMENT '菜谱id'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   `goods_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '商品id,字符串多个逗号隔开 .'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   `title` varchar(255) NOT NULL COMMENT '食材标题'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_recipe_ingredients','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_recipe_ingredients')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_refund_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '退货地址id',
  `uniacid` int(11) DEFAULT '0' COMMENT '小程序id',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `name` varchar(20) DEFAULT NULL COMMENT '联系人',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `city` varchar(30) DEFAULT NULL COMMENT '市',
  `area` varchar(30) DEFAULT NULL COMMENT '区',
  `address` varchar(300) DEFAULT NULL COMMENT '详细地址',
  `isdefault` tinyint(1) DEFAULT '0' COMMENT '是否默认',
  `zipcode` varchar(20) DEFAULT NULL COMMENT '邮编',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家退货地址';

");

if(!pdo_fieldexists('lionfish_comshop_refund_address','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '退货地址id'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '小程序id'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `title` varchar(20) DEFAULT NULL COMMENT '标题'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `name` varchar(20) DEFAULT NULL COMMENT '联系人'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','tel')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `tel` varchar(20) DEFAULT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `mobile` varchar(11) DEFAULT NULL COMMENT '手机'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','province')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `province` varchar(30) DEFAULT NULL COMMENT '省'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','city')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `city` varchar(30) DEFAULT NULL COMMENT '市'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','area')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `area` varchar(30) DEFAULT NULL COMMENT '区'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `address` varchar(300) DEFAULT NULL COMMENT '详细地址'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','isdefault')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `isdefault` tinyint(1) DEFAULT '0' COMMENT '是否默认'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','zipcode')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   `zipcode` varchar(20) DEFAULT NULL COMMENT '邮编'");}
if(!pdo_fieldexists('lionfish_comshop_refund_address','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_refund_address')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_salesroom` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `supply_id` int(10) DEFAULT NULL COMMENT '商户id',
  `room_name` varchar(255) NOT NULL COMMENT '门店名称',
  `room_logo` varchar(255) NOT NULL COMMENT '门店logo',
  `province_id` int(10) NOT NULL COMMENT '省',
  `city_id` int(10) NOT NULL COMMENT '市',
  `country_id` int(10) NOT NULL COMMENT '区',
  `area_id` int(10) NOT NULL COMMENT '镇',
  `address` varchar(255) NOT NULL COMMENT '详细地址',
  `lon` float NOT NULL COMMENT '经度',
  `lat` float NOT NULL COMMENT '纬度',
  `state` tinyint(1) NOT NULL COMMENT '状态，0禁用，1启用',
  `mobile` varchar(25) NOT NULL COMMENT '手机',
  `business_hours` varchar(100) NOT NULL COMMENT '营业时间',
  `contacts` varchar(50) NOT NULL COMMENT '联系人',
  `store_introduction` text NOT NULL COMMENT '门店介绍',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `supply_id` (`supply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='核销门店';

");

if(!pdo_fieldexists('lionfish_comshop_salesroom','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `supply_id` int(10) DEFAULT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','room_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `room_name` varchar(255) NOT NULL COMMENT '门店名称'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','room_logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `room_logo` varchar(255) NOT NULL COMMENT '门店logo'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','province_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `province_id` int(10) NOT NULL COMMENT '省'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','city_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `city_id` int(10) NOT NULL COMMENT '市'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','country_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `country_id` int(10) NOT NULL COMMENT '区'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','area_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `area_id` int(10) NOT NULL COMMENT '镇'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','address')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `address` varchar(255) NOT NULL COMMENT '详细地址'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','lon')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `lon` float NOT NULL COMMENT '经度'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','lat')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `lat` float NOT NULL COMMENT '纬度'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态，0禁用，1启用'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `mobile` varchar(25) NOT NULL COMMENT '手机'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','business_hours')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `business_hours` varchar(100) NOT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','contacts')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `contacts` varchar(50) NOT NULL COMMENT '联系人'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','store_introduction')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `store_introduction` text NOT NULL COMMENT '门店介绍'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_salesroom','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_salesroom_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `supply_id` int(10) DEFAULT '0' COMMENT '商户id',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `mobile` varchar(25) NOT NULL COMMENT '手机号',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `state` tinyint(1) NOT NULL COMMENT '状态，0禁用，1启用',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门店核销人员表';

");

if(!pdo_fieldexists('lionfish_comshop_salesroom_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `supply_id` int(10) DEFAULT '0' COMMENT '商户id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','username')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `username` varchar(255) NOT NULL COMMENT '用户名'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `mobile` varchar(25) NOT NULL COMMENT '手机号'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态，0禁用，1启用'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_member')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_salesroom_relative_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `salesroom_id` int(10) NOT NULL COMMENT '门店id',
  `smember_id` int(10) DEFAULT '0' COMMENT '核销会员id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `salesroom_id` (`salesroom_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门店核销人员跟门店关联表';

");

if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','salesroom_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   `salesroom_id` int(10) NOT NULL COMMENT '门店id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','smember_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   `smember_id` int(10) DEFAULT '0' COMMENT '核销会员id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_salesroom_relative_member','salesroom_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_salesroom_relative_member')." ADD   KEY `salesroom_id` (`salesroom_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_shipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `type` tinyint(1) NOT NULL COMMENT '1 按重量：，2 按件：，3：按体积',
  `sort_order` int(10) NOT NULL COMMENT '排序',
  `firstprice` decimal(10,2) NOT NULL COMMENT '首重价格',
  `secondprice` decimal(10,2) NOT NULL COMMENT '续重价格',
  `firstweight` int(11) NOT NULL COMMENT '首重 单位g',
  `secondweight` int(11) NOT NULL COMMENT '续重 单位g',
  `areas` longtext NOT NULL COMMENT '配送地区',
  `firstnum` int(11) NOT NULL COMMENT '首件',
  `secondnum` int(11) NOT NULL COMMENT '续件',
  `firstnumprice` decimal(10,2) NOT NULL COMMENT '首件价格',
  `secondnumprice` decimal(10,2) NOT NULL COMMENT '续件价格',
  `isdefault` tinyint(1) NOT NULL COMMENT '是否默认',
  `enabled` tinyint(1) DEFAULT '1' COMMENT '启用1:0关闭',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COMMENT='商城运费模板表';

");

if(!pdo_fieldexists('lionfish_comshop_shipping','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `name` varchar(100) NOT NULL COMMENT '模板名称'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `type` tinyint(1) NOT NULL COMMENT '1 按重量：，2 按件：，3：按体积'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','sort_order')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `sort_order` int(10) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','firstprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `firstprice` decimal(10,2) NOT NULL COMMENT '首重价格'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','secondprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `secondprice` decimal(10,2) NOT NULL COMMENT '续重价格'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','firstweight')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `firstweight` int(11) NOT NULL COMMENT '首重 单位g'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','secondweight')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `secondweight` int(11) NOT NULL COMMENT '续重 单位g'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','areas')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `areas` longtext NOT NULL COMMENT '配送地区'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','firstnum')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `firstnum` int(11) NOT NULL COMMENT '首件'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','secondnum')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `secondnum` int(11) NOT NULL COMMENT '续件'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','firstnumprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `firstnumprice` decimal(10,2) NOT NULL COMMENT '首件价格'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','secondnumprice')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `secondnumprice` decimal(10,2) NOT NULL COMMENT '续件价格'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','isdefault')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `isdefault` tinyint(1) NOT NULL COMMENT '是否默认'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   `enabled` tinyint(1) DEFAULT '1' COMMENT '启用1:0关闭'");}
if(!pdo_fieldexists('lionfish_comshop_shipping','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_shipping')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_signinreward_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `continuity_day` int(10) NOT NULL COMMENT '连续签到几天',
  `reward_socre` int(10) NOT NULL COMMENT '赠送积分数量',
  `signin_time` int(10) NOT NULL COMMENT '签到时间',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3441 DEFAULT CHARSET=utf8 COMMENT='商城签到奖励记录表';

");

if(!pdo_fieldexists('lionfish_comshop_signinreward_record','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','continuity_day')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   `continuity_day` int(10) NOT NULL COMMENT '连续签到几天'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','reward_socre')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   `reward_socre` int(10) NOT NULL COMMENT '赠送积分数量'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','signin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   `signin_time` int(10) NOT NULL COMMENT '签到时间'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_signinreward_record','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_signinreward_record')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_site_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '',
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_comshop_site_config','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_site_config')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_site_config','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_site_config')." ADD   `type` varchar(32) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('lionfish_comshop_site_config','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_site_config')." ADD   `content` text");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_solitaire` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `head_id` int(10) NOT NULL COMMENT '团长id',
  `solitaire_name` varchar(255) NOT NULL COMMENT '接龙名称',
  `images_list` text COMMENT '图片列表',
  `addtype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0平台添加，1团长添加',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未审核，1已审核',
  `appstate` tinyint(1) DEFAULT '1' COMMENT '是否审核，1审核，0未审核',
  `begin_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `content` text COMMENT '内容',
  `end` tinyint(1) DEFAULT '0' COMMENT '手动结束',
  `qrcode_image` varchar(255) DEFAULT NULL COMMENT '接龙二维码',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `head_id` (`head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='商城群接龙活动表';

");

if(!pdo_fieldexists('lionfish_comshop_solitaire','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','head_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `head_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','solitaire_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `solitaire_name` varchar(255) NOT NULL COMMENT '接龙名称'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','images_list')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `images_list` text COMMENT '图片列表'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','addtype')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `addtype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0平台添加，1团长添加'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未审核，1已审核'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','appstate')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `appstate` tinyint(1) DEFAULT '1' COMMENT '是否审核，1审核，0未审核'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `begin_time` int(10) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `end_time` int(10) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `content` text COMMENT '内容'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','end')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `end` tinyint(1) DEFAULT '0' COMMENT '手动结束'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','qrcode_image')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   `qrcode_image` varchar(255) DEFAULT NULL COMMENT '接龙二维码'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_solitaire','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_solitaire_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `soli_id` int(10) NOT NULL COMMENT '群接龙id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`soli_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8 COMMENT='商城接龙活动商品表';

");

if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','soli_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD   `soli_id` int(10) NOT NULL COMMENT '群接龙id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_goods')." ADD   KEY `uniacid` (`uniacid`,`soli_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_solitaire_invite` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `soli_id` int(10) NOT NULL COMMENT '群接龙id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `soli_id` (`soli_id`,`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8 COMMENT='商城群接龙访问记录表';

");

if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','soli_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD   `soli_id` int(10) NOT NULL COMMENT '群接龙id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_invite','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_invite')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_solitaire_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `soli_id` int(10) NOT NULL COMMENT '群接龙id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`soli_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='商城群接龙订单表';

");

if(!pdo_fieldexists('lionfish_comshop_solitaire_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_order')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_order','soli_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_order')." ADD   `soli_id` int(10) NOT NULL COMMENT '群接龙id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_order')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_order')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_solitaire_post` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `soli_id` int(10) NOT NULL COMMENT '群接龙id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '回复id，默认0',
  `fav_count` int(10) NOT NULL COMMENT '点赞数',
  `content` text NOT NULL COMMENT '评论内容',
  `addtime` int(10) NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`,`soli_id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='商城群接龙评论表';

");

if(!pdo_fieldexists('lionfish_comshop_solitaire_post','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','soli_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `soli_id` int(10) NOT NULL COMMENT '群接龙id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','pid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `pid` int(10) NOT NULL DEFAULT '0' COMMENT '回复id，默认0'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','fav_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `fav_count` int(10) NOT NULL COMMENT '点赞数'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','content')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `content` text NOT NULL COMMENT '评论内容'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   `addtime` int(10) NOT NULL COMMENT '评论时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post')." ADD   KEY `uniacid` (`uniacid`,`soli_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_solitaire_post_fav` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `soli_id` int(10) NOT NULL COMMENT '群接龙id',
  `post_id` int(10) NOT NULL COMMENT '评论id',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城群接龙点赞表';

");

if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','soli_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD   `soli_id` int(10) NOT NULL COMMENT '群接龙id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','post_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD   `post_id` int(10) NOT NULL COMMENT '评论id'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_solitaire_post_fav','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_solitaire_post_fav')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `name` varchar(255) NOT NULL COMMENT '规格名称',
  `value` text NOT NULL COMMENT '规格值名称',
  `spec_type` enum('normal','pintuan') DEFAULT 'normal' COMMENT '商品规格类型，普通，拼团',
  `addtime` int(11) NOT NULL COMMENT '10',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COMMENT='商城规格表';

");

if(!pdo_fieldexists('lionfish_comshop_spec','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_spec','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_spec','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD   `name` varchar(255) NOT NULL COMMENT '规格名称'");}
if(!pdo_fieldexists('lionfish_comshop_spec','value')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD   `value` text NOT NULL COMMENT '规格值名称'");}
if(!pdo_fieldexists('lionfish_comshop_spec','spec_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD   `spec_type` enum('normal','pintuan') DEFAULT 'normal' COMMENT '商品规格类型，普通，拼团'");}
if(!pdo_fieldexists('lionfish_comshop_spec','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD   `addtime` int(11) NOT NULL COMMENT '10'");}
if(!pdo_fieldexists('lionfish_comshop_spec','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spec')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_special` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '专题id',
  `uniacid` int(11) DEFAULT '0' COMMENT '小程序id',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面',
  `goodsids` text COMMENT '商品id',
  `enabled` tinyint(1) DEFAULT '0' COMMENT '状态',
  `type` tinyint(1) DEFAULT '0' COMMENT '0单图 1图+商品',
  `special_title` varchar(255) DEFAULT NULL COMMENT '活动页面主题标题',
  `special_cover` varchar(255) DEFAULT NULL COMMENT '活动页面主题图片',
  `displayorder` int(10) DEFAULT '0' COMMENT '排序',
  `begin_time` int(10) NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL COMMENT '活动结束时间',
  `is_index` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页显示',
  `show_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0小图，1大图',
  `bg_color` varchar(20) NOT NULL DEFAULT '#F6F6F6' COMMENT '背景颜色',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='后台专题管理';

");

if(!pdo_fieldexists('lionfish_comshop_special','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '专题id'");}
if(!pdo_fieldexists('lionfish_comshop_special','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `uniacid` int(11) DEFAULT '0' COMMENT '小程序id'");}
if(!pdo_fieldexists('lionfish_comshop_special','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `name` varchar(255) DEFAULT NULL COMMENT '名称'");}
if(!pdo_fieldexists('lionfish_comshop_special','cover')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `cover` varchar(255) DEFAULT NULL COMMENT '封面'");}
if(!pdo_fieldexists('lionfish_comshop_special','goodsids')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `goodsids` text COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_special','enabled')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `enabled` tinyint(1) DEFAULT '0' COMMENT '状态'");}
if(!pdo_fieldexists('lionfish_comshop_special','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '0单图 1图+商品'");}
if(!pdo_fieldexists('lionfish_comshop_special','special_title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `special_title` varchar(255) DEFAULT NULL COMMENT '活动页面主题标题'");}
if(!pdo_fieldexists('lionfish_comshop_special','special_cover')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `special_cover` varchar(255) DEFAULT NULL COMMENT '活动页面主题图片'");}
if(!pdo_fieldexists('lionfish_comshop_special','displayorder')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `displayorder` int(10) DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('lionfish_comshop_special','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `begin_time` int(10) NOT NULL COMMENT '活动开始时间'");}
if(!pdo_fieldexists('lionfish_comshop_special','end_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `end_time` int(10) NOT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_special','is_index')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `is_index` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页显示'");}
if(!pdo_fieldexists('lionfish_comshop_special','show_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `show_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0小图，1大图'");}
if(!pdo_fieldexists('lionfish_comshop_special','bg_color')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `bg_color` varchar(20) NOT NULL DEFAULT '#F6F6F6' COMMENT '背景颜色'");}
if(!pdo_fieldexists('lionfish_comshop_special','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_special','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_special')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_spike` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `title` varchar(255) NOT NULL COMMENT '活动标题',
  `state` tinytext NOT NULL COMMENT '活动状态，0下架，1上架',
  `slot_ids` varchar(255) DEFAULT NULL COMMENT '时间段id',
  `begin_time` int(10) NOT NULL COMMENT '开始时间，',
  `end_tme` int(10) NOT NULL COMMENT '结束时间',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `begin_time` (`begin_time`,`end_tme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='秒杀活动表';

");

if(!pdo_fieldexists('lionfish_comshop_spike','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_spike','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_spike','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `title` varchar(255) NOT NULL COMMENT '活动标题'");}
if(!pdo_fieldexists('lionfish_comshop_spike','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `state` tinytext NOT NULL COMMENT '活动状态，0下架，1上架'");}
if(!pdo_fieldexists('lionfish_comshop_spike','slot_ids')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `slot_ids` varchar(255) DEFAULT NULL COMMENT '时间段id'");}
if(!pdo_fieldexists('lionfish_comshop_spike','begin_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `begin_time` int(10) NOT NULL COMMENT '开始时间，'");}
if(!pdo_fieldexists('lionfish_comshop_spike','end_tme')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `end_tme` int(10) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('lionfish_comshop_spike','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_spike','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_spike','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_spike_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `spike_id` int(10) NOT NULL COMMENT '秒杀活动id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `activity_begintime` int(10) NOT NULL COMMENT '活动开始小时',
  `activity_endtime` int(10) NOT NULL COMMENT '活动结束小时',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `goods_id` (`goods_id`),
  KEY `spike_id` (`spike_id`),
  KEY `activity_begintime` (`activity_begintime`,`activity_endtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城秒杀商品表';

");

if(!pdo_fieldexists('lionfish_comshop_spike_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','spike_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   `spike_id` int(10) NOT NULL COMMENT '秒杀活动id'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','activity_begintime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   `activity_begintime` int(10) NOT NULL COMMENT '活动开始小时'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','activity_endtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   `activity_endtime` int(10) NOT NULL COMMENT '活动结束小时'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   KEY `goods_id` (`goods_id`)");}
if(!pdo_fieldexists('lionfish_comshop_spike_goods','spike_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_goods')." ADD   KEY `spike_id` (`spike_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_spike_time_slot` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `name` varchar(255) NOT NULL COMMENT '时间段名称',
  `hour` int(10) NOT NULL COMMENT '小时',
  `minute` int(10) NOT NULL COMMENT '分钟',
  `second` int(10) NOT NULL COMMENT '秒数',
  `endhour` int(10) NOT NULL DEFAULT '0' COMMENT '结束时',
  `endminute` int(10) NOT NULL DEFAULT '0' COMMENT '结束分',
  `endsecond` int(10) NOT NULL DEFAULT '0' COMMENT '结束秒',
  `state` tinyint(1) NOT NULL COMMENT '状态，0开启，1关闭',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城秒杀时间段';

");

if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `name` varchar(255) NOT NULL COMMENT '时间段名称'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','hour')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `hour` int(10) NOT NULL COMMENT '小时'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','minute')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `minute` int(10) NOT NULL COMMENT '分钟'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','second')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `second` int(10) NOT NULL COMMENT '秒数'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','endhour')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `endhour` int(10) NOT NULL DEFAULT '0' COMMENT '结束时'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','endminute')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `endminute` int(10) NOT NULL DEFAULT '0' COMMENT '结束分'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','endsecond')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `endsecond` int(10) NOT NULL DEFAULT '0' COMMENT '结束秒'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `state` tinyint(1) NOT NULL COMMENT '状态，0开启，1关闭'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_spike_time_slot','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_spike_time_slot')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_subscribe` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `template_id` varchar(255) NOT NULL COMMENT '模板',
  `type` enum('pay_order','send_order','hexiao_success','apply_community','open_tuan','take_tuan','pin_tuansuccess','apply_tixian') NOT NULL COMMENT '消息类型',
  `addtime` int(10) NOT NULL COMMENT '订阅时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`),
  KEY `member_id_2` (`member_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='商城用户订阅消息';

");

if(!pdo_fieldexists('lionfish_comshop_subscribe','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','template_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   `template_id` varchar(255) NOT NULL COMMENT '模板'");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   `type` enum('pay_order','send_order','hexiao_success','apply_community','open_tuan','take_tuan','pin_tuansuccess','apply_tixian') NOT NULL COMMENT '消息类型'");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   `addtime` int(10) NOT NULL COMMENT '订阅时间'");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('lionfish_comshop_subscribe','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_subscribe')." ADD   KEY `member_id` (`member_id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_supply` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `shopname` varchar(255) NOT NULL COMMENT '店铺名称',
  `name` varchar(100) NOT NULL COMMENT '供应商名称',
  `member_id` int(10) DEFAULT '0' COMMENT '关联会员id',
  `logo` varchar(255) DEFAULT NULL COMMENT 'logo',
  `storename` varchar(255) NOT NULL COMMENT '店铺名称',
  `banner` varchar(255) DEFAULT NULL COMMENT '店铺顶部图',
  `product` varchar(1000) DEFAULT NULL COMMENT '产品说明',
  `mobile` varchar(50) NOT NULL COMMENT '供应商手机号',
  `state` tinyint(1) NOT NULL COMMENT '供应商状态，0未审核，1已审核',
  `type` tinyint(1) DEFAULT '0' COMMENT '0 平台自营，1入驻',
  `commiss_bili` int(10) DEFAULT '0' COMMENT '供应商提成。百分比',
  `apptime` int(10) DEFAULT NULL COMMENT '申请时间',
  `login_name` varchar(50) DEFAULT NULL COMMENT '供应商登录名称',
  `login_password` varchar(100) DEFAULT NULL COMMENT '供应商登录密码',
  `login_slat` varchar(50) DEFAULT NULL COMMENT '密码盐值',
  `qualifications` text COMMENT '工商资质',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=utf8mb4 COMMENT='社区团购供应商表';

");

if(!pdo_fieldexists('lionfish_comshop_supply','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_supply','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_supply','shopname')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `shopname` varchar(255) NOT NULL COMMENT '店铺名称'");}
if(!pdo_fieldexists('lionfish_comshop_supply','name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `name` varchar(100) NOT NULL COMMENT '供应商名称'");}
if(!pdo_fieldexists('lionfish_comshop_supply','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `member_id` int(10) DEFAULT '0' COMMENT '关联会员id'");}
if(!pdo_fieldexists('lionfish_comshop_supply','logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `logo` varchar(255) DEFAULT NULL COMMENT 'logo'");}
if(!pdo_fieldexists('lionfish_comshop_supply','storename')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `storename` varchar(255) NOT NULL COMMENT '店铺名称'");}
if(!pdo_fieldexists('lionfish_comshop_supply','banner')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `banner` varchar(255) DEFAULT NULL COMMENT '店铺顶部图'");}
if(!pdo_fieldexists('lionfish_comshop_supply','product')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `product` varchar(1000) DEFAULT NULL COMMENT '产品说明'");}
if(!pdo_fieldexists('lionfish_comshop_supply','mobile')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `mobile` varchar(50) NOT NULL COMMENT '供应商手机号'");}
if(!pdo_fieldexists('lionfish_comshop_supply','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `state` tinyint(1) NOT NULL COMMENT '供应商状态，0未审核，1已审核'");}
if(!pdo_fieldexists('lionfish_comshop_supply','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `type` tinyint(1) DEFAULT '0' COMMENT '0 平台自营，1入驻'");}
if(!pdo_fieldexists('lionfish_comshop_supply','commiss_bili')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `commiss_bili` int(10) DEFAULT '0' COMMENT '供应商提成。百分比'");}
if(!pdo_fieldexists('lionfish_comshop_supply','apptime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `apptime` int(10) DEFAULT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('lionfish_comshop_supply','login_name')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `login_name` varchar(50) DEFAULT NULL COMMENT '供应商登录名称'");}
if(!pdo_fieldexists('lionfish_comshop_supply','login_password')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `login_password` varchar(100) DEFAULT NULL COMMENT '供应商登录密码'");}
if(!pdo_fieldexists('lionfish_comshop_supply','login_slat')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `login_slat` varchar(50) DEFAULT NULL COMMENT '密码盐值'");}
if(!pdo_fieldexists('lionfish_comshop_supply','qualifications')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `qualifications` text COMMENT '工商资质'");}
if(!pdo_fieldexists('lionfish_comshop_supply','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_supply','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_supply')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_sysset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `sets` longtext,
  `plugins` longtext,
  `sec` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城设置表';

");

if(!pdo_fieldexists('lionfish_comshop_sysset','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_sysset')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_sysset','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_sysset')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_sysset','sets')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_sysset')." ADD   `sets` longtext");}
if(!pdo_fieldexists('lionfish_comshop_sysset','plugins')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_sysset')." ADD   `plugins` longtext");}
if(!pdo_fieldexists('lionfish_comshop_sysset','sec')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_sysset')." ADD   `sec` text");}
if(!pdo_fieldexists('lionfish_comshop_sysset','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_sysset')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_system_copyright` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `copyright` text,
  `bgcolor` varchar(255) DEFAULT NULL,
  `ismanage` tinyint(3) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

");

if(!pdo_fieldexists('lionfish_comshop_system_copyright','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','copyright')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   `copyright` text");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','bgcolor')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   `bgcolor` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','ismanage')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   `ismanage` tinyint(3) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','logo')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   `logo` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','title')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   `title` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('lionfish_comshop_system_copyright','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_system_copyright')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_templatemsg` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `template_data` text NOT NULL COMMENT '发送数据',
  `url` varchar(255) DEFAULT NULL COMMENT '点击url',
  `open_id` varchar(255) NOT NULL COMMENT 'openid',
  `template_id` varchar(255) NOT NULL COMMENT '模板id',
  `type` tinyint(1) NOT NULL COMMENT '0 非群发，1 发送给所有人',
  `state` tinyint(1) NOT NULL COMMENT '0未发送状态，1已发送',
  `total_count` int(10) DEFAULT NULL COMMENT '一共多少条',
  `send_total_count` int(10) DEFAULT '0' COMMENT '已经发送了多少条',
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城群发模板消息';

");

if(!pdo_fieldexists('lionfish_comshop_templatemsg','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','template_data')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `template_data` text NOT NULL COMMENT '发送数据'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','url')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `url` varchar(255) DEFAULT NULL COMMENT '点击url'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','open_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `open_id` varchar(255) NOT NULL COMMENT 'openid'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','template_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `template_id` varchar(255) NOT NULL COMMENT '模板id'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','type')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `type` tinyint(1) NOT NULL COMMENT '0 非群发，1 发送给所有人'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `state` tinyint(1) NOT NULL COMMENT '0未发送状态，1已发送'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','total_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `total_count` int(10) DEFAULT NULL COMMENT '一共多少条'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','send_total_count')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `send_total_count` int(10) DEFAULT '0' COMMENT '已经发送了多少条'");}
if(!pdo_fieldexists('lionfish_comshop_templatemsg','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_templatemsg')." ADD   `addtime` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_user_favgoods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城商品收藏表';

");

if(!pdo_fieldexists('lionfish_comshop_user_favgoods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_user_favgoods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_user_favgoods','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_user_favgoods','goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD   `goods_id` int(10) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('lionfish_comshop_user_favgoods','add_time')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD   `add_time` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_comshop_user_favgoods','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_user_favgoods','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_user_favgoods')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_comshop_weprogram_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `token` varchar(255) NOT NULL COMMENT '会员标识',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `session_key` varchar(100) NOT NULL COMMENT '保持登录的session',
  `expires_in` int(10) NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=120266 DEFAULT CHARSET=utf8mb4 COMMENT='商城小程序会员token表';

");

if(!pdo_fieldexists('lionfish_comshop_weprogram_token','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','token')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   `token` varchar(255) NOT NULL COMMENT '会员标识'");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','member_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   `member_id` int(10) NOT NULL COMMENT '会员id'");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','session_key')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   `session_key` varchar(100) NOT NULL COMMENT '保持登录的session'");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','expires_in')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   `expires_in` int(10) NOT NULL COMMENT '过期时间'");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_comshop_weprogram_token','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_comshop_weprogram_token')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_supply_commiss` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(10) NOT NULL COMMENT '公众号id',
  `supply_id` int(10) NOT NULL COMMENT '供应商ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额',
  `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额',
  `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`,`supply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_supply_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_supply_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   `uniacid` int(10) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_supply_commiss','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   `supply_id` int(10) NOT NULL COMMENT '供应商ID'");}
if(!pdo_fieldexists('lionfish_supply_commiss','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   `money` float(10,2) DEFAULT '0.00' COMMENT '可提现金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss','dongmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   `dongmoney` float(10,2) DEFAULT '0.00' COMMENT '提现中金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss','getmoney')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   `getmoney` float(10,2) DEFAULT '0.00' COMMENT '已提现金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_supply_commiss','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_supply_commiss_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `supply_id` int(10) NOT NULL COMMENT '供应商id',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id',
  `total_money` float(10,2) DEFAULT '0.00' COMMENT '总金额',
  `comunity_blili` float(10,2) DEFAULT '0.00' COMMENT '服务费比例',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消',
  `head_commiss_money` float(10,2) DEFAULT '0.00' COMMENT '团长分佣金额',
  `member_commiss_money` float(10,2) DEFAULT '0.00' COMMENT '会员分销金额',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`,`supply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=163712 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");

if(!pdo_fieldexists('lionfish_supply_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `supply_id` int(10) NOT NULL COMMENT '供应商id'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','order_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `order_id` int(10) NOT NULL COMMENT '订单ID'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','order_goods_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `order_goods_id` int(10) DEFAULT '0' COMMENT '商品订单id'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','total_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `total_money` float(10,2) DEFAULT '0.00' COMMENT '总金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','comunity_blili')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `comunity_blili` float(10,2) DEFAULT '0.00' COMMENT '服务费比例'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结算：0，待计算。1，已结算。2，订单取消'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','head_commiss_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `head_commiss_money` float(10,2) DEFAULT '0.00' COMMENT '团长分佣金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','member_commiss_money')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `member_commiss_money` float(10,2) DEFAULT '0.00' COMMENT '会员分销金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '分佣金额'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   `addtime` int(10) NOT NULL COMMENT '添加时间'");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_supply_commiss_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_commiss_order')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_lionfish_supply_tixian_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uniacid` int(10) NOT NULL,
  `supply_id` int(10) NOT NULL COMMENT '团长id',
  `money` float(10,2) NOT NULL COMMENT '提现金额',
  `supply_apply_type` tinyint(1) DEFAULT '0' COMMENT '供应商提现类型：0原来默认，1 微信 2 支付宝 3银行卡',
  `service_charge` float(10,2) DEFAULT '0.00' COMMENT '手续费',
  `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败',
  `shentime` int(10) NOT NULL COMMENT '审核时间',
  `is_send_fail` tinyint(1) DEFAULT '0' COMMENT '是否发送是否，1是，0否',
  `fail_msg` varchar(255) DEFAULT NULL COMMENT '发送失败原因',
  `bankname` varchar(200) DEFAULT NULL COMMENT '转账类型。微信，支付宝，银行',
  `bankaccount` varchar(200) DEFAULT NULL COMMENT '账户',
  `bankusername` varchar(200) DEFAULT NULL COMMENT '持卡人姓名',
  `addtime` int(10) NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uniacid_2` (`uniacid`,`supply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='分销佣金提现表';

");

if(!pdo_fieldexists('lionfish_supply_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `uniacid` int(10) NOT NULL");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','supply_id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `supply_id` int(10) NOT NULL COMMENT '团长id'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','money')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `money` float(10,2) NOT NULL COMMENT '提现金额'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','supply_apply_type')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `supply_apply_type` tinyint(1) DEFAULT '0' COMMENT '供应商提现类型：0原来默认，1 微信 2 支付宝 3银行卡'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','service_charge')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `service_charge` float(10,2) DEFAULT '0.00' COMMENT '手续费'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','state')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `state` tinyint(1) NOT NULL COMMENT '提现状态，0申请中，1提现成功，2体现失败'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','shentime')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `shentime` int(10) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','is_send_fail')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `is_send_fail` tinyint(1) DEFAULT '0' COMMENT '是否发送是否，1是，0否'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','fail_msg')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `fail_msg` varchar(255) DEFAULT NULL COMMENT '发送失败原因'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','bankname')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `bankname` varchar(200) DEFAULT NULL COMMENT '转账类型。微信，支付宝，银行'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','bankaccount')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `bankaccount` varchar(200) DEFAULT NULL COMMENT '账户'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','bankusername')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `bankusername` varchar(200) DEFAULT NULL COMMENT '持卡人姓名'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','addtime')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   `addtime` int(10) NOT NULL COMMENT '提交时间'");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','id')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('lionfish_supply_tixian_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('lionfish_supply_tixian_order')." ADD   KEY `uniacid` (`uniacid`)");}
