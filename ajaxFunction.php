<?php 
require_once('../../../wp-config.php');
$g_criteria = ""; 
$g_criteria1 = ""; 
$g_criteria2 = ""; 
$g_criteria3 = "";
 
if(isset($_REQUEST['do'])) {
	$g_criteria1 = trim($_REQUEST['do']);
}

if(isset($_REQUEST['event'])) {
	$g_criteria2 = trim($_REQUEST['event']);
}


switch($g_criteria1)
{
	
	case "statuschange": 
		updatePaymentStatus();
		insert_refferal_commision1();
                echo !empty($_REQUEST['status'])?$_REQUEST['name']:'';
	break;
	
}


function updatePaymentStatus() 
{ 
	global $wpdb;
	if(isset($_REQUEST['userId']) && isset($_REQUEST['status']))
	{
		$table_prefix = mlm_core_get_table_prefix();
		$mlm_general_settings=get_option('wp_mlm_general_settings');
		$product_price=($_REQUEST['status']==1)?$mlm_general_settings['product_price']:'0';		
		$sql = "UPDATE {$table_prefix}mlm_users 
				      SET payment_status = '".$_REQUEST['status']."' , product_price='".$product_price."'
				      WHERE user_id = '".$_REQUEST['userId']."'";
			
		$rs = $wpdb->query($sql);
		if(!$rs){
			 _e("<span class='error' style='color:red'>Updating Fail</span>");
		} 		 
		 
	}
	
}

function insert_refferal_commision1()
{ 
	global $wpdb;
	$date = date("Y-m-d H:i:s");
	$child_ids='';
	$mlm_payout = get_option('wp_mlm_payout_settings');
	$refferal_amount=$mlm_payout['referral_commission_amount'];
	$table_prefix = mlm_core_get_table_prefix();
	
	
	
	/*Insert a refferal commision in wp_mlm_commission when user is not special*/
		/*if($_REQUEST['special']=='0' && $_REQUEST['status']=='1')
		{
			
			// to get parent id and chield id
			$user_id=$_REQUEST['userId'];
			$ids=$wpdb->get_row("SELECT * FROM {$table_prefix}mlm_users WHERE user_id=$user_id");
			$child=$ids->username;
			$sponsor_key=$ids->sponsor_key;
			$parent= $wpdb->get_row("SELECT id FROM {$table_prefix}mlm_users WHERE user_key='".$sponsor_key."'");
			$parent_id=$parent->id;
			$insert = $wpdb->query("INSERT INTO {$table_prefix}mlm_commission(id, date_notified, parent_id, child_ids, amount,refferal_id) 
							VALUES(NULL, '".$date."', '".$parent_id."', '".$child."', '".$refferal_amount."','".$parent_id."')");
		}*/
																							
		if(isset($_REQUEST['userId']) && $_REQUEST['status'] ==1)
		{
			$table_prefix = mlm_core_get_table_prefix();
			$user_id=$_REQUEST['userId'];
			$row=$wpdb->get_row("SELECT * FROM {$table_prefix}mlm_users WHERE user_id=$user_id");
			$sponsor_key=$row->sponsor_key;
			$child_id = $row->id;
			if($sponsor_key !=0) {
			$sponsor= $wpdb->get_row("SELECT id FROM {$table_prefix}mlm_users WHERE user_key='".$sponsor_key."'"); 
			$sponsor_id=$sponsor->id;
			$sql = "INSERT INTO {$table_prefix}mlm_referral_commission SET date_notified ='$date',sponsor_id='$sponsor_id',child_id='$child_id',amount='$refferal_amount',payout_id='0' ON DUPLICATE KEY UPDATE child_id='$child_id'";
			$rs = $wpdb->query($sql);
			//if($rs){_e("<span class='error' style='color:red'>Inserting  Fail</span>");}
			 
		}																					
	}
}


?>
