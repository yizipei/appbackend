<?php

// 设置分页数据
function util_set_PAGINATION(&$arr , $total  , $count , $page)
{
	$arr['total'] = $total ;						// 全部数据总量
	$arr['count'] = ceil($total / $count) ;			// 总页数
	$arr['limit'] = $count ;						// 每页显示的数量
	$arr['offset'] = $page ;						// 当前的页码
}
function util_get_PAGINATION($json , &$rnt_offset , &$rnt_limit)
{
	$pagination = get_jsonValue($json,"pagination") ;
	$offset = get_jsonValue($pagination,"offset") ;
	$limit  = get_jsonValue($pagination,"limit") ;
	$total  = get_jsonValue($pagination,"total") ;
	$count  = get_jsonValue($pagination,"count") ;
	if (is_null($offset) || $offset  == "")	$offset = 1 ;
	if (is_null($limit) || $limit  == "")	$limit = 100 ;
	$rnt_offset = intval($offset) ;
	$rnt_limit = intval($limit) ;
	if ($rnt_offset < 1)	$rnt_offset = 1 ;
	if ($rnt_limit <= 0)	$rnt_limit = 100 ;
}
// 组织image返回数据
function util_set_IMAGE(&$arr , $obj)
{
	if (is_null($obj)) 		return null ;
	
	$img_small_id = intval($obj['img_small_id']) ;
	$img_thumb_id = intval($obj['img_thumb_id']) ;
	$img_url_id   = intval($obj['img_url_id']) ;
	if (is_null($img_small_id))		$img_small_id = 0 ;
	if (is_null($img_thumb_id))		$img_thumb_id = 0 ;
	if (is_null($img_url_id))		$img_url_id = 0 ;
	
	return util_set_IMAGE3($arr , $img_small_id , $img_thumb_id , $img_url_id) ;	
}
function util_set_IMAGE3(&$arr, $small_id , $thumb_id , $url_id)
{
	$rnt = array() ;
	$rnt["s_w"] = 0 ;	$rnt["s_h"] = 0 ;
	$rnt["t_w"] = 0 ;	$rnt["t_h"] = 0 ;
	$rnt["u_w"] = 0 ;	$rnt["u_h"] = 0 ;
	
	$small_id = intval($small_id) ;
	$thumb_id = intval($thumb_id) ;
	$url_id = intval($url_id) ;
	$addr = sql_fetch_array("select `id` , `url` , `width` , `height` from `sys_image` where `id` in ($small_id , $thumb_id , $url_id)",'id') ;
	$arr['small'] = "" ;
	$arr['thumb'] = "" ;
	$arr['url'] = "" ;
	$ext = "" ; $ew = 0 ; $eh = 0 ;
	if (array_key_exists($small_id , $addr)){
		$arr['small'] = $GLOBALS['URL_IMG_HEAD'].$addr[$small_id]['url'] ;
		$rnt["s_w"] = $addr[$small_id]['width'] ;	$rnt["s_h"] = $addr[$small_id]['height'] ;
		$ext = $arr['small'] ; 	$ew = $rnt["s_w"] ; $eh = $rnt["s_h"] ;
	}
	if (array_key_exists($thumb_id , $addr)){
		$arr['thumb'] = $GLOBALS['URL_IMG_HEAD'].$addr[$thumb_id]['url'] ;
		$rnt["t_w"] = $addr[$thumb_id]['width'] ;	$rnt["t_h"] = $addr[$thumb_id]['height'] ;
		$ext = $arr['thumb'] ;  $ew = $rnt["t_w"] ; $eh = $rnt["t_h"] ;
	}
	if (array_key_exists($url_id , $addr)){
		$arr['url'] = $GLOBALS['URL_IMG_HEAD'].$addr[$url_id]['url'] ;
		$rnt["u_w"] = $addr[$url_id]['width'] ;	$rnt["u_h"] = $addr[$url_id]['height'] ;
		$ext = $arr['url'] ;  	$ew = $rnt["u_w"] ; $eh = $rnt["u_h"] ;
	}
	if ($arr['small'] == ""){	$arr['small'] = $ext ;	$rnt["s_w"] = $ew ;	$rnt["s_h"] = $eh ;	}
	if ($arr['thumb'] == ""){	$arr['thumb'] = $ext ;	$rnt["t_w"] = $ew ;	$rnt["t_h"] = $eh ;	}
	if ($arr['url'] == "")	{	$arr['url'] = $ext ;	$rnt["u_w"] = $ew ;	$rnt["u_h"] = $eh ;	}
	return $rnt ;
}
function  util_set_IMAGE_one(&$arr , $img_id)
{
	$rnt = array() ;
	$rnt["s_w"] = 0 ;	$rnt["s_h"] = 0 ;
	$rnt["t_w"] = 0 ;	$rnt["t_h"] = 0 ;
	$rnt["u_w"] = 0 ;	$rnt["u_h"] = 0 ;
	
	$arr['small'] = "" ;
	$arr['thumb'] = "" ;
	$arr['url'] = "" ;
	if ($img_id > 0){
		$addr = sql_fetch_one("select `id` , `url` , `width` , `height` from `sys_image` where `id` = '$img_id'") ;
		if (array_key_exists('url', $addr)){
			$arr['small'] = $GLOBALS['URL_IMG_HEAD'].$addr['url'] ;
			$arr['thumb'] = $GLOBALS['URL_IMG_HEAD'].$addr['url'] ;
			$arr['url']   = $GLOBALS['URL_IMG_HEAD'].$addr['url'] ;
			
			$rnt["s_w"] = $addr['width'] ;	$rnt["s_h"] = $addr['height'] ;
			$rnt["t_w"] = $addr['width'] ;	$rnt["t_h"] = $addr['height'] ;
			$rnt["u_w"] = $addr['width'] ;	$rnt["u_h"] = $addr['height'] ;
		}
		$rnt['img_obj'] = $addr ;
	}
	return $rnt ;
}
function  util_set_IMAGE_one_url(&$arr , $url)
{
	if ($url != ""){
		$arr['small'] = $url ;
		$arr['thumb'] = $url ;
		$arr['url']   = $url ;
	}
}
// 获取单品的基础价格和折扣价格
function util_get_item_price(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from `sys_item` where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['price_basic'] 	= array() ;
	$arr['price_discount']	= array() ;
	
	$item_id = intval($item['id']) ;
	
	// 最小基础价和折扣价
	$m_b_p 	= intval($item['price_base']) ;
	$m_b_zk = intval(intval($item['price_base']) * doubleval($item['price_discount'])) ;
	// 最大基础价和折扣价
	$x_b_p 	= $m_b_p ;
	$x_b_zk = $m_b_zk ;
	
	$one = sql_fetch_one("select min(price_custom) as m_p , min(price_custom * price_discount) as m_p_zk , max(price_custom) as x_p , max(price_custom * price_discount) as x_p_zk  from sys_item_prices where item_id = $item_id") ;
	if (is_array($one)){
		// 价格要加上定制价
		$m_b_p += intval($one['m_p']) ;
		$x_b_p += intval($one['x_p']) ;
		
		$m_b_zk += intval($one['m_p_zk']) ;
		$x_b_zk += intval($one['x_p_zk']) ;
	}
	
	util_set_PRICE_RANGE($arr['price_basic'] 	, $item_id , $m_b_p  , $x_b_p) ;
	util_set_PRICE_RANGE($arr['price_discount'] , $item_id , $m_b_zk , $x_b_zk) ;	
}
// 获取详细单品的基础价格和折扣价格
function util_get_item_price_preview(&$arr , $item , $preview)
{
	$arr['price_basic'] 	= array() ;
	$arr['price_discount']	= array() ;
	
	$item_id = intval($item['id']) ;
	$fabric_id = intval($preview['fid']) ;
	$material_id = intval($preview['mid']) ;
	
	// 最小基础价和折扣价
	$m_b_p 	= intval($item['price_base']) ;
	$m_b_zk = intval(intval($item['price_base']) * doubleval($item['price_discount'])) ;
	// 最大基础价和折扣价
	$x_b_p 	= $m_b_p ;
	$x_b_zk = $m_b_zk ;
		
	$one = sql_fetch_one("select min(price_custom) as m_p , min(price_custom * price_discount) as m_p_zk , max(price_custom) as x_p , max(price_custom * price_discount) as x_p_zk  from sys_item_prices where item_id = $item_id and fabric_id = $fabric_id and material_id = $material_id") ;
	if (is_array($one)){
		// 价格要加上定制价
		$m_b_p += intval($one['m_p']) ;
		$x_b_p += intval($one['x_p']) ;
		
		$m_b_zk += intval($one['m_p_zk']) ;
		$x_b_zk += intval($one['x_p_zk']) ;
	}
	
	util_set_PRICE_RANGE($arr['price_basic'] 	, $item_id , $m_b_p  , $x_b_p) ;
	util_set_PRICE_RANGE($arr['price_discount'] , $item_id , $m_b_zk , $x_b_zk) ;	
}
// 首页轮播对象
function util_set_BANNER_ITEM(&$arr , $item)
{
	$arr['id'] 			= $item['id'] ;
	$arr['description']	= $item['description'] ;
	$arr['url']			= $item['url'] ;
	$arr['image']		= array() ;
	util_set_IMAGE($arr['image'] , $item) ;	
	$arr['action']		= $item['action'] ;
	$arr['action_id']	= $item['action_id'] ;
}
// 首页推荐对象
function util_set_RECOMMEND_ITEM(&$arr , $item)
{
	$arr['id'] 			= $item['id'] ;
	$arr['name']		= $item['name'] ;
	$arr['url']			= $item['url'] ;
	// 设计师信息
	$arr['designer']	= array() ;
	util_set_USER($arr['designer'] , $item['designer_id']) ;
	// 图片信息
	$arr['image']		= array() ;
	util_set_IMAGE($arr['image'] , $item) ;
	$arr['action']		= $item['action'] ;
	$arr['action_id']	= $item['action_id'] ;
}
// 设置设计师的基本返回数据
function util_set_USER(&$arr , $dgid)
{
	$desg = sql_fetch_one("select * from user_info where `id` = '$dgid'") ;
	$info = sql_fetch_array("select * from user_info_ex where `user_id` = '$dgid'", "u_key") ;
	if (is_array($desg) && array_key_exists("id", $desg)){
		$arr['id'] = $desg['id'] ;
		$arr['avatar'] = array() ;
		
		if (array_key_exists("img_small_id" , $info)) 	$i_s_id = $info['img_small_id']['u_value'] ;	else 	$i_s_id = 0 ;
		if (array_key_exists("img_thumb_id" , $info)) 	$i_t_id = $info['img_thumb_id']['u_value'] ;	else 	$i_t_id = 0 ;
		if (array_key_exists("img_url_id" , $info)) 	$i_u_id = $info['img_url_id']['u_value'] ;		else 	$i_u_id = 0 ;
		
		util_set_IMAGE3($arr['avatar'] , $i_s_id , $i_t_id , $i_u_id) ;
		$arr['username'] = $desg['username'] ;
		$arr['nickname'] = $desg['nickname'] ;
		
		if (array_key_exists("user_sex" , $info)) 	$u_s = $info['user_sex']['u_value'] ;	else 	$u_s = "" ;
		
		$arr['sex'] = $u_s ;		if ($arr['sex'] == '')		$arr['sex'] = "男" ;
		$arr['type'] = $desg['user_type'] ; 
		$arr['type_class'] = $desg['user_type_class'] ; 
		
		if (array_key_exists("birthday" , $info)) 	$bdy = $info['birthday']['u_value'] ;	else 	$bdy = "" ;
		
		$arr['birthday'] = $bdy ;
		$arr['mobile'] = $desg['tel'] ;
		
		if (array_key_exists("star_rank" , $info)) 	$s_r = $info['star_rank']['u_value'] ;	else 	$s_r = "0" ;
		$arr['rank'] = intval($s_r) ;	
		$arr['loginType'] = $desg['user_from'] ;
		
		if (intval($arr['loginType']) != 1 && array_key_exists("third_face" , $info)){
			$arr['avatar']['small'] = $info['third_face']['u_value'] ;
			$arr['avatar']['thumb'] = $info['third_face']['u_value'] ;
			$arr['avatar']['url'] = $info['third_face']['u_value'] ;
		}
	}
	$inv = sql_fetch_one("select * from user_invite where `uid` = '$dgid'") ;
	if (is_array($inv) && array_key_exists("uid", $inv)){
		$arr['invitecode'] = $inv['invite'] ;
		$arr['invitecode1'] = $inv['invite_f'] ;
	}else{
		$arr['invitecode'] = '' ;
		$arr['invitecode1'] = '' ;
	}
	
	// 可用资产
	$alluser_take = sql_fetch_one_cell("select sum(`rebate_money`) from `pay_trade_rebate` where `uid` = '$dgid' and `state` != 2") ;
	$take_money = sql_fetch_one_cell("select sum(`take_money`) from `pay_rebate_usertake` where `uid` = '$dgid'  and `state` != 3") ;
	$arr['assets'] = intval($alluser_take) -  intval($take_money) ;
	
	// 可用积分
	$arr['integral'] = intval(sql_fetch_one_cell("select sum(`score`) from `log_user_score` where uid = $dgid")) ;
	
	// 邀请的一级和二级好友总个数
	
	$totle = sql_fetch_one("select * from `user_invite_totle` where `uid` = '$dgid'") ;
	if (is_array($totle)){
		$arr['friends'] = intval($totle['one_friends']) + intval($totle['two_friends']) ;	
	
	}else{
		$arr['friends'] = 0 ;
	}	
}

