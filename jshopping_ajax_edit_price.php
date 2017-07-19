<?php
/**
* @version      1.0.0 April 2017
* @author       Igor Levoshkin
* @package      Jshopping
* @copyright    Copyright (C) 2017 Igor Levoshkin. All rights reserved.
* @license      GNU/GPL
*/
header('Content-type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache');
header('Expires: ' . date('r'));

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.helper');

define('JPATH_BASE', __DIR__ . '/../../../');
require_once  JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

class plgJshoppingJshopping_ajax_edit_price extends JPlugin
{
    public function __construct(&$subject, $config) 
	{
        parent::__construct($subject, $config);
    }
	
    public function onBeforeDisplayListProductsView(&$view)
	{

		JSFactory::loadLanguageFile();
		include(dirname(__FILE__)."/functions.php");
		include(dirname(__FILE__)."/lang/ru-RU/ru-RU.php");
	}
	
	
	public function onAjaxJshopping_ajax_edit_price()
	{
		require_once JPATH_BASE . '/../components/com_jshopping/lib/factory.php';
		include(dirname(__FILE__)."/lang/ru-RU/ru-RU.php");
		require_once JPATH_BASE . '/components/com_jshopping/models/categories.php';

        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
		$app = JFactory::getApplication();
		
		$product_id = $app->input->getInt('product_id');
		$manufacturer = $app->input->getString('manufacturer');
		$product_ean = $app->input->getString('product_ean');
		$short_description = $app->input->getString('short_description');
		$product_name = $app->input->getString('product_name');
		$parent = $app->input->getInt('parent');
		$manufacturer_id = $app->input->getInt('manufacturer_id');
		$categories_id = $app->input->get('categories_id', array(), 'array');
		$currencies = $app->input->getString('currencies');
		
		$product_price = $app->input->getString('product_price');
		$product_old_price = $app->input->getString('product_old_price');
		$currency_id = $app->input->getInt('currency_id');
		$unlimited = $app->input->getInt('unlimited');
		$product_quantity = $app->input->getString('product_qty');
		
		$id_from_attr = $app->input->getInt('id_from_attr');
		
		if($product_id && $product_id != 0){
			$db =& JFactory::getDbo();
			
			if($product_price){
				$query = "UPDATE `#__jshopping_products` SET product_price='".$product_price."', product_old_price='".$product_old_price."', min_price='".$product_price."', currency_id=".$currency_id.", product_quantity=".$product_quantity.", unlimited='".$unlimited."' WHERE product_id=" . $product_id;
				$db->setQuery($query)->execute();
				
				$query = "SELECT P.product_price, P.product_old_price, P.currency_id, P.product_quantity, P.unlimited, C.currency_code FROM `#__jshopping_products` as P LEFT JOIN `#__jshopping_currencies` as C ON P.currency_id=C.currency_id WHERE product_id=" . $product_id;
				$db->setQuery($query);
				$result = $db->loadObjectList();
				return $result;
			}else if($product_name && $short_description){
				$query = "UPDATE `#__jshopping_products` SET `".$lang->get('short_description')."`='" . $short_description . "', `".$lang->get('name')."`='" . $product_name . "' WHERE product_id=" . $product_id;
				$db->setQuery($query)->execute();
				
				$query = "SELECT `".$lang->get('short_description')."` as short_description, `".$lang->get('name')."` as name FROM `#__jshopping_products` WHERE product_id=" . $product_id;
				$db->setQuery($query);
				$result = $db->loadObjectList();
				return $result;
			}else if($product_ean){
				$query = "UPDATE `#__jshopping_products` SET product_ean='" . $product_ean . "' WHERE product_id=" . $product_id;
				$db->setQuery($query)->execute();
				
				$query = "SELECT product_ean FROM `#__jshopping_products` WHERE product_id=" . $product_id;
				$db->setQuery($query);
				$result = $db->loadObjectList();
				return $result;
			}else if($manufacturer_id){
				$query = "UPDATE `#__jshopping_products` SET product_manufacturer_id='" . $manufacturer_id . "' WHERE product_id=" . $product_id;
				$db->setQuery($query)->execute();
				
				$query = "SELECT M.`".$lang->get('name')."` as man_name, M.manufacturer_id, P.product_id FROM `#__jshopping_manufacturers` as M LEFT JOIN `#__jshopping_products` as P ON M.manufacturer_id=P.product_manufacturer_id WHERE P.product_id=" . $product_id. " ORDER BY P.product_id";
				$db->setQuery($query);
				$result = $db->loadObjectList();
				return $result;
			}else if($categories_id){
				$sql = "DELETE FROM `#__jshopping_products_to_categories` WHERE product_id=" . $product_id;
				$db->setQuery($sql)->execute();
				foreach($categories_id as $category_id){
					$query = "INSERT INTO `#__jshopping_products_to_categories` (product_id, category_id, product_ordering) VALUES (".$product_id.", ".(int)$category_id.", 1)";
					$db->setQuery($query)->execute();
				}
				
				$query = "SELECT C.`".$lang->get('name')."` as name FROM `#__jshopping_categories` as C LEFT JOIN `#__jshopping_products_to_categories` as PC ON C.category_id=PC.category_id WHERE product_id=".$product_id;
				$db->setQuery($query);
				$result = $db->loadObjectList();
				return $result;
			}else{
				$query = "SELECT P.product_ean, P.`".$lang->get('name')."` as name, P.`".$lang->get('short_description')."` as short_description, P.product_price, P.product_old_price, P.product_quantity, P.unlimited, C.currency_code FROM `#__jshopping_products` as P LEFT JOIN `#__jshopping_currencies` as C ON P.currency_id=C.currency_id WHERE P.product_id=" . $product_id;
				$db->setQuery($query);
				$result = $db->loadObjectList();
				return $result;
			}
		}
		
		if(isset($currencies)){
			$db =& JFactory::getDbo();
			$query = "SELECT currency_code, currency_id FROM `#__jshopping_currencies` WHERE currency_publish=1";
			$db->setQuery($query);
			return $db->loadObjectList();
		}

		if(isset($parent)){
			$db =& JFactory::getDbo();
			$query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id FROM `#__jshopping_categories` WHERE category_publish=1 ORDER BY category_id";
			$db->setQuery($query);
			$cats = $db->loadObjectList();
			return $cats;
			
		}
		
		if($manufacturer){
			$db =& JFactory::getDbo();
			$query = "SELECT `".$lang->get('name')."` as man_name, manufacturer_id FROM `" . $manufacturer . "` ORDER BY manufacturer_id";
			$db->setQuery($query);
			$man = $db->loadObjectList();
			return $man;
		}
		
 		if($id_from_attr){
			$db =& JFactory::getDbo();
			
			$query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id=".$id_from_attr;
			$db->setQuery($query);
			$all_pr_attr_id = $db->loadObjectList();
			
			foreach($all_pr_attr_id as $pr_attr_id){
				
				$res .= 'id-'.$pr_attr_id->product_attr_id.',';
				
				$sql = "SELECT * FROM `#__jshopping_attr`";
				$db->setQuery($sql);
				$all_attr_id = $db->loadObjectList();
				
				//выбираем атрибуты и значения
				foreach($all_attr_id as $attr_id){
					$field = "attr_".(int)$attr_id->attr_id;
					$sql_1 = "SELECT P.price, P.count, P.ean, P.old_price, P.product_attr_id, P.product_id, V.`name_ru-RU` as val_name, V.value_id, A.`name_ru-RU` as name
								FROM `#__jshopping_products_attr` as P 
								LEFT JOIN  `#__jshopping_attr_values` as V
								ON $field=V.value_id
								LEFT JOIN `#__jshopping_attr` as A 
								ON A.attr_id=V.attr_id
								WHERE P.product_id=".$id_from_attr." 
								AND P.product_attr_id=".$pr_attr_id->product_attr_id;
								
					$db->setQuery($sql_1);
					$rows = $db->loadObjectList();
				
					foreach($rows as $row){
						if($row->val_name != null)
							$res .= $row->name. '-' .$row->val_name;
					}
					if($row->val_name != null)
						$res .= ',';
				}
					$res .= sprintf(_JSHOP_QUANTITY . '-%d,' . _JSHOP_EAN_PRODUCT . '-%s,' . _JSHOP_PRICE . '-%.2f,'._JSHOP_OLD_PRICE.'-%.2f|', $pr_attr_id->count, $pr_attr_id->ean, $pr_attr_id->price, $pr_attr_id->old_price);
			}
			
			return $res;
		}
		
		$product_attr_id = $app->input->getInt('product_attr_id');
		$old_price = floatval($app->input->getString('old_price'));
		$price = floatval($app->input->getString('price'));
		$ean = $app->input->getString('ean');
		$qty = floatval($app->input->getString('qty'));
		
		if($product_attr_id){
			
			$db=& JFactory::getDBO();
			
			$query = "UPDATE `#__jshopping_products_attr` SET price='".$price."', count='".$qty."', ean='".$ean."', old_price='".$old_price."' WHERE product_attr_id=".$product_attr_id;
			$db->setQuery($query)->execute();
			
			$query = "SELECT product_id FROM `#__jshopping_products_attr` WHERE product_attr_id=".$product_attr_id;
			$db->setQuery($query);
			$rows = $db->loadObject();
				
				$query = "SELECT count FROM `#__jshopping_products_attr` WHERE product_id=".$rows->product_id;
				$db->setQuery($query);
				$counts = $db->loadObjectList();
				
				$product_quantity = 0;
				foreach($counts as $count){
					$product_quantity += floatval($count->count);
				}
				$query = "UPDATE `#__jshopping_products` SET product_quantity=".$product_quantity." WHERE product_id=".$rows->product_id;
				$db->setQuery($query)->execute();
				
				$query = "SELECT product_quantity, unlimited FROM `#__jshopping_products` WHERE product_id=".$rows->product_id;
				$db->setQuery($query);
				$res = $db->loadObject();
				
			    return $res;
				
		}
		
		$delete_attr = $app->input->getString('delete_attr');
		
		if($delete_attr){
			$db=& JFactory::getDBO();
			
			$sql = "DELETE FROM `#__jshopping_products_attr` WHERE product_attr_id=".$delete_attr;
			$db->setQuery($sql)->execute();
			return $delete_attr;
		}
	}
}