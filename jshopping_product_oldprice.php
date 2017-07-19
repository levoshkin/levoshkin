<?php
/**
* @version      1.0.0 April 2017
* @author       Igor Levoshkin
* @package      Jshopping
* @copyright    Copyright (C) 2017 Igor Levoshkin. All rights reserved.
* @license      GNU/GPL
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.helper');

define('JPATH_BASE', __DIR__ . '/../../../');
require_once  JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';


class plgJshoppingJshopping_product_oldprice extends JPlugin
{
    public function __construct(&$subject, $config) 
	{
        parent::__construct($subject, $config);
    }
	
    public function onDisplayProductEditTabsEndTab(&$row, &$lists, &$tax_value)
	{
		require_once JPATH_BASE . '/../components/com_jshopping/lib/factory.php';
		include(dirname(__FILE__)."/lang/ru-RU/ru-RU.php");
		if($row->product_id)
			echo '<li><a href="#oldprice-page" data-toggle="tab">'._JSHOP_DISCOUNT.'</a></li>';
	}
	
    public function onDisplayProductEditTabs(&$pane, &$row, &$lists, &$tax_value, &$currency)
	{
		$product_id = $row->product_id;
		if(isset($product_id)){
			$db=& JFactory::getDBO();
			$query = "SELECT discount_type, discount_value, discount_apply, UNIX_TIMESTAMP(start_date) as discount_start_date, UNIX_TIMESTAMP(expire_date) as discount_expire_date FROM `#__jshopping_discounts` WHERE product_id=".$product_id;
			$db->setQuery($query);
			$res = $db->loadObject();
			include(dirname(__FILE__)."/tmpl/default.php");
		}
	}
	
	public function onAfterSaveProductEnd(&$product){
        $lang = JSFactory::getLang();
		$app = JFactory::getApplication();
		
		$product_id = $app->input->getInt('product_id');
		$discount_value = $app->input->getString('discount_value');
		$discount_apply = $app->input->getInt('discount_checked');
		$discount_type = $app->input->getInt('discount_type');
		$start_date = $app->input->getString('start_date');
		$expire_date = $app->input->getString('expire_date');
		if(!$discount_apply)
			$discount_apply = 0;
		
		$db=& JFactory::getDBO();
		
		if($discount_value*1 != 0){
			$query = "INSERT INTO `#__jshopping_discounts` SET product_id=".$product_id.", discount_type=".$discount_type.", discount_apply=".$discount_apply.", start_date='".$start_date."', expire_date='".$expire_date."', discount_value='".$discount_value."' ON DUPLICATE KEY UPDATE discount_type=".$discount_type.", discount_apply=".$discount_apply.", start_date='".$start_date."', expire_date='".$expire_date."', discount_value='".$discount_value."'";
			$db->setQuery($query);
			$db->query();
		}else{
			$query = "SELECT product_price, product_old_price FROM `#__jshopping_products` WHERE product_id=".$product_id;
			$db->setQuery($query);
			$res = $db->loadObject();
			
			if($res->product_old_price != 0){
				$query = "UPDATE `#__jshopping_products` SET product_price=product_old_price, product_old_price=0 WHERE product_id=".$product_id;
				$db->setQuery($query);
				$db->query();
			}

			$query = "DELETE FROM `#__jshopping_discounts` WHERE product_id=".$product_id;
			$db->setQuery($query);
			$db->query();
			
			$query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id=".$product_id;
			$db->setQuery($query);
			$attribs = $db->loadObjectList();
			
			foreach($attribs as $attr){
				if($attr->old_price != 0){
					$query = "UPDATE `#__jshopping_products_attr` SET price=old_price, old_price=0 WHERE product_attr_id=".$attr->product_attr_id;
					$db->setQuery($query);
					$db->query();
				}
			}
		}
		
		$query = "SELECT D.product_id, UNIX_TIMESTAMP(D.expire_date) as discount_expire_date, UNIX_TIMESTAMP(D.start_date) as discount_start_date, D.discount_type, D.discount_value, D.discount_apply, P.product_price, P.product_old_price FROM `#__jshopping_discounts` as D 
				LEFT JOIN `#__jshopping_products` as P ON D.product_id=P.product_id";
		$db->setQuery($query);
		$res = $db->loadObjectList();
		
		foreach($res as $val){
			$expire_time = $val->discount_expire_date;
			$start_time = $val->discount_start_date;
			
			$query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id=".$val->product_id;
			$db->setQuery($query);
			$attribs = $db->loadObjectList();
				
			if(($start_time == $expire_time || (time() >= $start_time && time() < $expire_time)) && $val->product_old_price == 0 && $val->discount_type == 1 && $val->discount_apply == 1){
				
				$new_price = $val->product_price - $val->discount_value;
				$query = "UPDATE `#__jshopping_products` SET product_old_price=product_price, product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price == 0){
						$new_attr_price = $attr->price - $val->discount_value;
						$query = "UPDATE `#__jshopping_products_attr` SET old_price=price, price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			if(($start_time == $expire_time || (time() >= $start_time && time() < $expire_time)) && $val->discount_type == 0 && $val->product_old_price == 0 && $val->discount_apply == 1){
				
				$new_price = $val->product_price - ($val->product_price * $val->discount_value / 100);
				$query = "UPDATE `#__jshopping_products` SET product_old_price=product_price, product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();			
				
				foreach($attribs as $attr){
					if($attr->old_price == 0){
						$new_attr_price = $attr->price - ($attr->price * $val->discount_value / 100);
						$query = "UPDATE `#__jshopping_products_attr` SET old_price=price, price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			if(($val->discount_apply == 0 || time() >= $expire_time || time() < $start_time) && $start_time != $expire_time && $val->product_old_price != 0){
				
				$query = "UPDATE `#__jshopping_products` SET product_price=product_old_price, product_old_price=0 WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price != 0){
						$query = "UPDATE `#__jshopping_products_attr` SET price=old_price, old_price=0 WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
				
			}

			if(($start_time == $expire_time || (time() >= $start_time && time() < $expire_time)) && $val->product_old_price != 0 && $val->discount_type == 1 && $val->discount_apply == 1){
				
				$new_price = $val->product_old_price - $val->discount_value;
				$query = "UPDATE `#__jshopping_products` SET product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();	
				
				foreach($attribs as $attr){
					if($attr->old_price != 0){
						$new_attr_price = $attr->old_price - $val->discount_value;
						$query = "UPDATE `#__jshopping_products_attr` SET price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			if(($start_time == $expire_time || (time() >= $start_time && time() < $expire_time)) && $val->discount_type == 0 && $val->product_old_price != 0 && $val->discount_apply == 1){
				
				$new_price = $val->product_old_price - ($val->product_old_price * $val->discount_value / 100);
				$query = "UPDATE `#__jshopping_products` SET product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();	
				
				foreach($attribs as $attr){
					if($attr->old_price != 0){
						$new_attr_price = $attr->old_price - ($attr->old_price * $val->discount_value / 100);
						$query = "UPDATE `#__jshopping_products_attr` SET price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			if($val->discount_apply == 0 && $start_time == $expire_time && $val->product_old_price != 0){
				
				$query = "UPDATE `#__jshopping_products` SET product_price=product_old_price, product_old_price=0 WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price != 0){
						$query = "UPDATE `#__jshopping_products_attr` SET price=old_price, old_price=0 WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}
	}
	
	public function onBeforeLoadProductList(){
		$db=& JFactory::getDBO();
		$query = "SELECT D.product_id, UNIX_TIMESTAMP(D.expire_date) as discount_expire_date, UNIX_TIMESTAMP(D.start_date) as discount_start_date, D.discount_type, D.discount_value, D.discount_apply, P.product_price, P.product_old_price FROM `#__jshopping_discounts` as D LEFT JOIN `#__jshopping_products` as P ON D.product_id=P.product_id";
		$db->setQuery($query);
		$res = $db->loadObjectList();
		
		foreach($res as $val){
			$expire_time = $val->discount_expire_date;
			$start_time = $val->discount_start_date;
			
			$query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id=".$val->product_id;
			$db->setQuery($query);
			$attribs = $db->loadObjectList();
				
			if($val->discount_expire_date > 0 && time() >= $val->discount_expire_date && $val->discount_expire_date != $val->discount_start_date && $val->product_old_price !=0){
				$query = "UPDATE `#__jshopping_products` SET product_price=product_old_price, product_old_price=0 WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price != 0){
						$query = "UPDATE `#__jshopping_products_attr` SET price=old_price, old_price=0 WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}

				$query = "DELETE FROM `#__jshopping_discounts` WHERE product_id=".$val->product_id;
				$db->setQuery($query)->execute();
			}

			if(($start_time == $expire_time || time() >= $start_time && time() < $expire_time) && $val->	discount_type == 1 && $val->product_old_price == 0 && $val->discount_apply != 0){
				$new_price = $val->product_price - $val->discount_value;
				$query = "UPDATE `#__jshopping_products` SET product_old_price=product_price, product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price == 0){
						$new_attr_price = $attr->price - $val->discount_value;
						$query = "UPDATE `#__jshopping_products_attr` SET old_price=price, price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			if(($start_time == $expire_time || time() >= $start_time && time() < $expire_time) && $val->discount_type == 0 && $val->product_old_price == 0 && $val->discount_apply != 0){
				$new_price = $val->product_price - ($val->product_price * $val->discount_value / 100);
				$query = "UPDATE `#__jshopping_products` SET product_old_price=product_price, product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price == 0){
						$new_attr_price = $attr->price - ($attr->price * $val->discount_value / 100);
						$query = "UPDATE `#__jshopping_products_attr` SET old_price=price, price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}
	}
	
    public function onBeforeDisplayListProductsGetAllProducts(){
		$db=& JFactory::getDBO();
		$query = "SELECT D.product_id, UNIX_TIMESTAMP(D.expire_date) as discount_expire_date, UNIX_TIMESTAMP(D.start_date) as discount_start_date, D.discount_type, D.discount_value, D.discount_apply, P.product_price, P.product_old_price FROM `#__jshopping_discounts` as D LEFT JOIN `#__jshopping_products` as P ON D.product_id=P.product_id";
		$db->setQuery($query);
		$res = $db->loadObjectList();
		
		foreach($res as $val){
			$expire_time = $val->discount_expire_date;
			$start_time = $val->discount_start_date;
			
			$query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id=".$val->product_id;
			$db->setQuery($query);
			$attribs = $db->loadObjectList();
				
			if($val->discount_expire_date > 0 && time() >= $val->discount_expire_date && $val->discount_expire_date != $val->discount_start_date && $val->product_old_price !=0){
				$query = "UPDATE `#__jshopping_products` SET product_price=product_old_price, product_old_price=0 WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price != 0){
						$query = "UPDATE `#__jshopping_products_attr` SET price=old_price, old_price=0 WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}

				$query = "DELETE FROM `#__jshopping_discounts` WHERE product_id=".$val->product_id;
				$db->setQuery($query)->execute();
			}

			if(($start_time == $expire_time || time() >= $start_time && time() < $expire_time) && $val->	discount_type == 1 && $val->product_old_price == 0 && $val->discount_apply != 0){
				$new_price = $val->product_price - $val->discount_value;
				$query = "UPDATE `#__jshopping_products` SET product_old_price=product_price, product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price == 0){
						$new_attr_price = $attr->price - $val->discount_value;
						$query = "UPDATE `#__jshopping_products_attr` SET old_price=price, price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			if(($start_time == $expire_time || time() >= $start_time && time() < $expire_time) && $val->discount_type == 0 && $val->product_old_price == 0 && $val->discount_apply != 0){
				$new_price = $val->product_price - ($val->product_price * $val->discount_value / 100);
				$query = "UPDATE `#__jshopping_products` SET product_old_price=product_price, product_price=".$new_price." WHERE product_id=".$val->product_id;
				$db->setQuery($query);
				$db->query();
				
				foreach($attribs as $attr){
					if($attr->old_price == 0){
						$new_attr_price = $attr->price - ($attr->price * $val->discount_value / 100);
						$query = "UPDATE `#__jshopping_products_attr` SET old_price=price, price=".$new_attr_price." WHERE product_attr_id=".$attr->product_attr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}
	}
}