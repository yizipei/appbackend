<?php

//$GLOBALS['db']['server']   = "127.0.0.1";
//$GLOBALS['db']['password'] = "";

$GLOBALS['db']['server']   = "127.0.0.1";
$GLOBALS['db']['password'] = "root" ;

$GLOBALS['db']['username'] = "root";
$GLOBALS['db']['database'] = "furniture";
$GLOBALS['db']['charset']  = "utf8";

// 上传文件存放地址
$GLOBALS['upload_Path'] = "/home/www/gjj/backend/web/" ;
// 返回给客户端 的图片URL地址头
$GLOBALS['URL_IMG_HEAD'] = "http://www.jajahome.com/gjj/backend/web/" ;


// VR xml 文件存放路径
$GLOBALS['VR_XML_PATH'] = "/home/www/krpano-1.19-pr8/viewer/examples/gjj/" ;
// VR xml 文件返回地址
$GLOBALS['VR_XML_URL'] = "http://www.jajahome.com/krpano-1.19-pr8/viewer/krpano.html?xml=examples/gjj/" ;


// 需要有分页的返回每页最多多少记录
$GLOBALS['PAGE_MAX_RECORD'] = 10 ;
// 内存临时表数据过期时间 -- 秒
$GLOBALS['MEM_TABLE_OUTTIME'] = 120 ;

$GLOBALS['DEBUG_ERR_LOG'] = true ;
$GLOBALS['DEBUG_CALL_LOG'] = true ;
$GLOBALS['DEBUG_SQL_LOG'] = true ;

// 短信接口url
$GLOBALS['SMS_CALL_URL'] = "http://222.73.117.156/msg/HttpBatchSendSM?" ; // "http://222.73.117.158/msg/HttpBatchSendSM?" ;
$GLOBALS['SMS_CALL_OUTTIME'] = 300 ;	// 短信过期时间

$GLOBALS['DEBUG_PAY'] = true ;		// 支付系统是否调试,调试状态付钱一律 1 分
// 微信
$GLOBALS['WX_APPID'] = "wx865a0f13acf4bc81" ;	// 公众账号ID
$GLOBALS['WX_MCHID'] = "1304926501" ;			// 商户号
$GLOBALS['WX_KEY'] = "guojiajia789weixinZHIFUjiekou668" ;
$GLOBALS['WX_SPBILL_IP'] = "123.206.222.43" ;
$GLOBALS['WX_NOTIFY_URL'] = "http://www.jajahome.com/furniture/call_back_wx_pay.php" ;
$GLOBALS['WX_SEND_unifiedorder'] = "https://api.mch.weixin.qq.com/pay/unifiedorder" ;
$GLOBALS['WX_SEND_orderquery'] = "https://api.mch.weixin.qq.com/pay/orderquery" ;

// 支付宝
$GLOBALS['ZFB_SELLER_ID'] = "2930643693@qq.com" ;	// 卖家支付宝账号 
$GLOBALS['ZFB_PARTNER'] = "2088121185691103" ;	// 签约的支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。
$GLOBALS['ZFB_NOTIFY_URL'] = "http://www.jajahome.com/furniture/call_back_zfb_pay.php" ;
$GLOBALS['ZFB_KEY']	= "1t3vk8iaw4s1btizotptxr4biknio6vp" ;

// vip充值回调
$GLOBALS['WX_NOTIFY_URL_VIP'] = "http://www.jajahome.com/furniture/call_back_wx_pay_vip.php" ;
$GLOBALS['ZFB_NOTIFY_URL_VIP'] = "http://www.jajahome.com/furniture/call_back_zfb_pay_vip.php" ;


$GLOBALS['TCB_LIST'] = array('RCD' , '完整家居' , '绿城','贵阳宽城国际','花样年华') ;

// 时区
date_default_timezone_set('PRC');

?>