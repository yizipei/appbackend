<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.3&key=da0fa0b9611e6377d9f562d0b0086afc&plugin=AMap.CitySearch"></script>
<title>线下店铺地址</title>
<style type="text/css">
body,html,#container{
  height: 100%;
  margin: 0px;
  background-color:#FFF;
  font: 12px Helvetica, 'Hiragino Sans GB', 'Microsoft Yahei', '微软雅黑', Arial;
}
.info-title{
  color: white;
  font-size: 14px;
  background-color: rgba(0,155,255,0.8);
  line-height: 26px;
  padding: 0px 0 0 6px;
  font-weight: lighter;
  letter-spacing: 1px
}
.info-content{
  padding: 4px;
  color: #666666;
  line-height: 23px;
}
.info-content img{
  float: left;
  margin: 3px;
}
#tip{
	background-color: #fff;
    padding-left: 10px;
    padding-right: 10px;
    position: absolute;
    font-size: 12px;
    right: 10px;
    top: 20px;
    border-radius: 3px;
    border: 1px solid #ccc;
    line-height: 30px;
	z-index:2;
}
.amap-touch-toolbar .amap-zoomcontrol{
	bottom:-35px;
}
.amap-geo{
	bottom:10px;
}
a{
	color:#555;
}
a:hover{
	color:#555;
}
a:visited{
	color:#555;
}
.logo{
	text-align:center;
	margin-top:10px;
	height: 36%;
}
.img-none{
	height: 13rem;
	margin: 1rem 0 1rem;
}
.img-have{
	height:3.6rem;
	margin-top: 0;
}
table{
	width:auto;
	margin:auto;
	line-height:23px;
	color:#333;
	text-align: left;
}
th{
	height: 1.2rem;
	text-align: center;
}
th span{
	width:100%;
	text-align:center;
	font-size:15px;
	font-weight:300;
}
.td1{
	height:0.5rem;
	width:3rem;
	vertical-align:text-top;
	text-align: right;
}
.td1,.td2{
	font-size:0.7rem;
}
#addressArea{
	display:none;
}
.map-box {
	position: relative;
	height: 60%;
	margin-top: 4%;
}
.table-on-top {
	text-align: left;
}
span.title {
    font-weight: 500;
    font-size: 1.08rem;
}
</style>
</head>
<body>

<?php
require_once("./config/app_webview_config.php");
$arr=array();
$i=0;
$str="";
if(isset($_GET['province'])){
	$str=$str." AND A.province = '".$_GET['province']."'";
	$str2=$str2." AND B.province = '".$_GET['province']."'";
	if (isset($_GET['city'])) {
		$str=$str." AND A.city = '".$_GET['city']."'";
		$str2=$str2." AND B.city = '".$_GET['city']."'";
	}
}elseif (isset($_GET['city'])) {
	$str=$str." AND A.city = '".$_GET['city']."'";
	$str2=$str2." AND B.city = '".$_GET['city']."'";
}
if($str != ''){
$query="SELECT A.sales_name,A.province,A.city,A.longitude,A.latitude,A.address,A.tel,A.person 
        FROM grp_shop_position A,sys_item 
        WHERE  (
            		use_flag = 1
            		AND distinguish = 0 
            		AND sys_item.id = ".intval($_GET['id'])."
            		AND A.brand_id = sys_item.brand_id
            		".$str."
            	)
        UNION
    	SELECT
    		B.sales_name,B.province,B.city,B.longitude,B.latitude,B.address,B.tel,B.person
    	FROM
    		grp_shop_position B
    	WHERE
    		(
    			B.use_flag = 1
    			AND B.distinguish = 1
    			".$str2."
    		)";
     
$result=mysqli_query($conn,$query);

while($row=mysqli_fetch_array($result))
{
	$arr[$i++]=array(
		"sales_name"=>$row["sales_name"],
		"province"=>$row["province"],
		"city"=>$row["city"],
		"longitude"=>floatval($row["longitude"]),
		"latitude"=>floatval($row["latitude"]),
		"address"=>$row["address"],
		"tel"=>$row["tel"],
		"person"=>$row["person"]==NULL?-1:$row["person"]
	);
}
echo "<script type='text/javascript'>var data=JSON.parse('".json_encode($arr)."');</script>";
}else {
echo "<script type='text/javascript'>var data=[];</script>";
}
?>
<div class="logo">
	<?php 
    	if(count($arr)==0)
        {
        	echo '<img src="images/logo.png" class="img-none">';
        }else {
            echo '<img src="images/rowlogo.png" class="img-have">';
        }
	?>
	<?php //这里是从数据库获取的地址信息
    	$query = "select * from grp_factory where `id` = 1";
    	$result=mysqli_query($conn,$query);
    	$item=mysqli_fetch_array($result);
    if(count($arr)==0)
    {
    	echo "<table>";
    }else {
        echo "<table class = 'table-on-top'>";
    }
    	echo "	<tr>
    				<th colspan='2'>
    					<span class='title'>".$item['name']."</span>
    				</th>
    			</tr>
    			<tr>
    				<td width='120'  class='td1'>
    					地址：
    				</td>
    				<td class='td2'>";
    				$t=explode("号",$item['addr']);
    	echo		$t[0]."号<br>";
    	echo		$t[1];
    	echo		"</td>
    			</tr>
    			<tr>
    				<td class='td1'>
    					电话：
    				</td>
    				<td  class='td2'>
    					".$item['phone']."
    				</td>
    			</tr>
    			<tr>
    				<td class='td1'>
    					邮件：
    				</td>
    				<td  class='td2'>
    					".$item['email']."
    				</td>
    			</tr>
    		</table>";
    ?>