// 商品选项
function util_set_ITEM_OPTION(&$arr , $item)
{
	$arr['id'] 		= $item['id'] ;
	$arr['name'] 	= $item['name'] ;
	$arr['image'] 	= array() ;
	util_set_IMAGE($arr['image'] , $item) ;
}
// 给一个数组加上面料数据结构 , 参数为字符串id序列
function util_set_ITEM_OPTION_fabric(&$arr , $stringval)
{
	if ($stringval == "")	return ;	
	$ids = explode("," , $stringval) ;
	if (count($ids) == 1 && intval($ids[0]) == 0)		return ;
	
	$vals = sql_fetch_array("select * from base_sys_fabric where `id` in ($stringval)" , "id") ;	
	foreach ($vals as $v)
	{
		$a = array() ;
		util_set_ITEM_OPTION($a , $v) ;
		$arr[] = $a ;
	}
}
// 给一个数组加上材质数据结构 , 参数为字符串id序列
function util_set_ITEM_OPTION_material(&$arr , $stringval)
{
	if ($stringval == "")	return ;	
	$ids = explode("," , $stringval) ;
	if (count($ids) == 1 && intval($ids[0]) == 0)		return ;
	
	$vals = sql_fetch_array("select * from base_sys_material where `id` in ($stringval)" , "id") ;	
	foreach ($vals as $v)
	{
		$a = array() ;
		util_set_ITEM_OPTION($a , $v) ;
		$arr[] = $a ;
	}
}
// 商品预览图
function util_set_ITEM_PREVIEW(&$arr , $item , $def_img)
{
	$arr['fabric'] 		= $item['fid'] ;
	$arr['material'] 	= $item['mid'] ;
	$arr['image'] 		= array() ;
	
	util_get_item_price_preview($arr , $def_img , $item) ;
	
	// 默认角度
	$a = array() ;
	util_set_IMAGE($a , $item) ;
	$arr['image'][] = $a ;
	// 其他角度
	$list = sql_fetch_rows("select * from base_sys_preview_angle where `pid` = '{$item['id']}'") ;
	if (count($list) > 0){
		foreach ($list as $t){
			$a = array() ;
			util_set_IMAGE($a , $t) ;
			$arr['image'][] = $a ;
		}
	}
	
	if ($def_img['img_url_id'] == $item['img_url_id']){
		$arr['is_default'] = 1 ;
	}else{
		$arr['is_default'] = 0 ;
	}
	// 产品透视图
	if (intval($item['img_tz_id']) > 0){
		$arr['set_image'] = array() ;	
		util_set_IMAGE_one($arr['set_image'] , $item['img_tz_id']) ;
	}else{
		// 这里是随便找的一个在用的套装中的
		$ones = sql_fetch_rows("select img_id from taozhuang_pos where item_id = '{$item['item_id']}'") ;
		if (is_array($ones) && count($ones) == 1){
			foreach ($ones as $o){
				$arr['set_image'] = array() ;
				util_set_IMAGE_one($arr['set_image'] , $o['img_id']) ;
				break ;
			}
		}
	}
	return ($arr['is_default'] == 1) ;
}
// 设置默认预览图
function util_set_default_ITEM_PREVIEW(&$arr , $fabric , $material , $def_img)
{
	$arr['fabric'] 		= $fabric ;
	$arr['material'] 	= $material ;
	$arr['image'] 		= array() ;
	$a = array() ;
	util_set_IMAGE($a , $def_img) ;
	$arr['image'][] = $a ;
	$arr['is_default'] = 1 ;
}
// 跟材质和面料相关的预览图信息序列 , def = 默认显示的图片
function util_set_ITEM_PREVIEW_lists(&$arr , $def)
{
	$vals = sql_fetch_array("select * from base_sys_preview where `item_id` = '{$def['id']}'" , "id") ;
	
	$bdef = false ;
	foreach ($vals as $v)
	{
		$a = array() ;
		if (util_set_ITEM_PREVIEW($a , $v , $def) == true){
			$bdef = true ;
		}
		$arr[] = $a ;
	}
	if ($bdef == false && count($arr) > 0){
		$arr[0]['is_default'] = 1 ;
	}
}

