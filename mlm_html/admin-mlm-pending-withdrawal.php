<?php 
function mlm_withdrawal_request(){
global $table_prefix;
global $wpdb;
global $date_format;
$url = plugins_url();
?>
<div class='wrap'>
	<div id="icon-users" class="icon32"></div><h1><?php _e('Pending User Withdrawals','binary-mlm-pro');?></h1><br />

	<div class="notibar msginfo" style="margin:10px;">
		<a class="close"></a>
		<p><?php _e('Given below is the list of all pending User Withdrawals.','binary-mlm-pro');?></p>
        <p><strong><?php _e('Process','binary-mlm-pro');?></strong> - <?php _e("Input the payment details for the withdrawal. These payment details would also show up on the User's Payout Details Page.",'binary-mlm-pro');?> </p>
        <p><strong><?php _e('Delete','binary-mlm-pro');?></strong> - <?php _e('This would mark the withdrawal as deleted. The user would need to initiate a fresh withdrawal for this payout from his interface.','binary-mlm-pro');?> </p>
	</div>	

</div>
<?php
$sql = "SELECT id, user_id, DATE_FORMAT(`date`,'%d %b %Y') as payoutDate, payout_id, commission_amount,referral_commission_amount, bonus_amount,total_amt,capped_amt,cap_limit, tax, service_charge, DATE_FORMAT(`withdrawal_initiated_date`,'%d %b %Y')withdrawal_initiated_date, withdrawal_initiated_comment FROM {$table_prefix}mlm_payout WHERE withdrawal_initiated= 1 AND `payment_processed`= 0";

		$rs = mysql_query($sql);
		
		 $payoutDetail=array();
		 $html_output="<table border='1' cellspacing='0' cellpadding='5' width='99%' style='border-color:#dadada;'>";
		 $html_output.="<tr><th>".__('Member Username','binary-mlm-pro')."</th><th>".__('Member Email','binary-mlm-pro')."</th><th>".__('Withdrawal Initiate Date','binary-mlm-pro')."</th><th>".__('Withdrawal Comment','binary-mlm-pro')."</th><th>".__('Amount','binary-mlm-pro')."</th><th>".__('Payout Id','binary-mlm-pro')."</th><th>".__('Payout Date','binary-mlm-pro')."</th><th>".__('Action','binary-mlm-pro')."</th></tr>";
		 if(mysql_num_rows($rs)>0)
		 {
		 	while($row = mysql_fetch_array($rs)){
			
			$sql1 = "SELECT {$table_prefix}mlm_users.username AS uname , {$table_prefix}users.user_email AS uemail FROM {$table_prefix}users,{$table_prefix}mlm_users WHERE {$table_prefix}mlm_users.username = {$table_prefix}users.user_login AND {$table_prefix}mlm_users.id = '".$row['user_id']."'"; 
				
			$res1 = mysql_query($sql1);
			$row1 = mysql_fetch_array($res1);		
			
			$payoutDetail['memberId'] = $row['user_id'];
			$payoutDetail['name'] = $row1['uname']; 
			$payoutDetail['email'] = $row1['uemail']; 			
			$payoutDetail['payoutId'] = $row['payout_id']; 
			$payoutDate = date_create($row['payoutDate']);
			$payoutDetail['payoutDate'] = date_format($payoutDate,$date_format);
			$withdrawal_date = date_create($row['withdrawal_initiated_date']);
			$payoutDetail['widate'] = date_format($withdrawal_date,$date_format);
			$payoutDetail['wicomment'] = $row['withdrawal_initiated_comment'];
			$payoutDetail['commamount'] = $row['commission_amount'];
			$payoutDetail['refcommamt'] = $row['referral_commission_amount'];
			$payoutDetail['bonusamount'] = $row['bonus_amount'];
			$payoutDetail['total_amt'] = $row['total_amt'];
			$payoutDetail['capped_amt'] = $row['capped_amt'];
			$payoutDetail['cap_limit'] = $row['cap_limit'];
			$payoutDetail['servicecharges'] = $row['service_charge'];
			$payoutDetail['tax'] = $row['tax'];
			$payoutDetail['netamount'] = number_format($row['capped_amt'], 2);
			
			
			
            $html_output.="<tr><td>".$payoutDetail['name']."</td><td>".$payoutDetail['email']."</td><td>".$payoutDetail['widate']."</td><td>".$payoutDetail['wicomment']."</td><td>".$payoutDetail['netamount']."</td><td>".$payoutDetail['payoutId']."</td><td>".$payoutDetail['payoutDate']."</td>
			<td><form name='withdrawal_process' method='POST' action='".admin_url( 'admin.php' )."?page=admin-mlm-withdrawal-process' id='withdrawal_process'>
			<input type='hidden' name='member_name' value='".$payoutDetail['name']."'>
			<input type='hidden' name='member_id' value='".$payoutDetail['memberId']."'>
			<input type='hidden' name='member_payout_id' value='".$payoutDetail['payoutId']."'>
			<input type='hidden' name='member_email' value='".$payoutDetail['email']."'>
			<input type='hidden' name='withdrawal_amount' value='".$payoutDetail['netamount']."'>
			<input type='submit' value='".__('Process','binary-mlm-pro')."' id='process' name='process-amount'>
			</form>&nbsp;|&nbsp;<a class='ajax-link' id='".$payoutDetail['memberId']."$".$payoutDetail['payoutId']."' href='javascript:void(0);'>".__('Delete','binary-mlm-pro')."</a></td>";
		       
		 }
		 $html_output.="</table>";
		 _e($html_output);
		
		} else { _e("Hooray! Nothing in the pipeline to be processed.",'binary-mlm-pro'); 		}
		?>
		
	<script type="text/javascript">
			jQuery(document).ready(function ($) {
			$(".ajax-link").click( function() {
			var b=$(this).parent().parent();
			var id = $(this).attr('id');
			var ids = id.split("$");
			var dataString = 'wdel_id='+ ids[0]+'&pay_id='+ids[1];
			
			if(confirm("<?php _e('Confirm Delete withdrawal request?','binary-mlm-pro')?>")){
			$.ajax({
			type: "POST",
			url: "<?php _e($url); ?>/binary-mlm-pro/mlm_html/delete_withdrawal.php",
			data: dataString,
			cache: false,
			success: function(e)
			{
				//b.hide();
				window.location.reload(true);
			}
			});
				return false;
			}
			});
			});
		</script>
<?php } ?>
