<?php
	$GLOBALS["ERR"]["OK"] = 0 ;						$GLOBALS["MES"]["OK"] = "调用成功" ;

	$GLOBALS["ERR"]["ERR_CALL_NO_CMD"] = 1 ;		$GLOBALS["MES"]["ERR_CALL_NO_CMD"] = "调用的接口CMD未找到" ;
	$GLOBALS["ERR"]["ERR_CALL_TRY"] = 2 ;			$GLOBALS["MES"]["ERR_CALL_TRY"]	= "程序崩溃错误" ;	
	
	$GLOBALS["ERR"]["ERR_USER_NOTFIND"]	= -1 ;		$GLOBALS["MES"]["ERR_USER_NOTFIND"] = "用户不存在或未登陆" ;
	$GLOBALS["ERR"]["ERR_USER_PASSFIAL"] = -2 ;		$GLOBALS["MES"]["ERR_USER_PASSFIAL"] = "用户密码错误" ;
	$GLOBALS["ERR"]["ERR_USER_PASSCHANGE"] = -3 ;	$GLOBALS["MES"]["ERR_USER_PASSCHANGE"] = "密码修改失败" ;
	$GLOBALS["ERR"]["ERR_USER_NOEMPTY"] = -4 ;		$GLOBALS["MES"]["ERR_USER_NOEMPTY"]	= "密码不能为空" ;
	$GLOBALS["ERR"]["ERR_USER_CODE"] = -5 ;			$GLOBALS["MES"]["ERR_USER_CODE"]	= "验证码错误" ;
	$GLOBALS["ERR"]["ERR_USER_EXIST"] = -6 ;		$GLOBALS["MES"]["ERR_USER_EXIST"]	= "用户名已被使用" ;
	$GLOBALS["ERR"]["ERR_USER_CODENO_OUT"] = -7 ;	$GLOBALS["MES"]["ERR_USER_CODENO_OUT"]	= "验证码还未过期" ;
	$GLOBALS["ERR"]["ERR_USER_CODE_SEND"] = -8 ;	$GLOBALS["MES"]["ERR_USER_CODE_SEND"]	= "验证码发送失败" ;
	$GLOBALS["ERR"]["ERR_USER_NO_DATA"] = -9 ;		$GLOBALS["MES"]["ERR_USER_NO_DATA"]	= "没有找到对应数据" ;
	$GLOBALS["ERR"]["ERR_BASE64"] = -10 ;			$GLOBALS["MES"]["ERR_BASE64"]	= "base64 解码数据失败" ;
	$GLOBALS["ERR"]["ERR_HEAD_TYPE"] = -11 ;		$GLOBALS["MES"]["ERR_HEAD_TYPE"]	= "头像存储类型丢失" ;
	$GLOBALS["ERR"]["ERR_FILE_SAVE"] = -12 ;		$GLOBALS["MES"]["ERR_FILE_SAVE"]	= "文件保存失败" ;
	$GLOBALS["ERR"]["ERR_FILE_NOT_IMG"] = -13 ;		$GLOBALS["MES"]["ERR_FILE_NOT_IMG"]	= "文件不是图片文件" ;
	$GLOBALS["ERR"]["ERR_PARAM_ISNULL"] = -14 ;		$GLOBALS["MES"]["ERR_PARAM_ISNULL"]	= "提交数据不能都为空" ;
	$GLOBALS["ERR"]["ERR_DATABASE"] = -15 ;			$GLOBALS["MES"]["ERR_DATABASE"]	= "数据库操作失败" ;
	$GLOBALS["ERR"]["ERR_PARAM_ERROR"] = -16 ;		$GLOBALS["MES"]["ERR_PARAM_ERROR"]	= "参数类型或值非法" ;
	$GLOBALS["ERR"]["ERR_NOFIND_VALER"] = -17 ;		$GLOBALS["MES"]["ERR_NOFIND_VALER"]	= "未找到对应数据" ;
	$GLOBALS["ERR"]["ERR_NOFIND_INVITE"] = -18 ;	$GLOBALS["MES"]["ERR_NOFIND_INVITE"]	= "邀请码不存在" ;
	$GLOBALS["ERR"]["ERR_TRADE_INVITE"] = -19 ;		$GLOBALS["MES"]["ERR_TRADE_INVITE"]	= "订单号码不存在" ;
	$GLOBALS["ERR"]["ERR_PAY_ZERO"] = -20 ;			$GLOBALS["MES"]["ERR_PAY_ZERO"]	= "充值金额为0" ;
	$GLOBALS["ERR"]["ERR_PAY_NOAUDIT"] = -21 ;		$GLOBALS["MES"]["ERR_PAY_NOAUDIT"]	= "订单修改价格未审核通过" ;
	$GLOBALS["ERR"]["ERR_PAY_STATE"] = -22 ;		$GLOBALS["MES"]["ERR_PAY_STATE"]	= "订单状态为非付款状态,无法付款" ;
	$GLOBALS["ERR"]["ERR_PAY_NO_EQUAL"] = -23 ;		$GLOBALS["MES"]["ERR_PAY_NO_EQUAL"]	= "支付金额和汇总金额不相等" ;
	$GLOBALS["ERR"]["ERR_PAY_LOGSTATE"] = -24 ;		$GLOBALS["MES"]["ERR_PAY_LOGSTATE"]	= "日至状态错误,验证失败" ;
	$GLOBALS["ERR"]["ERR_AUDIT_OK"] = -25 ;			$GLOBALS["MES"]["ERR_AUDIT_OK"]	= "已经审核成功,不能重复提交." ;
	$GLOBALS["ERR"]["ERR_TRADE_STATE"] = -26 ;		$GLOBALS["MES"]["ERR_TRADE_STATE"]	= "订单状态错误." ;
	$GLOBALS["ERR"]["ERR_TRADE_NOTTRADE"] = -27 ;	$GLOBALS["MES"]["ERR_TRADE_NOTTRADE"]	= "此订单还未付款." ;
	$GLOBALS["ERR"]["ERR_TRADE_NOREFUND"] = -28 ;	$GLOBALS["MES"]["ERR_TRADE_NOREFUND"]	= "此订单未在退款状态." ;
	$GLOBALS["ERR"]["ERR_TEL_NOFIND"] = -29 ;		$GLOBALS["MES"]["ERR_TEL_NOFIND"]	= "此手机号不存在." ;
	$GLOBALS["ERR"]["ERR_TEL_EXIST"] = -30 ;		$GLOBALS["MES"]["ERR_TEL_EXIST"]	= "此手机号已经注册." ;
?>