// 商品评论
function util_set_COMMENT_SIMPLE(&$arr , $item)
{
	$arr['id'] 	 = $item['id'] ;
	$arr['text'] = $item['text'] ;
	$arr['user'] = array() ;
	util_set_USER($arr['user'] , $item['designer_id']) ;
}

// 获取单品评论信息 -- 返回评论总数 
function util_set_COMMENT_SIMPLE_item(&$arr , $item_id , $start , $count)
{
	$rnt_count = intval(sql_fetch_one_cell("select count(`id`) from user_item_comment where `item_id` = '$item_id'")) ;
	if ($rnt_count == 0)		return $rnt_count ;
	
	$vals = sql_fetch_array("select * from user_item_comment where `item_id` = '$item_id' order by `id` desc limit $start , $count" , "id") ;
	foreach ($vals as $v)
	{
		$a = array() ;
		util_set_COMMENT_SIMPLE($a , $v) ;
		$arr[] = $a ;
	}
	return $rnt_count ;
}
function util_set_COMMENT_SIMPLE_taozhuang(&$arr , $tz_id , $start , $count)
{
	$rnt_count = intval(sql_fetch_one_cell("select count(`id`) from user_taozhuang_comment where `tz_id` = '$tz_id'")) ;
	if ($rnt_count == 0)		return $rnt_count ;
	
	$vals = sql_fetch_array("select * from user_taozhuang_comment where `tz_id` = '$tz_id' order by `id` desc limit $start , $count" , "id") ;
	foreach ($vals as $v)
	{
		$a = array() ;
		util_set_COMMENT_SIMPLE($a , $v) ;
		$arr[] = $a ;
	}
	return $rnt_count ;
}

// 套装列表中的套装对象
function util_set_SET_SIMPLE(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from taozhuang where `sid` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['id'] 		= $item['sid'] ;
	$arr['name'] 	= $item['name'] ;
	$arr['tips'] 	= $item['tips'] ;
	$arr['image'] 	= array() ;
	util_set_IMAGE($arr['image'] , $item) ;
}
// 获取套装列表中套装序列数组
function util_set_SET_SIMPLE_list(&$arr , $stringval)
{
	if ($stringval == "")	return ;	
	$ids = explode("," , $stringval) ;
	if (count($ids) == 1 && intval($ids[0]) == 0)		return ;
	
	$vals = sql_fetch_array("select * from taozhuang where `sid` in ($stringval) and `app_return` = 1" , "sid") ;	
	foreach ($vals as $v)
	{
		$a = array() ;
		util_set_SET_SIMPLE($a , $v) ;
		$arr[] = $a ;
	}
}
// 单品列表中的单品对象
function util_set_ITEM_SIMPLE(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from sys_item where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['id'] 		= $item['id'] ;
	$arr['name'] 	= $item['name'] ;
	$arr['price'] 	= $item['price_base'] + $item['price_custom'] ;
	$arr['image'] 	= array() ;
	util_set_IMAGE($arr['image'] , $item) ;
	
	util_get_item_price($arr , $item) ;
	
	return $item ;
}
// 获取单品列表中的单品对象序列
function util_set_ITEM_SIMPLE_list(&$arr , $stringval)
{
	if ($stringval == "")	return ;	
	$ids = explode("," , $stringval) ;
	if (count($ids) == 1 && intval($ids[0]) == 0)		return ;
	
	$vals = sql_fetch_array("select * from sys_item where `id` in ($stringval) and `app_return` = 1" , "id") ;	
	foreach ($vals as $v)
	{
		$a = array() ;
		util_set_ITEM_SIMPLE($a , $v) ;
		$arr[] = $a ;
	}
}
// 获取秀家列表中的秀家对象
function util_set_SHOW_SIMPLE(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from show where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['id'] 		= $item['id'] ;
	$arr['name'] 	= $item['name'] ;
	$arr['image'] 	= array() ;
	util_set_IMAGE($arr['image'] , $item) ;
	
	$arr['type'] 	= $item['state'] ;
}

function util_set_SHOW(&$arr , $val , $uid = 0)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from `show` where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['id'] 			= $item['id'] ;
	$arr['name'] 		= $item['name'] ;
	$arr['preview'] 	= array() ;
	$imgrn = util_set_IMAGE($arr['preview'] , $item) ;
	$arr['width'] 		= $imgrn['u_w'] ;
	$arr['height'] 		= $imgrn['u_h'] ;
	$arr['type']		= intval($item['state']) ;
	$arr['list']			= array() ;	
	
	if ($arr['type'] == 0){
		$arr['content']		= "" ;
		$listtz = sql_fetch_rows("select * from `show_pos` where `sid` = '{$item['id']}'");
		
		foreach($listtz as $tz){
			$v = array() ;
			util_set_SHOW_ITEM($v , $tz) ;
			$arr['list'][] = $v ;
		}
	}else{
		$time=date("Y-m-d",$item['publish_time']);
		if($item['publish_uid']>=2000)
		{
			$row=sql_fetch_one("select username,nickname from user_info where `id` = ".$item['publish_uid']);
			$name=$row["nickname"]?$row["nickname"]:$row["username"];
		}
		else
		{
			$name="JAJAHOME";
		}
		$html_head="
		<div class='set_show_title'>
			".$item['name']."
		</div>
		<div class='set_show_time_editor'>
			<span>".$time."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>".$name."<span>
		</div>";
		$arr['content']		= _util_sethtml_content($html_head.$item['content']) ; 
	}
	
	// 是否已经加入收藏
	$arr['favorite'] = 0 ;
	$sid = $item['id'] ;
	if ($uid > 0){
		$arr['favorite'] = intval(sql_fetch_one_cell("select type_id from user_favorite where `uid` = '$uid' and `type` = 2 and `type_id` = '$sid'")) > 0 ? 1 : 0 ;
	}
}
function util_set_SHOW_ITEM(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from `show_pos` where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['id'] 			= $item['id'] ;
	$arr['pos'] 		= array("x"=>floatval($item['posx']) , "y"=>floatval($item['posy'])) ;
	$arr['url']			= $item['url'] ;
	$arr['action']		= $item['action'] ;
	$arr['action_id']	= $item['action_id'] ;	
}
// 获取一个单品的详细信息
function util_set_ITEM(&$arr , $val , $uid)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from `sys_item` where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	if (is_array($item) == false) 	return ;
	$item_id = $item['id'] ;
	
	$arr['id']			= $item['id'] ;
	$arr['name']		= $item['name'] ;
	$arr['price_base']	= $item['price_base'] ;			// 接口中已删
	$arr['price_custom']= $item['price_custom'] ;		// 接口中已删
	util_get_item_price($arr , $item) ;					// 新的价格计算方式
	
	$arr['published']	= intval($item['app_return']) ;
	$arr['fabric']		= array() ;
	util_set_ITEM_OPTION_fabric($arr['fabric'] , $item['fabric_list']) ;
	if (count($arr['fabric']) <= 0){
		unset($arr['fabric']) ;
	}
	$arr['material']	= array() ;
	util_set_ITEM_OPTION_material($arr['material'] , $item['material_list']) ;
	if (count($arr['material']) <= 0){
		unset($arr['material']) ;
	}
	$arr['preview']		= array() ;
	util_set_ITEM_PREVIEW_lists($arr['preview'] , $item) ;
	if (count($arr['preview']) == 0){
		$a = array() ;
		util_set_default_ITEM_PREVIEW($a , 0 , 0 , $item) ;
		$arr['preview'][] = $a ;
	}
	$arr['related']		= array() ;
	util_set_ITEM_SIMPLE_list($arr['related'] , $item['related_list']) ;
	
	$arr['comment_list'] = array() ;
	$arr['comment_total'] = util_set_COMMENT_SIMPLE_item($arr['comment_list'] , $item_id , 0 , 2) ;
	
	// 是否已经加入收藏
	$arr['favorite'] = 0 ;
	if ($uid > 0){
		$arr['favorite'] = intval(sql_fetch_one_cell("select type_id from user_favorite where `uid` = '$uid' and `type` = 1 and `type_id` = '$item_id'")) > 0 ? 1 : 0 ;
	}
	
	$arr['brand'] = sql_fetch_one_cell("select `name` from grp_brand where `id` = '{$item['brand_id']}'") ;	// 品牌
	// 风格
	$style = sql_fetch_rows("select g.`name` from sys_item_style s left join grp_style g on s.style_id = g.`id` where s.`item_id` = '{$item['id']}'") ;
	$arr['style'] = "" ;
	if (count($style) > 0){
		$dh = "" ;
		foreach ($style as $s){
			$arr['style'] .= $dh.$s['name'] ;
			$dh = "," ;
		} 
	}
	$arr['size'] = $item['size'] ;	// 规格
	$sizelist = sql_fetch_rows("select * from sys_item_size where `item_id` = '{$item['id']}'") ;
	if (is_array($sizelist) && count($sizelist)){
		$arr['size'] = "" ;
		$dh = "" ;
		foreach ($sizelist as $s){
			$strs = trim($s['size']) ;			
			if (strlen($strs) > 0){
				$arr['size'] .= $dh . $strs ;		$dh = "," ;
			}
		}
		if (strlen($arr['size']) == 0){
			$arr['size'] = $item['size'] ;
		}
	}
	
	$arr['gjj_code'] = $item['gjj_code'] ;
	$arr['desc'] = $item['tips'] ;  // 描述
	// 产地
	$place = sql_fetch_rows("select g.`name` from sys_item_place s left join grp_place g on s.place_id = g.`id` where s.`item_id` = '{$item['id']}'") ;
	$arr['place'] = "" ;
	if (count($place) > 0){
		$dh = "" ;
		foreach ($place as $s){
			$arr['place'] .= $dh.$s['name'] ;
			$dh = "," ;
		} 
	}
}

