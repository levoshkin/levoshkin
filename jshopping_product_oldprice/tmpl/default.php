<?php
/**
* @version      1.0.0 April 2017
* @author       Igor Levoshkin
* @package      Jshopping
* @copyright    Copyright (C) 2017 Igor Levoshkin. All rights reserved.
* @license      GNU/GPL
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
define('JPATH_BASE', __DIR__ . '/../../../');

include_once JPATH_BASE . '/../components/com_jshopping/helpers/selectoptions.php';
$jshopConfig = JSFactory::getConfig();
?>
<div id="oldprice-page" class="tab-pane">
    <div class="col100">
    <table class="admintable" width="90%">
    <tr>
		<td class="key" style="width:180px;">
			<?php echo _JSHOP_APPLY;?>
		</td>
		<td>
			<input type="checkbox" class="inputbox" id="discount_checked" name="discount_checked" value="1" <?php if ($res->discount_apply) echo 'checked="checked"'?>/>
		</td>
    </tr>
	<tr>
		<td class="key">
			<?php echo _JSHOP_TYPE_COUPON;?>*
		</td>
		<td>
			<input name="discount_type" type="radio" value="0" <?php if($res->discount_type==0) echo 'checked="checked"'?> onclick="javascript:jQuery('#dtype_value').hide();jQuery('#dtype_percent').show()""/><?php echo ' ' . _JSHOP_COUPON_PERCENT?>
			<input name="discount_type" type="radio" value="1" <?php if($res->discount_type==1) echo 'checked="checked"'?> onclick="javascript:jQuery('#dtype_value').show();jQuery('#dtype_percent').hide()"/><?php echo ' ' . _JSHOP_COUPON_ABS_VALUE?>
			<?php echo JHTML::tooltip( _JSHOP_COUPON_VALUE_DESCRIPTION, _JSHOP_HINT );?>
		</td>
   </tr>
   <tr>
     <td class="key">
       <?php echo _JSHOP_VALUE; ?>*
     </td>
     <td>
       <input type="text" class="inputbox" id="discount_value" name="discount_value" value="<?php if($res->discount_value){echo $res->discount_value;} else {?>0<?php }?>"/>
       <span id="dtype_percent" name="dtype_percent" <?php if($res->discount_type==1) {?>style="display:none"<?php }?>>%</span>
       <span id="dtype_value" name="dtype_value" <?php if($res->discount_type==0) {?>style="display:none"<?php }?>></span>
		 <script>
			var options = document.getElementById('currency_id').getElementsByTagName('option');
			for(var i=0; i<options.length; i++){
				if(options[i].hasAttribute('selected'))
					dtype_value.appendChild(document.createTextNode(options[i].firstChild.nodeValue));
			}
		 </script>
     </td>
	</tr>
	<tr>
		<td class="key">
			<?php echo _JSHOP_START_DATE;?>
		</td>
		<td>
			<?php if($res->discount_start_date!=0){
				echo JHTML::_('calendar', date('Y-m-d', $res->discount_start_date), 'start_date', 'start_date', 
				'%Y-%m-%d', array('size'=>25,'style'=>"class='inputbox'"));
				}else{
				echo JHTML::_('calendar', date('Y-m-d', time()), 'start_date', 'start_date', 
				'%Y-%m-%d', array('size'=>25,'style'=>"class='inputbox'"));
				}?>
		</td>
    </tr>     
    <tr>
		<td class="key">
			<?php echo _JSHOP_EXPIRE_DATE;?>
		</td>
		<td>
			<?php if($res->discount_expire_date!=0){
				echo JHTML::_('calendar', date('Y-m-d', $res->discount_expire_date), 'expire_date', 'expire_date', 
				'%Y-%m-%d', array('size'=>25,'style'=>"class='inputbox'"));
			}else{
				echo JHTML::_('calendar', date('Y-m-d', time()), 'expire_date', 'expire_date', 
				'%Y-%m-%d', array('size'=>25,'style'=>"class='inputbox'"));
			}?>
		</td>
    </tr>
	</table>
	</div>
</div>