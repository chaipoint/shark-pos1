<script src="js/cp.swipe.js" type="text/javascript" charset="UTF-8"></script>
<?php
if($store_id && $store_id!=''){

?>
	<div id="store_open_outer" class='clear right clearfix'>
		<a href="javascript:swipeInDialog();" class="button right" rel="swipe_in">Swipe In</a>
	</div>
	
<?php
}

$selectQuery = "SELECT sa.id,sm.code,sm.name,tm.name as title,sm.phone_1 phone,sa.in_time,sa.out_time,sm.id as staff_id,sa.pocket_cash pocket_cash_in ,sa.pocket_cash_out  pocket_cash_out, 
	case  when sa.out_time is null OR sa.out_time='' then 'Swipe Out' else '' end swipe_out,
	case  when sa.out_time is null OR sa.out_time='' then '' else TIMEDIFF(sa.out_time , sa.in_time) end time_spent

	from staff_attendance sa,staff_master sm,title_master tm 
	where tm.active='Y' and sm.active='Y' 
		and sm.title_id = tm.id and sa.staff_id = sm.id 
		 and date(in_time) =  '$currentDate' 
		 and sa.store_id = '$store_id'";
//	echo 	$selectQuery;
$result = $db->func_query($selectQuery);


$columns = Array(
		//"id"=>  Array("label"=>"ID","type"=>"TEXT", "th-class"=>"no-show"),
		"name"=>  Array("label"=>"Name", "type"=>"TEXT", "th-class"=>"no-sort"),
		"title"=>  Array("label"=>"Title", "type"=>"TEXT", "th-class"=>"no-sort"),
		"phone"=> Array("label"=>"Phone", "type"=>"TEXT", "th-class"=>""),
		"in_time"=> Array("label"=>"In Time", "type"=>"TEXT", "th-class"=>"no-sort", "td-class"=>"number" ),
		"out_time"=> Array("label"=>"Out Time", "type"=>"TEXT", "th-class"=>"no-sort", "td-class"=>"number" ),
		"time_spent"=>  Array("label"=>"Time Spent", "type"=>"TEXT", "th-class"=>"no-sort"),
		"pocket_cash_in"=> Array("label"=>"In Cash", "type"=>"TEXT", "th-class"=>"no-sort", "td-class"=>"number qty" ),
		"pocket_cash_out"=> Array("label"=>"Out Cash", "type"=>"TEXT", "th-class"=>"no-sort", "td-class"=>"number qty" ),
		"swipe_out"=> Array("label"=>"Swipe Out", "tag"=>"a", "type"=>"link", "th-class"=>"no-sort ", "attr" => "role='swipe-out' class='swip_out_grid'" ),
);

$html = createDataTable($result,$columns );

echo $html;
		
?>


		