// 套装 中 item 信息
function util_set_SET_ITEMINFO(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from taozhuang_pos where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
		
	$arr['id'] 				= $item['id'] ;  
	$arr['scale']			= floatval($item['zoom']) ;
	$arr['flip']				= intval($item['mirror']) ;
	$arr['pos']				= array("x"=>floatval($item['x']) , "y"=>floatval($item['y'])) ;
	$arr['anchor']			= array("x"=>floatval($item['anchor_x']) , "y"=>floatval($item['anchor_y'])) ;
	$test = new test_bk() ;
	$ones = $test->get_taozhuang_pos_for_ts_image($item['id']) ;	
	$arr['image_size']		= array("x"=>floatval($ones[4]) , "y"=>floatval($ones[5])) ;
	$arr['image']			= array() ;	
	util_set_IMAGE_one_url($arr['image'] , $ones[1]) ;
	$arr['name'] = "" ;

	$it = sql_fetch_one("select * from sys_item where `id` = '{$item['item_id']}'") ;	
	if (is_array($it) && array_key_exists("name", $it)){
		$arr['name']			= $it['name'] ;
		
		$cz = sql_fetch_one("select * from base_sys_preview where `item_id` = '{$it['id']}' and `img_tz_id` = '{$ones[0]}'") ;
		if (is_array($cz) && array_key_exists("fid", $cz)){
			$fid = intval($cz['fid']) ;
			$mid = intval($cz['mid']) ;
			if ($fid > 0)	$arr['fabric'] = $fid ;
			if ($mid > 0)	$arr['material'] = $mid ;
			util_get_item_price_preview($arr , $it , $cz) ;
		}else{
			util_get_item_price($arr , $it) ;
		}
	}
	$arr['count'] =  intval($item['count']) ;
	$arr['pay_state'] =  intval($item['pay_state']) ;
}

// 套装位置
function util_set_SET_POS(&$arr , $val)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from taozhuang_pos where `id` = '$val'") ;
	}else{
		$item = $val ;
	}
	$arr['id']				= $item['id'] ;
	$arr['zorder']			= $item['pos'] ;
	$arr['parent_zorder']	= $item['father'] ;
	$arr['item_size']		= sql_fetch_one_cell("select count(`id`) from taozhuang_pos where `tzid` = '{$item['tzid']}' and `pos` = '{$item['pos']}'") ;
	$arr['item_info']		= array() ;
	util_set_SET_ITEMINFO($arr['item_info'] , $item) ;
	if ($item['show_name'] == ""){
		$arr['name']			= $arr['item_info']['name'] ;
	}else{
		$arr['name']			= $item['show_name'] ;
	}
	$arr['fabric_name'] = trim($item['fab_name']) ;
	$arr['material_name'] = trim($item['mat_name']) ;
	if ($arr['fabric_name'] == "")		$arr['fabric_name'] = "面料" ;
	if ($arr['material_name'] == "")	$arr['material_name'] = "材质" ;
}

// 获取一个套装对象
function util_set_SET(&$arr , $val , $uid = 0)
{
	if (is_array($val) == false){
		$item = sql_fetch_one("select * from taozhuang where `sid` = '$val'") ;
	}else{
		$item = $val ;
	}
	
	$arr['id'] 			= $item['sid'] ;
	$arr['name'] 		= $item['name']."  (".$item['sid'].")" ;
//	$arr['price_base'] 	= $item['price_base'] ;
	$arr['price_desc'] 	= $item['price_memo'] ;
	$arr['discount_rate'] = doubleval($item['price_custom']) ;
	if ($arr['discount_rate'] <= 0)		$arr['discount_rate'] = 1 ;
	
	$arr['list']			= array() ;	
	// $listtz = sql_fetch_rows("select * from taozhuang_pos  where `tzid` = '{$item['sid']}' and `order` = 0 order by `pos` asc");
	$listtz = sql_fetch_rows("select t.* from taozhuang_pos t left join sys_item s on t.item_id = s.`id` left join grp_class g on s.class_id = g.`id` where t.`tzid` = '{$item['sid']}' and t.`order` = 0 order by g.`t_order` desc , t.`pos` asc");
	foreach($listtz as $tz){
		$v = array() ;		
		util_set_SET_POS($v , $tz) ;
		$arr['list'][] = $v ;
	}
	$arr['comment_list'] = array() ;
	$arr['comment_total'] = util_set_COMMENT_SIMPLE_taozhuang($arr['comment_list'] , $item['sid'] , 0 , 2) ;
	
	$arr['related']		= array() ;
	util_set_SET_SIMPLE_list($arr['related'] , $item['related_list']) ;
	
	// 价格计算类型 0：总价 1: 累计
	$arr['type'] = 0 ;		// 默认0
	if ($item['state_tree'] == 0)		$arr['type'] = 0 ;		// 过夹夹
	elseif ($item['state_tree'] == 1)	$arr['type'] = 1 ;		// 完整家居 
	
	if ($arr['type'] == 0){
		$arr['package_price'] = doubleval($item['price_custom']) ;
	}
	
	// 是否已经加入收藏
	$arr['favorite'] = 0 ;
	$sid = $item['sid'] ;
	if ($uid > 0){
		$arr['favorite'] = intval(sql_fetch_one_cell("select type_id from user_favorite where `uid` = '$uid' and `type` = 0 and `type_id` = '$sid'")) > 0 ? 1 : 0 ;
	}
	$blueprint = array() ;
	util_set_IMAGE_one($blueprint , $item['back_img']) ;	
	$arr['image_blueprint'] = $blueprint['url'] ;
}

// 套装中 item 信息(中可更换款式信息)
function util_set_SET_POS_ITEM(&$arr , $val , $uid)
{
	$arr['id'] = $val['id'] ;  
	$arr['item'] = array() ;
	util_set_ITEM($arr['item'] , $val['item_id'] , $uid);
	$arr['info'] = array() ;
	util_set_SET_ITEMINFO($arr['info'] , $val);
}

