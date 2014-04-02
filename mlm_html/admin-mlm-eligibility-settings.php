<?php
function mlmEligibility()
{
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	
	//most outer if condition
	if(isset($_POST['mlm_eligibility_settings']))
	{
		$direct_referral = sanitize_text_field( $_POST['direct_referral'] );
		$right_referral = sanitize_text_field( $_POST['right_referral'] );
		$left_referral = sanitize_text_field( $_POST['left_referral'] );
		//$member_in_group=sanitize_text_field( $_POST['member_in_group'] );
		if ( checkInputField($direct_referral) ) 
			$error .= '<br/>'.__("\n Please specify your direct active referrals.","binary-mlm-pro");
		if ( checkInputField($right_referral) ) 
			$error .= '<br/>'.__("\n Please specify your right leg active referrals.","binary-mlm-pro");
		if ( checkInputField($left_referral) ) 
			$error .= '<br/>'.__("\n Please specify your left leg active referrals.","binary-mlm-pro");
		//if ( checkInputField($member_in_group) ) 
		//$error .= '<br/>'.__("\n Please specify Your Minimum No. of Members in the Group.","binary-mlm-pro");
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_eligibility_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=payout";
			_e("<script>window.location='$url'</script>");
			$msg = __("<span style='color:green;'>Your eligibility settings has been successfully updated.</span>","binary-mlm-pro");
		}
	}// end outer if condition
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_eligibility_settings');
		?>
		
<div class='wrap1'>
	<h2><?php _e('Eligibility Settings','binary-mlm-pro');?> </h2>
	<div class="notibar msginfo">
		<a class="close"></a>
		<p><?php _e('Use this screen to define the eligibility criteria for a member to start earning commissions in the network.','binary-mlm-pro');?></p>
		<p><strong><?php _e('No. of Direct Paid Referrals','binary-mlm-pro');?> -</strong> <?php _e('The number of members that a member will need to directly and personally refer in the network before he can start earning commissions.','binary-mlm-pro');?></p>
		<p><strong><?php _e('No. of paid referral(s) on right leg','binary-mlm-pro');?> -</strong> <?php _e('The number of paid direct and personal referrals a member needs to introduce in this right leg before he can start earning commissions.','binary-mlm-pro');?></p>
		<p><strong><?php _e('No. of paid referral(s) on left leg','binary-mlm-pro');?> -</strong> <?php _e('The number of paid direct and personal referrals a member needs to introduce in this left leg before he can start earning commissions.','binary-mlm-pro');?></p>
	</div>
	<?php if($error) :?>
	<div class="notibar msgerror">
		<a class="close"></a>
		<p> <strong><?php _e('Please Correct the following Error','binary-mlm-pro');?> :</strong> <?=$error; ?></p>
	</div>
	<?php endif; ?>
	

		
<?php
		if(empty($mlm_settings))
		{
                    
?>
	
	<form name="admin_eligibility_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_direct_referral');">
				<?php _e('No. of direct paid referral(s)','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="direct_referral" id="direct_referral" size="10" value="<?php if(isset($_POST['direct_referral'])) _e(htmlentities($_POST['direct_referral']));?>">
				<div class="toggle-visibility" id="mlm_direct_referral"><?php _e('Please specify direct referral by you.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_right_referral');">
				<?php _e('No. of paid referral(s) on right leg','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="right_referral" id="right_referral" size="10" value="<?php if(isset($_POST['right_referral'])) _e(htmlentities($_POST['right_referral']));?>">
				<div class="toggle-visibility" id="mlm_right_referral"><?php _e('Please specify no. of paid referrals on right leg.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_left_referral');">
				<?php _e('No. of paid referral(s) on left leg','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="left_referral" id="left_referral" size="10" value="<?php if(isset($_POST['left_referral'])) _e(htmlentities($_POST['left_referral']));?>">
				<div class="toggle-visibility" id="mlm_left_referral"><?php _e('Please specify no. of paid referrals on left leg.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<!--<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('member_in_group');">
				<?php //_e('Minimum No. of Members in the Group','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="member_in_group" id="member_in_group" size="10" value="<?php //if(isset($_POST['member_in_group'])) _e(htmlentities($_POST['member_in_group']));?>">
				<div class="toggle-visibility" id="member_in_group"><?php //_e('Please specify no. of paid referrals on left leg.','binary-mlm-pro');?></div>
			</td>
		</tr>-->
		
		
		</table>
		<p class="submit">
	<input type="submit" name="mlm_eligibility_settings" id="mlm_eligibility_settings" value="<?php _e('Update Options', 'binary-mlm-pro');?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
</form>


<?php
		}
		else if(!empty($mlm_settings))
		{
			include 'js-validation-file.html'; ?>
		
			<form name="admin_eligibility_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_direct_referral');">
				<?php _e('No. of direct paid referral(s)','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="direct_referral" id="direct_referral" size="10" value="<?php if(isset($mlm_settings['direct_referral'])) _e($mlm_settings['direct_referral']);?>">
				<div class="toggle-visibility" id="mlm_direct_referral"><?php _e('Please specify direct referral by you.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_right_referral');">
				<?php _e('No. of paid referral(s) on right leg','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="right_referral" id="right_referral" size="10" value="<?php if(isset($mlm_settings['right_referral'])) _e($mlm_settings['right_referral']);?>">
				<div class="toggle-visibility" id="mlm_right_referral"><?php _e('Please specify no. of paid referrals on right leg.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_left_referral');">
				<?php _e('No. of paid referral(s) on left leg','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="left_referral" id="left_referral" size="10" value="<?php if(isset($mlm_settings['left_referral'])) _e($mlm_settings['left_referral']);?>">
				<div class="toggle-visibility" id="mlm_left_referral"><?php _e('Please specify no. of paid referrals on left leg.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<!--<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('member_in_groupText');">
				<?php //_e('Minimum No. of Members in the Group','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
		<input type="text" name="member_in_group" id="member_in_group" size="10" value="<?php //if(isset($mlm_settings['member_in_group'])) _e($mlm_settings['member_in_group']);?>">
				<div class="toggle-visibility" id="member_in_groupText"><?php //_e('Please specify Minimum No. of Members in the Group.','binary-mlm-pro');?></div>
			</td>
		</tr>-->
		
		</table>
		<p class="submit">
	<input type="submit" name="mlm_eligibility_settings" id="mlm_eligibility_settings" value="<?php _e('Update Options', 'binary-mlm-pro');?> &raquo;" class='button-primary' onclick="needToConfirm = false;" >
	</p>
</form>

<script language="JavaScript">
  populateArrays();
</script>
<?php
		}
		
	?>
	</div>
	<?php 	
	} // end if statement
	else{ ?>
	<?=$msg?>
        <?php }
} //end mlmEligibility funtion
?>