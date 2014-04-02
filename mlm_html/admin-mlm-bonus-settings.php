<?php

function mlmBonus() {
    //get database table prefix
    $table_prefix = mlm_core_get_table_prefix();

    $error = '';
    $chk = 'error';

    //most outer if condition
    if (isset($_POST['mlm_bonus_settings'])) {
        $bonus_criteria = sanitize_text_field($_POST['bonus_criteria']);
        $pair = $_POST['pair'];
        $amount = $_POST['amount'];

        if (checkinputField($bonus_criteria))
            $error .= "\n Please Select bonus criteria.";

        for ($i = 0; $i < count($pair); $i++) {
            if ($pair[$i] == "" || $amount[$i] == "")
                $error .= "\n Your bonus slab data is wrong.";
        }

        //if any error occoured
        if (!empty($error))
            $error = nl2br($error);
        else {
            $chk = '';
            update_option('wp_mlm_bonus_settings', $_POST);
            $url = get_bloginfo('url') . "/wp-admin/admin.php?page=admin-settings&tab=bonus";
            _e("<script>window.location='$url'</script>");
            $msg = _e("<span style='color:green;'>Your bonus has been successfully updated.</span>", 'binary-mlm-pro');
        }
    }// end outer if condition
    if ($chk != '') {
        $mlm_settings = get_option('wp_mlm_bonus_settings');
        ?>
        <div class='wrap1'>
            <h2><?php _e('Bonus Settings', 'binary-mlm-pro'); ?>  </h2>
            <div class="notibar msginfo">
                <a class="close"></a>
                <p><?php _e('In case you have a bonus option in your Network, use this tab to configure the bonus settings.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Bonus Criteria', 'binary-mlm-pro'); ?> - </strong><?php _e('The bonus amount can be paid on the basis of Total number of Pairs in a members network or the Total number of members referred Personally by a member. Select the option that suits your network.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Bonus Slabs', 'binary-mlm-pro'); ?> -</strong> <?php _e('Specify the total number of pairs or members that a member needs to achieve and the corresponding bonus amount for the same. To add a new slab click the Add Row button. Specify the number of pairs / members and the corresponding amount for the next slab. When you are done creating the slabs click the Update Options button.', 'binary-mlm-pro'); ?></p>

                <p><table width="200" align="center" border="1" cellspacing="0" cellpadding="5">
                    <tr>
                        <td colspan="2" align="center"><strong><?php _e('Example Slab Figures', 'binary-mlm-pro'); ?></strong></td>
                    </tr>
                    <tr>
                        <th width="50%"><?php _e('Unit', 'binary-mlm-pro'); ?></th>
                        <th><?php _e('Amount', 'binary-mlm-pro'); ?></th>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td align="right">1000.00</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td align="right">2000.00</td>
                    </tr>
                    <tr>
                        <td>20</td>
                        <td align="right">4000.00</td>
                    </tr> 
                </table> </p>

                <p><?php _e('This implies that a member is paid a commission of 10 on achieving 5 pairs or personal referrals.', 'binary-mlm-pro'); ?></p>
                <p><?php _e('On achieving the NEXT 10 pairs or personal referrals the member is paid a commission of 20. So now the member has either 15 pairs or 15 personal referrals in total.', 'binary-mlm-pro'); ?></p>
                <p><?php _e('On achieving the NEXT 20 pairs or personal referrals the member is paid a commission of 40. So now the member has either 35 pairs or 35 personal referrals in total.', 'binary-mlm-pro'); ?></p>
            </div>

        <?php if ($error) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p> <strong><?php _e('Please Correct the following Error', 'binary-mlm-pro'); ?> :</strong> <?php _e($error); ?></p>
                </div>
        <?php endif; ?>
        <?php if (!empty($msg)) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p><?php _e($msg); ?></p>
                </div>
        <?php endif; ?>


            <?php
            if (empty($mlm_settings)) {
                include 'js-validation-file.html';
                ?>
                <form name="admin_bonus_settings" method="post" action="">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
                        <tr>
                            <th scope="row" class="admin-settings">
                                <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus');">
            <?php _e('Bonus Criteria', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                            </th>
                            <td>
                                <select name="bonus_criteria" id="bonus_criteria">
                                    <option value=""><?php _e('Select Bonus Criteria', 'binary-mlm-pro'); ?></option>
                                    <option value="pair" <?= $_POST['bonus_criteria'] == 'pair' ? 'selected' : '' ?>><?php _e('No. of Pairs', 'binary-mlm-pro'); ?></option>
                                    <option value="personal" <?= $_POST['bonus_criteria'] == 'personal' ? 'selected' : '' ?>><?php _e('No. of Personal Referrer', 'binary-mlm-pro'); ?></option>
                                </select>
                                <div class="toggle-visibility" id="admin-mlm-bonus"><?php _e('Please select bonus type.', 'binary-mlm-pro'); ?></div>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row" class="admin-settings">
                                <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus-slab');">
            <?php _e('Bonus Slab', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>:</a>
                            </th>
                            <td>
                                <input type="button" value="<?php _e('Add Row', 'binary-mlm-pro') ?>" onclick="addRow('datatable')" class='button-primary' />
                                <input type="button" value="<?php _e('Delete Row', 'binary-mlm-pro') ?>" onclick="deleteRow('datatable')" class='button-primary' />
                                <div class="toggle-visibility" id="admin-mlm-bonus-slab"><?php _e('Add or remove bonus slab.', 'binary-mlm-pro'); ?></div>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                    </table>
                    <table id="datatableheading" cellspacing="5" cellpadding="5"  border="0" width="300">
                        <tr>
                            <td align="center" width="20%"><strong><?php _e('Select', 'binary-mlm-pro'); ?></strong></td>
                            <td align="center" width="40%"> <strong><?php _e('No. of Pairs', 'binary-mlm-pro'); ?></strong></td>
                            <td align="center" width="40%"><strong><?php _e('Amount', 'binary-mlm-pro'); ?></strong></td>
                        </tr>
                    </table>
                    <br\>
                    <table id="datatable"  cellspacing="0" cellpadding="0" border="0" width="300">
                        <tr>
                            <td align="center" width="20%"><input type="checkbox" name="chk[]"/></td>
                            <td align="center" width="40%"> <input type="text" name="pair[]" size="15" /> </td>
                            <td align="center" width="40%"> <input type="text" name="amount[]" size="15" /> </td>
                        </tr>
                    </table>

                    <table border="0" width="100%">	
                        <tr>
                            <td>
                                <p class="submit">
                                    <input type="submit" name="mlm_bonus_settings" id="mlm_bonus_settings" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
                                </p>
                            </td>
                        <tr>
                    </table>
                </form>
            </div>
            <script language="JavaScript">
                populateArrays();
            </script>
            <?php
        } else if (!empty($mlm_settings)) {
            include 'js-validation-file.html';
            ?>

            <form name="admin_bouns_settings" method="post" action="">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus');">
            <?php _e('Bonus Criteria', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <select name="bonus_criteria" id="bonus_criteria">
                                <option value=""><?php _e('Select Bonus Criteria', 'binary-mlm-pro'); ?></option>
                                <option value="pair" <?= $mlm_settings['bonus_criteria'] == 'pair' ? 'selected' : '' ?>><?php _e('No. of Pairs', 'binary-mlm-pro'); ?></option>
                                <option value="personal" <?= $mlm_settings['bonus_criteria'] == 'personal' ? 'selected' : '' ?>><?php _e('No. of Personal Referrer', 'binary-mlm-pro'); ?></option>
                            </select>
                            <div class="toggle-visibility" id="admin-mlm-bonus"><?php _e('Please select bonus type.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus-slab');">
            <?php _e('Bonus Slab', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="button" value="<?php _e('Add Row', 'binary-mlm-pro') ?>" onclick="addRow('datatable')" class='button-primary'/>
                            <input type="button" value="<?php _e('Delete Row', 'binary-mlm-pro') ?>" onclick="deleteRow('datatable')" class='button-primary'/>
                            <div class="toggle-visibility" id="admin-mlm-bonus-slab"><?php _e('Add or remove bonus slab.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                </table>
                <table id="datatableheading"  border="0" align="center">
                        <tr>
                            <td align="center" width="11%"><strong><?php _e('Select', 'binary-mlm-pro'); ?></strong></td>
                            <td align="center" width="40%"> <strong><?php _e('No. of Pairs', 'binary-mlm-pro'); ?></strong></td>
                            <td align="center" width="40%"><strong><?php _e('Amount', 'binary-mlm-pro'); ?></strong></td>
                        </tr>
                    </table>
                <table id="datatable"  border="0" align="center">
            <?php
            $i = 0;
            while ($i < count($mlm_settings['pair'])) {
                ?>
                        <tr>
                            <td><input type="checkbox" name="chk[]"/></td>
                            <td> <input type="text" name="pair[]" size="15" value="<?= $mlm_settings['pair'][$i] ?>"/> </td>
                            <td> <input type="text" name="amount[]" size="15" value="<?= $mlm_settings['amount'][$i] ?>"/> </td>
                        </tr>    	
                        <?php
                        $i++;
                    }
                    ?>
                </table>

                <p class="submit">
                    <input type="submit" name="mlm_bonus_settings" id="mlm_bonus_settings" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
                </p>
            </form>
            </div>
            <script language="JavaScript">
                populateArrays();
            </script>
            <?php
        }
    } // end if statement
    else
        _e($msg);
}

//end mlmBonus function
?>