// 获取用户 SESSION
function util_set_SESSION(&$arr , $token)
{
	$usess = sql_fetch_one("select * from sys_session where `token` = '$token' and `deadline` > unix_timestamp()") ;
	$arr['sid'] = $usess['session'] ;
	$arr['token'] = $usess['token'] ;
}
// 通过session获取用户id
function util_set_GETUID_for_SESSION($sess)
{
	$token = trim(get_jsonValue($sess,"token")) ; 
	$session = trim(get_jsonValue($sess,"sid")) ;
	
	if ($token != "" && $session != ""){
		$usess = sql_fetch_one("select * from sys_session where `token` = '$token' and `deadline` > unix_timestamp()") ;
		if (is_array($usess) && array_key_exists('session', $usess)){
			if ($session == $usess['session']){
				return intval($usess['uid']) ;
			}
		}
	}
	return 0 ;
}
// 设置价格范围
function util_set_PRICE_RANGE(&$arr , $id , $min , $max)
{
	$arr['id']  = doubleval($id) ;
	$arr['min'] = doubleval($min) ;
	$arr['max'] = doubleval($max) ;
}
function util_set_CONFIG(&$arr)
{
	// 套装
	$list = sql_fetch_rows("select `name` from grp_space") ;
	$arr['set_room'] = array() ;
	foreach ($list as $a){
		$arr['set_room'][] = $a['name'] ; 
	}
	$list = sql_fetch_rows("select `name` from grp_style order by `order` desc") ;
	$arr['set_style'] = array() ;
	foreach ($list as $a){
		$arr['set_style'][] = $a['name'] ; 
	}
	$list = sql_fetch_rows("select `name` from grp_color") ;
	$arr['set_color'] = array() ;
	foreach ($list as $a){
		$arr['set_color'][] = $a['name'] ; 
	}
	$list = sql_fetch_rows("select * from grp_price where `type` = 0 order by min") ;
	$arr['set_price'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_PRICE_RANGE($v , $a['id'] , $a['min'] , $a['max']) ;
		$arr['set_price'][] = $v ; 
	}
	// 单品
	$list = sql_fetch_rows("select `name` from grp_space") ;
	$arr['item_room'] = array() ;
	foreach ($list as $a){
		$arr['item_room'][] = $a['name'] ; 
	}
	$list = sql_fetch_rows("select `name` from grp_style order by `order` desc") ;
	$arr['item_style'] = array() ;
	foreach ($list as $a){
		$arr['item_style'][] = $a['name'] ; 
	}
	$list = sql_fetch_rows("select `name` from grp_color") ;
	$arr['item_color'] = array() ;
	foreach ($list as $a){
		$arr['item_color'][] = $a['name'] ; 
	}
	$list = sql_fetch_rows("select * from grp_price where `type` = 1 order by min") ;
	$arr['item_price'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_PRICE_RANGE($v , $a['id'] , $a['min'] , $a['max']) ;
		$arr['item_price'][] = $v ; 
	}	
}
function util_set_FILTER_ITEM(&$arr , $id , $text)
{
	$arr['id'] = doubleval($id) ;
	$arr['text'] = $text ;
}
function util_set_FILTER_ITEM_all(&$arr , &$list)
{
	foreach ($arr as &$r){
		$id = doubleval($r['id']) ;
		foreach ($list as $a){
			if (doubleval($a['father']) == $id){
				$v = array() ;
				util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
				$r['items'][] = $v ;
			}
		}
		if (array_key_exists('items', $r)){
			util_set_FILTER_ITEM_all($r['items'] , $list) ;
		}
	}
}
function util_set_CONFIG_V2(&$arr , $city)
{
	// 套装
	$list = sql_fetch_rows("select * from grp_space where useing = 1") ;
	$arr['set_room'] = array() ;
	_util_setfilter_name($arr['set_room'] , $list) ;
	
	$list = sql_fetch_rows("select * from grp_style order by `order` desc") ;
	$arr['set_style'] = array() ;
	_util_setfilter_name($arr['set_style'] , $list) ;
	
	$arr['set_brand'] = array() ;		// 套装品牌列表	
	for($i = 0 ; $i < count($GLOBALS['TCB_LIST']) ; ++ $i){
		$vv = array() ; 
		util_set_FILTER_ITEM($vv , 10 + $i , $GLOBALS['TCB_LIST'][$i]) ;	// 0 - 过夹夹  1 - 完整家居 '  2 - 绿城
		$arr['set_brand'][] = $vv ;
	}

	/*
	$list = sql_fetch_rows("select * from grp_brand where `id` in (select s.brand_id from taozhuang_pos p inner join sys_item s on p.item_id = s.id inner join taozhuang t on t.sid = p.tzid and t.app_return = 1 where s.brand_id > 0 group by s.brand_id) order by `order`") ;
	foreach ($list as $a){
		$v = array() ;
		util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
		
		$id = $a['id'] ;
		$items = sql_fetch_rows("select * from grp_class where `id` in (select s.class_id from taozhuang_pos p inner join sys_item s on p.item_id = s.id inner join taozhuang t on t.sid = p.tzid and t.app_return = 1 where s.class_id > 0 and s.brand_id = $id group by s.class_id) order by `t_order` desc") ;
		if (is_array($items)){
			$v['items'] = array() ;
			foreach ($items as $c){
				$vv = array() ;
				$newid = intval($c['id']) + intval($id) ; 
				util_set_FILTER_ITEM($vv , $newid , $c['name']) ;
				$v['items'][] = $vv ;
			}
		}
		
		$arr['set_brand'][] = $v ; 
	}
	*/
	
	$list = sql_fetch_rows("select * from grp_color") ;
	$arr['set_color'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
		$arr['set_color'][] = $v ; 
	}
	$list = sql_fetch_rows("select * from grp_price where `type` = 0 order by min") ;
	$arr['set_price'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_PRICE_RANGE($v ,$a['id'] , $a['min'] , $a['max']) ;
		$arr['set_price'][] = $v ; 
	}
	
	// 单品
	$list = sql_fetch_rows("select * from grp_class order by `t_order` desc") ;
	$arr['item_category'] = array() ;
	_util_setfilter_name($arr['item_category'] , $list) ;
	
	$addwhere = "" ;
	$head = sql_fetch_rows("select * from grp_brand_head order by `order` desc") ;
	if (strlen($city) >= 2){
		$city_filter = trim($city) ;
		$brand_lists = array() ;
		if ($city_filter != "")
			$brand_lists = sql_fetch_rows("select b.* from grp_brand_city b inner join grp_city c on b.city_id = c.id inner join grp_brand d on b.brand_id = d.id and d.`useing` = 1 where c.`name` = '$city_filter'") ;
		$brands = "" ; $dh = "" ;
		foreach($brand_lists as $b){
			$brands .= $dh.$b['brand_id'] ; $dh = "," ;
		}
		//if ($brands != "")
		{
			$other_list = sql_fetch_rows("SELECT * from grp_brand a where  a.`useing` = 1 and  a.id not in (select e.brand_id from grp_brand_city e )") ;
			foreach($other_list as $b){
				$brands .= $dh.$b['id'] ; $dh = "," ;
			}

			$addwhere .= " and `id` in ($brands) " ;	
		}		
		
	}
	
	$list = sql_fetch_rows("select * from grp_brand where `useing` = 1 $addwhere order by `order`") ;
	$arr['item_brand'] = array() ;
	foreach ($head as $a){
		$v = array() ;
		util_set_FILTER_ITEM($v , intval($a['id']) * 10000 , $a['name']) ;
		$arr['item_brand'][] = $v ;
	}
	foreach ($arr['item_brand'] as &$bb){
		$bb['items'] = array() ;		
		foreach ($list as $a){
			if (intval($a['head_grp']) * 10000 == intval($bb['id'])){
				$v = array() ;
				util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
				$bb['items'][] = $v ; 
			}
		}
	}
	
	$list = sql_fetch_rows("select * from grp_style order by `order` desc") ;
	$arr['item_style'] = array() ;
	_util_setfilter_name($arr['item_style'] , $list) ;
	
	$list = sql_fetch_rows("select * from grp_color") ;
	$arr['item_color'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
		$arr['item_color'][] = $v ;
	}
	$list = sql_fetch_rows("select * from grp_price where `type` = 1 order by min") ;
	$arr['item_price'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_PRICE_RANGE($v , $a['id'] , $a['min'] , $a['max']) ;
		$arr['item_price'][] = $v ; 
	}	
	
	// 秀家
	$arr['show_user'] = array() ;
	$v = array() ;	util_set_FILTER_ITEM($v , 1 , "设计师") ;		$arr['show_user'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 2 , "JAJAHOME") ;		$arr['show_user'][] = $v ;
			
	$arr['show_type'] = array() ;
	$v = array() ;	util_set_FILTER_ITEM($v , 1 , "文章") ;		$arr['show_type'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 2 , "比赛") ;		$arr['show_type'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 3 , "原创设计") ;	$arr['show_type'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 4 , "活动") ;		$arr['show_type'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 5 , "美图") ;		$arr['show_type'][] = $v ;
	
	$arr['show_time'] = array() ;
	$v = array() ;	util_set_FILTER_ITEM($v , 1 , "最近1个月") ;		$arr['show_time'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 2 , "最近2个月") ;		$arr['show_time'][] = $v ;
	$v = array() ;	util_set_FILTER_ITEM($v , 3 , "最近3个月") ;		$arr['show_time'][] = $v ;
	
	/*
	$list = sql_fetch_rows("select * from grp_space where useing = 1") ;	// 秀家空间列表
	$arr['show_room'] = array() ;
	_util_setfilter_name($arr['show_room'] , $list) ;
		
	$list = sql_fetch_rows("select * from grp_style order by `order` desc") ;		//秀家风格列表
	$arr['show_style'] = array() ;
	_util_setfilter_name($arr['show_style'] , $list) ;
	
	$list = sql_fetch_rows("select * from grp_color") ;		//秀家色系列表
	$arr['show_color'] = array() ;
	foreach ($list as $a){
		$v = array() ;
		util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
		$arr['show_color'][] = $v ;
	}	
	*/
}
function _util_setfilter_name(&$arr , $list)
{
	$list_now = array() ;	
	foreach ($list as $a){
		if (intval($a['father']) == 0){
			$v = array() ;
			util_set_FILTER_ITEM($v , $a['id'] , $a['name']) ;
			$arr[] = $v ;
		}else{
			$list_now[] = $a ;
		}
	}
	util_set_FILTER_ITEM_all($arr , $list_now) ;
}

