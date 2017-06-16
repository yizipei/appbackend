<?php

// 检测并声称表 mem_diy_tz_item 的数据 ， 30秒重新生成一次。
function temp_check_and_createtable__mem_diy_tz_item()
{
	$cache_key = "createtable__mem_diy_tz_item" ;
	$ret = intval(mem_getValues($cache_key)) ;
	if ($ret == null || $ret == 0){
		$time_key = "createtable__mem_diy_tz_item2" ;
		$uptime = intval(mem_getValues($cache_key)) ;
		$nowtime = intval(sql_fetch_one_cell("select unix_timestamp()")) ;
		$out_sessond = $GLOBALS['MEM_TABLE_OUTTIME'] ;	// 过期时间
		if ($nowtime - $uptime > $out_sessond){
			mem_setKey($cache_key, 1) ;
			mem_setKey($time_key, $nowtime) ;
		}else{
			return ;
		}
	}else{
		$i = 0 ;
		while($i < 10){
			$ret = intval(mem_getValues($cache_key)) ;
			if ($ret == 0){
				return ;
			}
			sleep(1) ;
			$i += 1 ;
		}
	}
	
	sql_query("delete from mem_diy_tz_item") ;
	sql_query("replace into mem_diy_tz_item select item_id from taozhuang_pos group by item_id") ;
	sql_query("replace into mem_diy_tz_item select item_id from base_sys_preview where img_tz_id > 0 group by img_tz_id") ;
	
	mem_setKey($cache_key, 0) ;
}

function util_set_SET_SIMPLE_diy(&$arr , $item)
{
	$arr['id'] 		= $item['id'] ;
	$arr['name'] 	= $item['name'] ;
	$arr['image'] 	= array() ;
	util_set_IMAGE($arr['image'] , $item) ;
}

function util_set_SET_diy(&$arr , $item)
{
	$arr['id'] 			= $item['id'] ;
	$arr['name'] 		= $item['name'] ;
	$arr['discount_rate'] = 1 ;
	
	$arr['list']			= array() ;	
	// 这么复杂的搜索只是为了返回的值排序用.....
	$listtz = sql_fetch_rows("select t.* from diy_pos t left join sys_item s on t.item_id = s.`id` left join grp_class g on s.class_id = g.`id` where t.`diy_id` = '{$item['id']}' order by g.`t_order` desc , t.`pos` asc");
	foreach($listtz as $tz){
		$v = array() ;		
		util_set_SET_POS_diy($v , $tz) ;
		$arr['list'][] = $v ;
	}
	$arr['comment_list'] = array() ;
	$arr['comment_total'] = array() ;
	
	$arr['related']		= array() ;
	
	$blueprint = array() ;
	util_set_IMAGE_one($blueprint , $item['back_img']) ;	
	$arr['image_blueprint'] = $blueprint['url'] ;
}

