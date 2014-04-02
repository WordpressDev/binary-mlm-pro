<?php
require_once("php-form-validation.php");

function register_user_html_page()
{

    global $wpdb, $current_user;
    $user_id = $current_user->ID;
    $table_prefix = mlm_core_get_table_prefix();
    $error = '';
    $chk = 'error';


    if (!empty($_GET['sp_name']))
    {
        $sp_name = $wpdb->get_var("select username from {$table_prefix}mlm_users where username='" . $_GET['sp_name'] . "'");
        if ($sp_name)
        {
            ?>
            <script type='text/javascript'>
                $.cookie('sp_name', '<?= $sp_name ?>', {path: '/'});
            </script>
            <?php
        }
    }
    else if (!empty($_REQUEST['sp']))
    {
        $sp_name = $wpdb->get_var("select username from {$table_prefix}mlm_users where user_key='" . $_REQUEST['sp'] . "'");
        if ($sp_name)
        {
            ?>
            <script type='text/javascript'>
                $.cookie('sp_name', '<?= $sp_name ?>', {path: '/'});
            </script>
            <?php
        }
    }
    else
    {
        $sp_name = empty($_COOKIE["sp_name"]) ? '' : $_COOKIE["sp_name"];
    }

    /*     * ****date format ***** */

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    //$user_registered=date($date_format.' '.$time_format ,strtotime(current_time( 'mysql')));
    $user_registered = date('Y-m-d H:i:s', strtotime(current_time('mysql')));

    /*     * ****** end******* */

    global $current_user;

    get_currentuserinfo();
    $mlm_general_settings = get_option('wp_mlm_general_settings');
    if (is_user_logged_in())
    {
        $sponsor_name = $current_user->user_login;
        $readonly_sponsor = 'readonly';
    }
    else if (isset($_REQUEST['sp']) && $_REQUEST['sp'] != '')
    {

        $sponsorName = getusernamebykey($_REQUEST['sp']);

        if (isset($sponsorName) && $sponsorName != '')
        {
            $readonly_sponsor = 'readonly';
            $sponsor_name = $sponsorName;
        }
        else
        {

            redirectPage(home_url(), array());
            exit;
        }
    }
    else if (!empty($_REQUEST['sp_name']))
    {

        $sponsorName = $_REQUEST['sp_name'];

        if (!empty($sponsorName))
        {
            $readonly_sponsor = 'readonly';
            $sponsor_name = $sponsorName;
        }
        else
        {

            redirectPage(home_url(), array());
            exit;
        }
    }
    else
    {
        $readonly_sponsor = '';
    }

    /* script for auto insert users================================================ *

    echo '<form name="form1"action="" method="post">
      <input type="number" min="0" max="99" name="id"/>
      <input type="number" min="0" max="1" name="epin"/>
      <input type="number" min="0" max="1" name="leg"/>
      <input type="submit"/></form>';
    $epinstatus = isset($_POST['epin']) ? $_POST['epin'] : '';
    if ($epinstatus != '')
        $epin_no = $wpdb->get_var("select epin_no from {$table_prefix}mlm_epins where  point_status='{$epinstatus}' AND status=0 limit 1 ");
    if (isset($_POST['id']))
    {
        $z = $_POST['id'];
        
        $_POST = array('firstname' => 'binary' . $z,
            'lastname' => 'binary' . $z,
            'username' => 'binary' . $z,
            'password' => 'binary' . $z,
            'confirm_password' => 'binary' . $z,
            'email' => 'binary' . $z . '@gmail.com',
            'confirm_email' => 'binary' . $z . '@gmail.com',
            'sponsor' => !empty($sponsor_name) ? $sponsor_name : '',
            'submit' => 'submit',
            'leg' => $_POST['leg'],
            'epin'=>$epin_no,
        );
    }      //'epin'=>!empty($epin_no)?$epin_no:'',
    /* ===========================================================Close Auto Insert. */
    //most outer if condition
    if (isset($_POST['submit']))
    {

        $firstname = sanitize_text_field($_POST['firstname']);
        $lastname = sanitize_text_field($_POST['lastname']);
        $username = sanitize_text_field($_POST['username']);
        $sponsor = sanitize_text_field($_POST['sponsor']);
        if (empty($sponsor))
        {
            $sponsor = $wpdb->get_var("select `username` FROM {$table_prefix}mlm_users order by id asc limit 1");
        }
        /*         * ***** check for the epin field ***** */
        if (isset($_POST['epin']) && !empty($_POST['epin']))
        {
            $epin = sanitize_text_field($_POST['epin']);
        }
        else if (isset($_POST['epin']) && empty($_POST['epin']))
        {

            $epin = '';
        }

        /*         * ***** check for the epin field ***** */

        $password = sanitize_text_field($_POST['password']);
        $confirm_pass = sanitize_text_field($_POST['confirm_password']);
        $email = sanitize_text_field($_POST['email']);
        $confirm_email = sanitize_text_field($_POST['confirm_email']);



        //Add usernames we don't want used
        $invalid_usernames = array('admin');
        //Do username validation
        $username = sanitize_user($username);

        if (!validate_username($username) || in_array($username, $invalid_usernames))
            $error .= "\n Username is invalid.";

        if (username_exists($username))
            $error .= "\n Username already exists.";
        /*         * ***** check for the epin field ***** */
        if (isset($epin) && !empty($epin))
        {

            if (epin_exists($epin))
            {
                $error .= "\n ePin already issued or wrong ePin.";
            }
        }
        if (!empty($mlm_general_settings['sol_payment']))
        {
            if (isset($_POST['epin']) && empty($_POST['epin']))
            {
                $error .= "\n Please enter your ePin.";
            }
        }

        /*         * ***** check for the epin field ***** */

        if (checkInputField($password))
            $error .= "\n Please enter your password.";

        if (confirmPassword($password, $confirm_pass))
            $error .= "\n Please confirm your password.";

        if (checkInputField($sponsor))
            $error .= "\n Please enter your sponsor name.";

        if (checkInputField($firstname))
            $error .= "\n Please enter your first name.";

        if (checkInputField($lastname))
            $error .= "\n Please enter your last name.";

        //Do e-mail address validation
        if (!is_email($email))
            $error .= "\n E-mail address is invalid.";

        if (email_exists($email))
            $error .= "\n E-mail address is already in use.";

        if (confirmEmail($email, $confirm_email))
            $error .= "\n Please confirm your email address.";

        $sql = "SELECT COUNT(*) num, `user_key` 
				FROM {$table_prefix}mlm_users 
				WHERE `username` = '" . $sponsor . "'";
        $intro = $wpdb->get_row($sql);


        if (isset($_GET['l']) && $_GET['l'] != '')
            $leg = $_GET['l'];
        else
            @$leg = $_POST['leg'];

        if (isset($leg) && $leg != '0')
        {
            if ($leg != '1')
            {
                $error .= "\n You have enter a wrong placement.";
            }
        }

        //generate random numeric key for new user registration
        $user_key = generateKey();
        //if generated key is already exist in the DB then again re-generate key
        do
        {
            $check = $wpdb->get_var("SELECT COUNT(*) ck 
													FROM {$table_prefix}mlm_users 
													WHERE `user_key` = '" . $user_key . "'");
            $flag = 1;
            if ($check == 1)
            {
                $user_key = generateKey();
                $flag = 0;
            }
        } while ($flag == 0);

        //check parent key exist or not

        if (isset($_GET['k']) && $_GET['k'] != '')
        {
            if (!checkKey($_GET['k']))
                $error .= "\n Parent key does't exist.";
            // check if the user can be added at the current position
            $checkallow = checkallowed($_GET['k'], $leg);
            if ($checkallow >= 1)
                $error .= "\n You have enter a wrong placement.";
        }
        if (!isset($leg))
        {
            $key = $wpdb->get_var("SELECT user_key FROM {$table_prefix}mlm_users WHERE user_id = '$user_id'");
            $l = totalLeftLegUsers($key);
            $r = totalRightLegUsers($key);
            if ($l < $r)
            {
                $leg = '0';
            }
            else
            {
                $leg = '1';
            }
        }
        // outer if condition
        if (empty($error))
        {
            // inner if condition
            if ($intro->num == 1)
            {
                $sponsor = $intro->user_key;

                $sponsor1 = $sponsor;
                //find parent key
                if (isset($_GET['k']) && $_GET['k'] != '')
                {
                    $parent_key = $_GET['k'];
                }
                else
                {
                    $readonly_sponsor = '';
                    do
                    {
                        $sql = "SELECT `user_key` FROM {$table_prefix}mlm_users 
								WHERE parent_key = '" . $sponsor1 . "' AND 
								leg = '" . $leg . "' AND banned = '0'";
                        $spon = $wpdb->get_var($sql);
                        $num = $wpdb->num_rows;
                        if ($num)
                        {
                            $sponsor1 = $spon;
                        }
                    } while ($num == 1);
                    $parent_key = $sponsor1;
                }




                $user = array
                    (
                    'user_login' => $username,
                    'user_pass' => $password,
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'user_email' => $email,
                    'user_registered' => $user_registered,
                    'role' => 'mlm_user'
                );

                // return the wp_users table inserted user's ID
                $user_id = wp_insert_user($user);


                /* Send e-mail to admin and new user - 
                  You could create your own e-mail instead of using this function */
                wp_new_user_notification($user_id, $password);


                /*                 * ******* product Price set *************** */
                if (!empty($mlm_general_settings['product_price']))
                {
                    $pc = $mlm_general_settings['product_price'];
                }
                else
                {
                    $pc = '0';
                }


                //insert the data into fa_user table

                if (!empty($epin))
                {
                    $pointResult = mysql_query("select point_status from {$table_prefix}mlm_epins where epin_no = '{$epin}'");
                    $pointStatus = mysql_fetch_row($pointResult);
                    // to epin point status 1 
                    if ($pointStatus[0] == '1')
                    {
                        $paymentStatus = '1';
                        $product_price = $pc;
                    }
                    // to epin point status 1 
                    else if ($pointStatus[0] == '0')
                    {
                        $paymentStatus = '2';
                        $product_price = '0';
                    }
                }
                else
                { // to non epin 
                    $paymentStatus = '0';
                    $product_price = '0';
                }


                $insert = "INSERT INTO {$table_prefix}mlm_users
						   (
								user_id, username, user_key, parent_key, sponsor_key, leg,payment_status,product_price
							) 
							VALUES
							(
								'" . $user_id . "','" . $username . "', '" . $user_key . "', '" . $parent_key . "', '" . $sponsor . "', '" . $leg . "','" . $paymentStatus . "','" . $product_price . "'
							)";

                // if all data successfully inserted
                if ($wpdb->query($insert))
                { //begin most inner if condition
                    //entry on Left and Right Leg tables
                    if ($leg == 0)
                    {
                        $insert = "INSERT INTO {$table_prefix}mlm_leftleg 
								   (
										pkey, ukey
									) 
									VALUES 
									(
										'" . $parent_key . "','" . $user_key . "'
									)";
                        $insert = $wpdb->query($insert);
                    }
                    else if ($leg == 1)
                    {
                        $insert = "INSERT INTO {$table_prefix}mlm_rightleg
								   (
										pkey, ukey
									) 
									VALUES 
									(
										'" . $parent_key . "','" . $user_key . "'
									)";
                        $insert = $wpdb->query($insert);
                    }
                    //begin while loop
                    while ($parent_key != '0')
                    {
                        $query = "SELECT COUNT(*) num, parent_key, leg 
								  FROM {$table_prefix}mlm_users 
								  WHERE user_key = '" . $parent_key . "'
								  AND banned = '0'";
                        $result = $wpdb->get_row($query);
                        if ($result->num == 1)
                        {
                            if ($result->parent_key != '0')
                            {
                                if ($result->leg == 1)
                                {
                                    $tbright = "INSERT INTO {$table_prefix}mlm_rightleg 
												(
													pkey,ukey
												) 
												VALUES
												(
													'" . $result->parent_key . "','" . $user_key . "'
												)";
                                    $tbright = $wpdb->query($tbright);
                                }
                                else
                                {
                                    $tbleft = "INSERT INTO {$table_prefix}mlm_leftleg 
												(
													pkey, ukey
												) 
												VALUES
												(
													'" . $result->parent_key . "','" . $user_key . "'
												)";
                                    $tbleft = $wpdb->query($tbleft);
                                }
                            }
                            $parent_key = $result->parent_key;
                        }
                        else
                        {
                            $parent_key = '0';
                        }
                    }//end while loop
                    if (isset($epin) && !empty($epin))
                    {
                        $sql = "update {$table_prefix}mlm_epins set user_key='{$user_key}', date_used=now(), status=1 where epin_no ='{$epin}' "; // Update epin according user_key (19-07-2013)

                        mysql_query($sql);
                        if ($paymentStatus == 1)
                        {
                            insert_refferal_commision($user_id, $sponsor, $user_key);
                        }
                    }
                    $chk = '';
                    $msg = "<span style='color:green;'>Congratulations! You have successfully registered in the system.</span>";
                }//end most inner if condition
            } //end inner if condition
            else
                $error = "\n Sponsor does not exist in the system.";
        }//end outer if condition
    }//end most outer if condition
    //if any error occoured
    if (!empty($error))
        $error = nl2br($error);

    if ($chk != '')
    {

        include 'js-validation-file.html';
        ?>

        <?php
        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);
        $general_setting = get_option('wp_mlm_general_settings');
        if (is_user_logged_in())
        {
            if (!empty($general_setting['wp_reg']) && !empty($general_setting['reg_url']) && $user_role != 'mlm_user')
            {
                echo "<script>window.location ='" . site_url() . '/' . $general_setting['reg_url'] . "'</script>";
            }
        }
        else
        {
            if (!empty($general_setting['wp_reg']) && !empty($general_setting['reg_url']))
            {
                echo "<script>window.location ='" . site_url() . '/' . $general_setting['reg_url'] . "'</script>";
            }
        }
        ?>

        <span style='color:red;'><?= $error ?></span>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <form name="frm" method="post" action="" onSubmit="return formValidation();">
                <tr>
                    <td><?php _e('Create Username', 'binary-mlm-pro'); ?><span style="color:red;">*</span> :</td>
                    <td><input type="text" name="username" id="username" value="<?php if (!empty($_POST['username'])) _e(htmlentities($_POST['username'])); ?>" maxlength="20" size="37" onBlur="checkUserNameAvailability(this.value);"><br /><div id="check_user"></div></td>
                </tr>
                <?php
                $mlm_settings = get_option('wp_mlm_general_settings');

                if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1' && isset($mlm_settings['sol_payment']) && $mlm_settings['sol_payment'] == '1')
                {
                    ?>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Enter ePin', 'binary-mlm-pro'); ?><span style="color:red;">*</span> :</td>
                        <td><input type="text" name="epin" id="epin" value="<?php if (!empty($_POST['epin'])) _e(htmlentities($_POST['epin'])); ?>" maxlength="20" size="37" onBlur="checkePinAvailability(this.value);"><br /><div id="check_epin"></div></td>
                    </tr>
        <?php } else if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1' && isset($mlm_settings['sol_payment']) && $mlm_settings['sol_payment'] == '0')
        {
            ?>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Enter ePin', 'binary-mlm-pro'); ?> :</td>
                        <td><input type="text" name="epin" id="epin" value="<?php if (!empty($_POST['epin'])) _e(htmlentities($_POST['epin'])); ?>" maxlength="20" size="37" onBlur="checkePinAvailability1(this.value);"><br /><div id="check_epin"></div></td>
                    </tr>
        <?php } ?>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td><?php _e('Create Password', 'binary-mlm-pro') ?> <span style="color:red;">*</span> :</td>
                    <td>	<input type="password" name="password" id="password" maxlength="20" size="37" >
                        <br /><span style="font-size:12px; font-style:italic; color:#006633"><?php _e('Password length atleast 6 character', 'binary-mlm-pro'); ?></span>
                    </td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('Confirm Password', 'binary-mlm-pro') ?>  <span style="color:red;">*</span> :</td>
                    <td><input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" ></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td><?php _e('Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="email" id="email" value="<?php if (!empty($_POST['email'])) _e(htmlentities($_POST['email'])); ?>"  size="37" ></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr><tr>

                <tr>
                    <td><?php _e('Confirm Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="confirm_email" id="confirm_email" value="<?php if (!empty($_POST['confirm_email'])) _e(htmlentities($_POST['confirm_email'])); ?>" size="37" ></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('First Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="firstname" id="firstname" value="<?php if (!empty($_POST['firstname'])) _e(htmlentities($_POST['firstname'])); ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'firstname');" ></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('Last Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="lastname" id="lastname" value="<?php if (!empty($_POST['lastname'])) _e(htmlentities($_POST['lastname'])); ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'lastname');"></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
        <?php
        if (isset($sponsor_name) && $sponsor_name != '')
        {
            $spon = $sponsor_name;
        }
        else if (isset($sp_name))
            $spon = $sp_name;
        else if (isset($_POST['sponsor']))
            $spon = htmlentities($_POST['sponsor']);
        ?>
                    <td><?php _e('Sponsor Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="sponsor" id="sponsor" value="<?php if (!empty($spon)) _e($spon); ?>" maxlength="20" size="37" onBlur="checkReferrerAvailability(this.value);" <?= $readonly_sponsor; ?>>
                        <br /><div id="check_referrer"></div>
                    </td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('Placement', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <?php
                    if (isset($_POST['leg']) && $_POST['leg'] == '0')
                    {
                        $checked = 'checked';
                    }
                    else if (isset($_GET['l']) && $_GET['l'] == '0')
                    {
                        $checked = 'checked';
                        $disable_leg = 'disabled';
                    }
                    else
                        $checked = '';

                    if (isset($_POST['leg']) && $_POST['leg'] == '1')
                    {
                        $checked1 = 'checked';
                    }
                    else if (isset($_GET['l']) && $_GET['l'] == '1')
                    {
                        $checked1 = 'checked';
                        $disable_leg = 'disabled';
                    }
                    else
                        $checked1 = '';
                    ?>

                    <td><?= __('Left', 'binary-mlm-pro') ?> <input id="left" type="radio" name="leg" value="0" <?= $checked; ?> <?php if (!empty($disable_leg)) _e($disable_leg); ?>/>
        <?= __('Right', 'binary-mlm-pro') ?><input id="right" type="radio" name="leg" value="1" <?= $checked1; ?> <?php if (!empty($disable_leg)) _e($disable_leg); ?>/>



                    </td>
                </tr>








                <tr>
                    <td colspan="2"><input type="submit" name="submit" id="submit" value="<?php _e('Submit', 'binary-mlm-pro') ?>" /></td>
                </tr>
            </form>
        </table>
        <?php
    }
    else
        _e($msg);
}

//function end
?>