</div>
<div class="map-box">
    <div id="tip"></div>
    <div id="container" tabindex="0"></div>
    <div id='panel'></div>
    <div id="addressArea"></div>
    
</div>
<script type="text/javascript">
(function (doc, win) {
          var docEl = doc.documentElement,
            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
            recalc = function () {
              var clientWidth = docEl.clientWidth;
              if (!clientWidth) return;
              docEl.style.fontSize = (clientWidth/20) + 'px';
            };

          if (!doc.addEventListener) return;
          win.addEventListener(resizeEvt, recalc, false);
          doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
if(data.length>0)
{
	var current_city;
	var map = new AMap.Map('container',{
		resizeEnable: true,
		zoom: 11,
		center:[data[0]["longitude"], data[0]["latitude"]]
	});
	AMap.plugin(['AMap.ToolBar','AMap.Scale'],function(){
		var toolBar = new AMap.ToolBar();
		var scale = new AMap.Scale();
		map.addControl(toolBar);
		map.addControl(scale);
	});
	AMap.plugin('AMap.AdvancedInfoWindow',function(title,content,tel){
	   infowindow = new AMap.AdvancedInfoWindow({
		offset: new AMap.Pixel(0, -30)
	  });
	})
	function showCityInfo()
	{
		var current_city=<?= isset($_GET['city'])?"'".$_GET['city']."'":0;?>;
		if (current_city!=0) {
			showSellers(current_city);
		}else{
			map.plugin('AMap.Geolocation', function() {
				geolocation = new AMap.Geolocation({
					enableHighAccuracy: true,//是否使用高精度定位，默认:true
			        timeout: 10000,          //超过10秒后停止定位，默认：无穷大
			        maximumAge: 0,           //定位结果缓存0毫秒，默认：0
			        convert: true,           //自动偏移坐标，偏移后的坐标为高德坐标，默认：true
			        showButton: true,        //显示定位按钮，默认：true
			        buttonPosition: 'LB',    //定位按钮停靠位置，默认：'LB'，左下角
			        buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
			        showMarker: true,        //定位成功后在定位到的位置显示点标记，默认：true
			        showCircle: true,        //定位成功后用圆圈表示定位精度范围，默认：true
			        panToLocation: true,     //定位成功后将定位到的位置作为地图中心点，默认：true
			        zoomToAccuracy:false     //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
				});
				map.addControl(geolocation);
				geolocation.getCurrentPosition();
				AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
				AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
			});
			//解析定位结果
			function onComplete(data) {
				/*var str=['定位成功'];
				str.push('经度：' + data.position.getLng());
				str.push('纬度：' + data.position.getLat());
				str.push('精度：' + data.accuracy + ' 米');
				str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));
				document.getElementById('tip').innerHTML = str.join('<br>');*/
			}
			//解析定位错误信息
			function onError(data) {
				document.getElementById('tip').innerHTML = '定位失败';
			}

			var citysearch = new AMap.CitySearch();
			citysearch.getLocalCity(function(status, result) {
				if (status === 'complete' && result.info === 'OK')
				{
					if (result && result.city && result.bounds)
					{
						var cityinfo = result.city;
						var citybounds = result.bounds;
						current_city=cityinfo;
						var t=0;
						for(var i in data)
						{
							if(data[i]["city"]==current_city)
							{
								t=1;
								break;
							}
						}
						if(t){
							//document.getElementById('tip').innerHTML = '您当前所在城市：'+cityinfo;
							map.setBounds(citybounds);
							showSellers(current_city);
						}else{
							location.href="sellers_location.php?id=0";
						}
					}
				}
				else{
					document.getElementById('tip').innerHTML = result.info;
				}
			});
		}
	}
	function showSellers(current_city)
	{
		for(var i in data)
		{
			if(data[i]["city"]==current_city)
			{
				var marker = new AMap.Marker({
					title:data[i]["sales_name"],
					position: [data[i]["longitude"], data[i]["latitude"]]
				});
				marker.title=data[i]["sales_name"];
				marker.content=data[i]["address"];
				marker.tel=data[i]["tel"];
				marker.person=data[i]["person"];
				marker.setMap(map);
				marker.on('click',function(e){
					infowindow.setContent(
						'<div class="info-title">'+e.target.title+'</div><div class="info-content">'+
						'<img src="https://webapi.amap.com/images/amap.jpg">'+
						''+e.target.content+'<br>'+
						''+(e.target.person==-1?'':('联系人:&nbsp;&nbsp;'+e.target.person)+'<br>')+
						'电&nbsp;&nbsp;&nbsp;&nbsp;话:&nbsp;&nbsp;<a target="_blank" href="tel:'+e.target.tel+'">'+e.target.tel+'</a></div>'
					);
					infowindow.open(map,e.target.getPosition());
				});
			}
		}
	}
	showCityInfo();
}
else
{
	document.getElementById("container").style.display="none";
	document.getElementById("tip").style.display="none";
	document.getElementById("panel").style.display="none";
	document.getElementById("addressArea").style.display="block";
}
</script>
</body>
</html>