function util_set_SET_POS_diy(&$arr , $item , $type = 1)
{
	$arr['id']				= $item['id'] ;
	$arr['zorder']			= $item['pos'] ;
	$arr['parent_zorder']	= $item['father'] ;
	$arr['item_size']		= 1 ;
	$arr['item_info']		= array() ;
	util_set_SET_ITEMINFO_diy($arr['item_info'] , $item , $type) ;
	
	util_diy_set_ex_pos_info($arr['item_info'] , $item['id'] , sql_fetch_one("select * from sys_item where `id` = '{$item['item_id']}'")) ;
		
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
// 套装 中 item 信息 , $type = 1 模板 , 2 mydiy
function util_set_SET_ITEMINFO_diy(&$arr , $item , $type = 1 , $item_id = 0)
{
	if ($item_id == 0)	$item_id = $item['item_id'] ;

	$arr['id'] 				= $item_id ;  
	$arr['scale']			= floatval($item['zoom']) ;
	$arr['flip']				= intval($item['mirror']) ;
	$arr['pos']				= array("x"=>floatval($item['x']) , "y"=>floatval($item['y'])) ;
	$arr['anchor']			= array("x"=>floatval($item['anchor_x']) , "y"=>floatval($item['anchor_y'])) ;
	$test = new test_bk() ;
	
	if ($item_id != $item['item_id']){
		$ones = $test->get_item_default_ts_image($item_id) ;	
		$arr['image_size']		= array("x"=>floatval($ones[2]) , "y"=>floatval($ones[3])) ;
		$arr['image']			= array() ;	
		util_set_IMAGE_one_url($arr['image'] , $ones[1]) ;
	}else{
		$ones = $test->get_diy_pos_for_ts_image($item['id'] , $type) ;	
		$arr['image_size']		= array("x"=>floatval($ones[4]) , "y"=>floatval($ones[5])) ;
		$arr['image']			= array() ;	
		util_set_IMAGE_one_url($arr['image'] , $ones[1]) ;
	}
	$arr['name'] = "" ;

	$it = sql_fetch_one("select * from sys_item where `id` = '{$item_id}'") ;	
	if (is_array($it) && array_key_exists("name", $it)){
		$arr['name']			= $it['name'] ;
		$arr['pay_state'] =  intval($item['pay_state']) ;
		if (intval($it['app_return']) != 1){
			$arr['pay_state'] =  0 ;
		}
		
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
}

// 套装diy中 item 信息(中可更换款式信息)
function util_set_SET_POS_ITEM_diy(&$arr , $posinfo , $item , $uid , $type = 1 , $thenew = false)
{
	$arr['id'] = $posinfo['id'] ;  
	$arr['item'] = array() ;
	util_set_ITEM($arr['item'] , $item , $uid);
	$arr['info'] = array() ;
	util_set_SET_ITEMINFO_diy($arr['info'] , $posinfo , $type , $item['id']);

	if ($type == 1){
		util_diy_set_ex_pos_info($arr['info'] , $posinfo['id'] , $item , $thenew) ;
	}else{
		util_diy_set_ex_pos_info($arr['info'] , $posinfo['pos_id'] , $item , $thenew) ;		
	}
	
	foreach($arr['item']['preview'] as &$vv){
		$vv['is_default'] = 0 ;
		if ($vv['set_image']['url'] == $arr['info']['image']['url']){
			$vv['is_default'] = 1 ;
		}
	}

}

function util_diy_set_ex_pos_info(&$arr , $posid , $iteminfo , $thenew = false)
{
	$one = sql_fetch_one("select * from diy_pos_ex where `pos_id` = '$posid' and `item_id` = {$iteminfo['id']}") ;
	if (is_array($one) && array_key_exists("anchor_x", $one) && $thenew == true){
		$arr['pos']				= array("x"=>floatval($one['x']) , "y"=>floatval($one['y'])) ;
		$arr['anchor']			= array("x"=>floatval($one['anchor_x']) , "y"=>floatval($one['anchor_y'])) ;
		$arr['scale']			= floatval($one['zoom']) ;
		$arr['flip']				= intval($one['mirror']) ;
	}elseif ($iteminfo['anchor_x'] != 0.5 || $iteminfo['anchor_y'] != 0.5){
		$arr['anchor']			= array("x"=>floatval($iteminfo['anchor_x']) , "y"=>floatval($iteminfo['anchor_y'])) ;
	}
}

/**
 * 保存mydiy的时候需要传递的DIY_POS的修改信息
 * 返回模板pos编号 , 透视图编号 , 单品编号
 */
function util_diy_DIY_POS_from_json($value)
{
	$id = get_jsonValue($value , "id") ;				// pos表编号
	$imgurl = get_jsonValue($value , "image_url") ;		// 选中的透视图地址
	$item_id = get_jsonValue($value , "item_id") ;		// 单品编号
	
	$addr = substr($imgurl , strlen($GLOBALS['URL_IMG_HEAD'])) ;  // 图片表中的图片存放位置信息
	$imgid = intval(sql_fetch_one_cell("select `id` from `sys_image` where `url` = '$addr'")) ;
	
	return array($id , $imgid , $item_id) ;
}


function util_set_SET_my_diy(&$arr , $item)
{
	$arr['id'] 			= $item['id'] ;
	$arr['name'] 		= $item['name'] ;
	$arr['discount_rate'] = 1 ;
	
	$arr['list']			= array() ;	
	// 这么复杂的搜索只是为了返回的值排序用.....
	$listtz = sql_fetch_rows("select ut.* , t.`show_name` , t.`fab_name`,t.`mat_name`,t.`pos`,t.`father`,t.`x`,t.`y`,t.`anchor_x`,t.`anchor_y`,t.`rotate`,t.`zoom`,t.`mirror`,t.`pay_state` from user_diy_pos ut inner join diy_pos t on ut.`pos_id` = t.`id` left join sys_item s on t.item_id = s.`id` left join grp_class g on s.class_id = g.`id` where ut.`user_diy_id` = '{$item['id']}' order by g.`t_order` desc , t.`pos` asc");
	foreach($listtz as $tz){
		$v = array() ;		
		util_set_SET_POS_diy($v , $tz , 2) ;
		$arr['list'][] = $v ;
	}
	$arr['comment_list'] = array() ;
	$arr['comment_total'] = array() ;
	
	$arr['related']		= array() ;
	
	$userid = $item[`user_id`]  ;
	$id     = $item['id'] ;
	$img_bk = intval(sql_fetch_one_cell("select d.`back_img` from `user_diy` ud inner join `diy` d on ud.`diy_id` = d.`id` and ud.`user_id` = '$userid' where ud.`id` = '$id'")) ;

	$blueprint = array() ;
	util_set_IMAGE_one($blueprint , $img_bk) ;	
	$arr['image_blueprint'] = $blueprint['url'] ;
}


?>