function util_set_MESSAGE(&$arr , $item)
{
	$arr['id'] 		= $item['id'] ;
	$arr['title'] 	= $item['title'] ;
	$arr['icon']	= array() ;
	util_set_IMAGE_one($arr['icon'] , $item['icon']) ;
	$arr['content'] = _util_sethtml_content($item['content']) ;
	$arr['type'] 	= $item['type'] ;
	$arr['time'] 	= date("Y/m/d H:i:s",$item['create_time']) ;
	$arr['is_read'] = $item['read'] ;		
}


function _util_sethtml_content($content)
{
	$findstr = "/gjj/backend/web/" ;
	$repstr = $GLOBALS['URL_IMG_HEAD'] ;
	$rnt = "<!DOCTYPE HTML>
			<html>
				<head>
					<meta name='viewport' content='width=320, initial-scale=1'>
					<style type='text/css'>
						body{color:555;}
						#body{margin:0;padding:0 10px 0 10px;font-weight:300;}
						img{max-width:100%;margin:auto;display:block;}
						.set_show_title{font-size:24px;font-weight:300;margin-top:10px;margin-bottom:12px;}
						.set_show_time_editor{color:#999999;font-size:17px;font-weight:300;margin-bottom:28px;}
					</style>
				</head>
				<body id='body'>".$content."</body>
			</html>" ;
	return str_replace($findstr, $repstr , $rnt) ;
}

function util_set_ADDRESS(&$arr , $item)
{
	$arr['id'] 				= $item['id'] ;		//   数值 | 编号
    $arr['area'] 			= $item['area'] ;	//  字符串 | 省市城市数据
    $arr['detail_address'] 	= $item['address'] ;//  字符串 | 详细地址
    $arr['postcode'] 		= $item['postcode'];//  字符串 | 邮编
    $arr['name'] 			= $item['name'] ;	//  字符串 | 收货人姓名
    $arr['mobile'] 			= $item['mobile'] ;	//  字符串 | 收货人联系手机号码
    $arr['tel'] 			= $item['tel'] ;	//  字符串 | 电话
    $arr['type'] 			= $item['type'] ;	// 数值 | 类型  ***0默认收货地址 ，只能有一个
}

function util_set_AREA(&$arr , $item)
{
	$s_id = intval(intval($item['id']) / 10000) ;
	$m_id = intval(intval($item['id']) % 10000) ;
	$s_name= $item['name'] ;

	foreach ($arr as &$s){
		if (intval(intval($s['id']) / 10000) == $s_id){
			$s['city'][] = $s_name ;
			return ;
		}
	}
	
	$a = array() ;
	$a['id'] = $item['id'] ;
	$a['province'] = $s_name ;
	$a['city'] = array() ;
	$arr[] = $a ;	
}

function _util_find_and_add_city_id(&$arr , &$citys , $id , &$rnt)
{
	$v = null ;
	foreach ($arr as &$a){
		if (intval($a['id']) == intval($id)){
			$v = &$a ;
		}
	}
	
	if ($v == null){
		$v = array() ;
		util_set_FILTER_ITEM($v , $citys[$id]['id'] , $citys[$id]['name']) ;
		$v['items'] = array() ;	
		$arr[] = &$v ;
	}
	$rnt[1] = &$v ;
}
function util_set_CITYLIST(&$arr , $citys , $item)
{
	$city_id = intval($item['city_id']) ;
	$s_id = intval(intval($item['city_id']) / 10000) * 10000 ;
	
	$rt=array() ;
	_util_find_and_add_city_id($arr , $citys , $s_id , $rt) ;
	$v = &$rt[1] ;
		
	if ($city_id != $s_id){
		$v = &$v['items'] ;

		$s_id = intval(intval($item['city_id']) / 100) * 100 ;
		_util_find_and_add_city_id($v , $citys , $s_id , $rt) ;
		$v1 = &$rt[1] ;
		
		if ($city_id != $s_id){	
			$v1 = &$v1['items'] ;
			
			$s_id = $city_id ;
			_util_find_and_add_city_id($v1 , $citys , $s_id) ;
		}
	}
}

function util_set_BULIDING(&$arr , $item)
{
	$arr['id'] = $item['id'] ;
	$arr['name'] = $item['name'] ;
}
function util_set_HOUSETYPE(&$arr , $item)
{
	$arr['id'] = $item['id'] ;
	$arr['title'] = $item['title'] ;
	$arr['image'] = array() ;
	util_set_IMAGE($arr['image'] , $item) ;	
}
function util_set_INVITE(&$arr , $item)
{
	$arr['phone'] = $item['tel'] ;	
	$arr['time'] = $item['time_str'] ;
	$arr['phone'] = substr($arr['phone'], 0 , 3) . "xxxx" . substr($arr['phone'] , strlen($arr['phone']) - 4) ; ;
	
	$uid = $item['id'] ;
	$totle = sql_fetch_one("select * from `user_invite_totle` where `uid` = '$uid'") ;
	if (is_array($totle)){
		$arr['pay_amount'] 		=  intval($totle['my_payamounts']) ;	// （该一级好友总消费金额）
    	$arr['one_friends'] 	=  intval($totle['one_friends']) ;		// （该二级好友人数）（即为该phone对象的一级好友总人数）
    	$arr['one_payAmounts'] 	=  intval($totle['one_payamounts']) ;	// （二级好友总消费金额）（即为该phone对象的一级好友总消费
	}else{
		$arr['pay_amount'] 		= 0 ;
    	$arr['one_friends'] 	= 0 ;
    	$arr['one_payAmounts'] 	= 0 ;
	}
}
function util_set_INVITE_TOTAL(&$arr , $user_id)
{
	$arr['my_payAmounts'] = 0 ;		// 我的消费总金额
	$arr['one_friends'] =  0 ;		// | 数值 | 一级好友邀请人数
    $arr['one_payAmounts'] =  0 ;	//  | 数值 | 一级好友总消费金额
    $arr['two_friends'] =  0 ;		//  | 数值 | 二级好友邀请人数
    $arr['two_payAmounts'] =  0 ;	//  | 数值 | 二级好友总消费金额
    
    $totle = sql_fetch_one("select * from `user_invite_totle` where `uid` = '$user_id'") ;
	if (is_array($totle)){
		$arr['my_payAmounts'] 	=  intval($totle['my_payamounts']) ;	// 我的消费总金额
		$arr['one_friends'] 	=  intval($totle['one_friends']) ;		// | 数值 | 一级好友邀请人数
    	$arr['one_payAmounts'] 	=  intval($totle['one_payamounts']) ;	//  | 数值 | 一级好友总消费金额
    	$arr['two_friends'] 	=  intval($totle['two_friends']) ;		//  | 数值 | 二级好友邀请人数
    	$arr['two_payAmounts'] 	=  intval($totle['two_payamounts']) ;	//  | 数值 | 二级好友总消费金额
	}
}
function util_set_ORDER(&$arr , $item)
{
	$arr['order_id'] 	= $item['trade_no'] ;
	$arr['price'] 		= $item['price_total'] ;
	if (intval($arr['price']) == 0)		$arr['price'] = $item['price_modify'] ;
	// 0 - 待处理(未付款) ,  1 - 已处理(已付款) , 2 - 订单取消(交易关闭)  3 交易完成 , 4 退款中   5 退款成功(交易关闭)
	$arr['paymode']		= array() ;
	$arr['paymode'][] 	= 1 ;		// 微信支付
	$arr['paymode'][] 	= 2 ;		// 支付宝支付
	//$arr['paymode'][] 	= 3 ;		// 银联支付
	
	// 多次付款,付款未完成状态,状态返回 8 ;
	if ($item['trade_state'] == 1 && $item['price_state'] == '11')
	{
		$item['trade_state'] = 8 ;
	}
	$arr['status'] 		= $item['trade_state'] ;

	// 地址
	$arr['adress'] = array() ;
	$addlist = explode(";" , $item['address']) ;
	if (count($addlist) >= 1)	$arr['adress']['detail_address'] = $addlist[0] ;	// 详细地址
	if (count($addlist) >= 2)	$arr['adress']['name'] = $addlist[1] ;				// 收货人姓名
	if (count($addlist) >= 3)	$arr['adress']['mobile'] = $addlist[2] ;			// 手机号
	if (count($addlist) >= 4)	$arr['adress']['tel'] = $addlist[3] ;				// 电话
	if (count($addlist) >= 5)	$arr['adress']['postcode'] = $addlist[4] ;			// 邮编
	
	// 订单数组
	$arr['order_list'] = array() ;
	$arr['sales_service'] = util_set_ORDER_addlist($arr['order_list'] , $item['content']) ;
}
function util_set_ORDER_SIMPLE(&$arr , $item)
{
	$arr['order_id'] 	= $item['trade_no'] ;
	$arr['price'] 		= $item['price_total'] ;
	if (intval($arr['price']) == 0)		$arr['price'] = $item['price_modify'] ;
	// 0 - 待处理(未付款) ,  1 - 已处理(已付款) , 2 - 订单取消(交易关闭)  3 交易完成 , 4 退款中   5 退款成功(交易关闭)

	// 多次付款,付款未完成状态,状态返回 8 ;
	if ($item['trade_state'] == 1 && $item['price_state'] == '11')
	{
		$item['trade_state'] = 8 ;
	}
	$arr['status'] 		= $item['trade_state'] ;
	
	
	// 订单数组
	$arr['order_list'] = array() ;	
	util_set_ORDER_addlist($arr['order_list'] , $item['content']) ;
}

// 把json数据按照要求转换到列表中
function util_set_ORDER_addlist(&$arr, $json_content , $source_json = false)
{
	$content = json_decode($json_content) ;
	$rnt_sales_service = 0 ;
	
	// 单品数据
	$list = get_jsonValue($content , "singleItem") ;
	if (is_array($list)) foreach($list as $it){
		$items = get_jsonValue($it,"items") ;		
		$info = get_jsonValue($it,"info") ;
		foreach($items as $it){
			$n = array() ;
			util_set_ORDER_ITEM($n , $it , $source_json) ;
			$arr[] = $n ;
		}
		if ($info != "") 	$ss = get_jsonValue($info,"sales_service") ;	else $ss = "" ;
		if ($ss != "")	$rnt_sales_service = intval($ss) ;
	}
	// 套装数据
	$list = get_jsonValue($content , "tz") ;
	if (is_array($list)) foreach($list as $it){
		$items = get_jsonValue($it,"items") ;
		$info = get_jsonValue($it,"info") ;
		foreach($items as $it){
			$n = array() ;
			util_set_ORDER_ITEM($n , $it , $source_json) ;
			$cc = intval(get_jsonValue($info,"count") ) ;
			if ($cc > 1 && $n['number'] > 0){
				$n['number'] = $n['number'] * $cc ;
			}
			$arr[] = $n ;
		}
		if ($info != "") 	$ss = get_jsonValue($info,"sales_service") ;	else $ss = "" ;
		if ($ss != "")	$rnt_sales_service = intval($ss) ;
	}
	// 优材宝套餐包字段
	$list = get_jsonValue($content , "YCBPackage") ;
	if (is_array($list)) foreach($list as $it){
		$items = get_jsonValue($it,"items") ;
		$info = get_jsonValue($it,"info") ;
		foreach($items as $it){
			$n = array() ;
			util_set_ORDER_ITEM($n , $it , $source_json) ;
			$cc = intval(get_jsonValue($info,"count") ) ;
			if ($cc > 1 && $n['number'] > 0){
				$n['number'] = $n['number'] * $cc ;
			}
			$arr[] = $n ;
		}
		if ($info != "") 	$ss = get_jsonValue($info,"sales_service") ;	else $ss = "" ;
		if ($ss != "")	$rnt_sales_service = intval($ss) ;
	}
	// 过夹夹套餐包字段
	$list = get_jsonValue($content , "GJJPackage") ;
	if (is_array($list)) foreach($list as $it){
		$items = get_jsonValue($it,"items") ;
		$info = get_jsonValue($it,"info") ;
		foreach($items as $it){
			$n = array() ;
			util_set_ORDER_ITEM($n , $it , $source_json) ;
			$cc = intval(get_jsonValue($info,"count") ) ;
			if ($cc > 1 && $n['number'] > 0){
				$n['number'] = $n['number'] * $cc ;
			}
			$arr[] = $n ;
		}
		if ($info != "") 	$ss = get_jsonValue($info,"sales_service") ;	else $ss = "" ;
		if ($ss != "")	$rnt_sales_service = intval($ss) ;
	}
	return $rnt_sales_service ;
}

function util_set_ORDER_ITEM(&$arr , $json_item , $source_json = false)
{
	$item_id = get_jsonValue($json_item,"item_id") ;
	$item_name = get_jsonValue($json_item,"name") ;
	$count = get_jsonValue($json_item,"count") ;
	$price_id = get_jsonValue($json_item,"price_id") ;
	
	$priceitem = sql_fetch_one("select * from sys_item_prices where `id` = '$price_id'") ;
	$fid = 0 ; $mid = 0 ;
	if (is_array($priceitem) && array_key_exists("fabric_id", $priceitem)){
		$fid = $priceitem['fabric_id'] ;
		$mid = $priceitem['material_id'] ;
	}

	// 面料材质等
	$arr['desc'] = "" ; $dh = "" ;
	$sku = get_jsonValue($json_item,"sku") ;
	$sku_fab = get_jsonValue($sku,"fab") ;
	if (strlen($sku_fab) > 0)	{	$arr['desc'] .= $dh.$sku_fab ;	}
	$dh = "," ;
	
	$sku_mat = get_jsonValue($sku,"mat") ;
	if (strlen($sku_mat) > 0)	{	$arr['desc'] .= $dh.$sku_mat ;	}
	else	$arr['desc'] .= $dh ;
	$sku_color = get_jsonValue($sku,"颜色") ;
	if (strlen($sku_color) > 0)	{	$arr['desc'] .= $dh.$sku_color ;}	
	else	$arr['desc'] .= $dh ;
	$sku_size = get_jsonValue($sku,"size") ;
	if (strlen($sku_size) > 0)	{	$arr['desc'] .= $dh.$sku_size ;	}	
	else	$arr['desc'] .= $dh ;
//	$sku_light = get_jsonValue($sku,"光源") ;
//	if (strlen($sku_light) > 0)	{	$arr['desc'] .= $dh.$sku_light ;}	
//	else	$arr['desc'] .= $dh ;
	// 加上价格
	$final_price = doubleval(get_jsonValue($json_item,"final_price")) ;
	$arr['desc'] .= "\n价格: ¥".$final_price ;
	
	$mm = sql_fetch_one("select * from sys_item where `id` = '$item_id'") ;
	$arr['number'] = $count ;	
	$arr['name'] = $item_name ;
	
	$pv = sql_fetch_one("select * from base_sys_preview where `item_id` = '$item_id' and `fid` = '$fid' and `mid` = '$mid'") ;	
	$arr['image'] = array() ;
	if (is_array($pv) && array_key_exists("item_id", $pv)){
		util_set_IMAGE($arr['image'] , $pv) ;
	}else{
		util_set_IMAGE($arr['image'] , $mm) ;
	}
	
	if ($source_json == true){
		$arr['source_json'] = json_encode($json_item,JSON_UNESCAPED_UNICODE);//json_encode_utf8($json_item) ;	//json_encode
		$arr['item_id'] = $item_id ;
	}
}

// 物流运单号详细信息
function util_set_WAYBILL_NUMBER(&$arr , $item_list)
{
	$arr['waybill_number'] = "" ;
	$arr['order_list'] = array() ;
	
	foreach($item_list as $item){
		$arr['waybill_number'] = $item['logistics_trade'] ;
		
		$content = json_decode($item['item_memo']) ;
		$aa = array() ;
		util_set_ORDER_ITEM($aa , $content) ;
		$arr['order_list'][] = $aa ;
	}
}
// 物流跟踪信息
function util_set_LOGISTICS_TRACKING(&$arr , $dataitem)
{
	$arr['context'] = $dataitem['context'] ;
	$arr['time']	= $dataitem['ftime'] ;
}
// 物流信息
function util_set_LOGISTICS_STATUS(&$arr , $jsonarray , $kdname='')
{
	$arr['state'] = $jsonarray['state'] ;
	$arr['company'] = $kdname==''?$jsonarray['com']:$kdname ;
	$arr['logisticsId'] = $jsonarray['nu'] ;
	
	$arr['logisticsArray'] = array() ;
	foreach($jsonarray['data'] as $ar){
		$a = array() ;
		util_set_LOGISTICS_TRACKING($a , $ar) ;
		$arr['logisticsArray'][] = $a ;
	}
}

// 记录用户足迹
function _util_save_user_log($uid , $title , $action , $actionid , $url="")
{
	if (intval($uid) <= 0)		return ;
	if ($action == "item" || $action == "set" || $action == "show"){
		sql_insert("INSERT INTO `log_user_footprint` (`uid`, `calltime`, `title`, `action` , `action_id` , `url`) " .
				"VALUES ('$uid', unix_timestamp() , '$title', '$action', '$actionid' , '$url') ") ;
	}	
}
// 用户足迹 -- 用户访问记录
function util_set_LOG(&$arr , $log)
{
	$arr['id'] = $log['id'] ;			// 数值 | id
	$arr['time'] = $log['calltime'] ; 	// | 数值 | 时间类型
	$arr['title'] = $log['title'] ; 	//  | 字符串 | 名称
	$arr['image'] = array() ;	 //  | `IMAGE` 对象 | 预览图
	$arr['url'] = $log['url'] ; 		//  | 字符串 | 网址
	$arr['action'] = $log['action'] ; //  | 字符串 | 操作类型
	$arr['action_id'] = $log['action_id'] ; //  | 数值 | 操作参数

	if ($arr['action'] == 'item'){
		$item_id = $arr['action_id'] ;		
		$item = sql_fetch_one("select * from `sys_item` where `id` = '$item_id'") ;
		if (is_array($item) == false) 	return ;	
		util_set_IMAGE($arr['image'] , $item) ;

		$arr['price'] 	= $item['price_base'] ;	
		util_get_item_price($arr , $item) ;

		/*
		$preview		= array() ;
		util_set_ITEM_PREVIEW_lists($arr['preview'] , $item) ;
		if (count($preview) == 0){
			$a = array() ;
			util_set_default_ITEM_PREVIEW($a , 0 , 0 , $item) ;
			$preview[] = $a ;
			$arr['image'] = $a['set_image'] ;
		}
		
		foreach($preview as $pre){
			if ($pre['is_default'] == 1){
				$arr['image'] = $pre['set_image'] ;
				break ;
			}
		}*/
	}elseif($arr['action'] == 'set'){		
		$set_id = $arr['action_id'] ;		
		$item = sql_fetch_one("select * from taozhuang where `sid` = '$set_id'") ;	
		util_set_IMAGE($arr['image'] , $item) ;
		
//		$tzren = array() ;
//		util_set_SET($tzren , $item , $log['uid']) ;
		
		$arr['price'] = $item['price_base'] ;
		
	}elseif($arr['action'] == 'show'){
		$show_id = $arr['action_id'] ;		
		$item = sql_fetch_one("select * from `show` where `id` = '$show_id'") ;
		util_set_IMAGE($arr['image'] , $item) ;
		
		$arr['price'] = 0 ;
	}
}

function util_set_REBATE_DETAIL(&$arr , $item)
{
	if ($item['ty'] == -5){
//		$arr['id'] = "" ;
//		$arr['number'] = 0 ;
//		$arr['image'] = array();
//		$arr['order_name'] = "提取现金";
//		$arr['level'] = 0 ;

		$arr['variable_amount'] = - intval($item['at']) ;
		$arr['type'] = 1 ;
		$arr['time'] = $item['tm'] ;
	}else{
//		$trade = sql_fetch_one("select * from pay_trade where `trade_no` = '{$item['no']}'") ;
//		$arr['id'] = $item['no'] ;					//   字符串 | 订单号
//	    $arr['number'] = $trade['number'];		//   数值 | 订单单品数量	
//		$arr['image'] = array();					//   IMAGE` 对象 | 订单的预览图 （套装为第一个单品的图）     
//	    $arr['order_name'] = "";					//  订单名称 , 取里边商品第一个名称
//	    $aa = array() ;	
//		util_set_ORDER_addlist($aa , $trade['content']) ;
//	    if (count($aa) > 0){
//	    	$add_name = "" ;
//		    if ($arr['number'] > 1){
//		    	$add_name = "组合订单：" ;
//		    }
//		    
//	    	$arr['order_name'] = $add_name.$aa[0]['name'] ;
//	    	$arr['image'] = $aa[0]['image'] ;
//	    }	
//	    $arr['level'] = $item['ty'] ;
	    
	    $arr['variable_amount'] = intval($item['at']) ;
	    $arr['type'] = 0 ;
	    $arr['time'] = $item['tm'] ;
	}
}
function util_set_INTEGRAL_DETAIL(&$arr , $item)
{
	$arr['variable_amount'] = $item['score'];
    $arr['type'] = $item['state'] ;
    $arr['time'] = $item['time'] ;
}
// 用户评论
function util_set_COMMENT(&$arr , $item)
{
	$arr['id']				= $item['id'] ;		
	$arr['content_type'] 	= $item['content_type'] ;			//  | 字符串 | 评论内容类型 等同 `action`
	$arr['content_id'] 	= intval($item['content_id']) ;	//  | 数值 | 评论内容id
	$arr['user'] = array() ;			
	util_set_USER($arr['user'] , $item['send_uid']) ;			//  | `USER` | 发表用户
	$arr['time'] 			= intval($item['send_time']) ;		//  | 数值 | 时间类型
	$arr['like'] 			= intval($item['like']) ;			//  | 数值 | 点赞数
	$arr['comment'] 		= $item['comment'] ;				//  | 字符串 | 评论内容
	
	$arr['replys'] 		= array() ;
	
	$comment_id = intval($item['comment_id']) ;	
	if ($comment_id > 0){
		$list = sql_fetch_rows("select * from `user_comment` where comment_id = $comment_id order by send_time desc") ;
		if (is_null($list) == false && array_key_exists('id' , $list)){
			foreach($list as $t){
				$a = array() ;
				util_set_COMMENT($a , $t) ;
				$arr['replys'][] = $a ;
			}
		}
	}
}

?>