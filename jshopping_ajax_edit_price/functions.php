<?php
/**
* @version      1.0.1 August 2017
* @author       Igor Levoshkin
* @package      Jshopping
* @copyright    Copyright (C) 2017 Igor Levoshkin. All rights reserved.
* @license      GNU/GPL
*/
	include(dirname(__FILE__)."/lang/ru-RU/ru-RU.php");
?>
<style>
#bg {
	display:none; 
	position:fixed; 
	top:0; left:0; 
	background:#333; 
	opacity:0.8; 
	width:100vw; 
	height:100vh; 
	z-index:990;
}

#div_form {
	display:none; 
	padding: 10px; 
	position:fixed; 
	width:80vw; 
	height:80vh; 
	background:#fff; 
	z-index:991; 
	left:10vw; 
	top:10vh; 
	overflow-y:auto; 
	overflow-x:visible; 
	border:solid 1px #ccc; 
	border-radius:5px; 
	-moz-border-radius:5px; 
	-o-border-radius:5px; 
	-webkit-border-radius:5px; 
	box-shadow:5px 5px 10px #000; 
	-moz-box-shadow:5px 5px 10px #000; 
	-o-box-shadow:5px 5px 10px #000; 
	-webkit-box-shadow:5px 5px 10px #000;
}

#close_form {
	display:block; 
	text-align:right; 
	height:20px; 
	width:100%; 
	z-index:992;
}
</style>
<div id="bg"></div>
<div id="div_form">
	<div id="close_form"><i class="icon-remove" style="cursor:pointer; width:20px; height:20px; background:#fff; border:solid 2px #000; border-radius:18px; -moz-border-radius:18px; -o-border-radius:18px; -webkit-border-radius:18px; text-align:center; line-height:1.5;"></i></div>
</div>
<script>
function getXmlHttpRequest()
{
	if (window.XMLHttpRequest){
		try{
			return new XMLHttpRequest();
		}catch (e){}
	}else if (window.ActiveXObject){
		try{
			return new ActiveXObject('Msxml2.XMLHTTP');
		}catch (e){}
		try
		{
			return new ActiveXObject('Microsoft.XMLHTTP');
		}catch (e){}
	}
	return null;
}


/**
* Всплывающий блок формы
*/
function showDiv()
{
	jQuery('#bg').show();
	jQuery('#close_form').on('click', function(e){
							jQuery('#bg').hide();
							jQuery('#div_form').hide();
							jQuery('#div_form form').remove();
						});
	jQuery('#close_form').attr('title', '<?php echo _JSHOP_CLOSE;?>');
	jQuery('#div_form').show();
}

jQuery(function($){
	var trs = $('table').find('tr'); //Строки в таблице
	
	for(var j=1; j<trs.length-1; j++)
	{
		var ths = trs.eq(0).find('th');
		for(var i=0; i<ths.length; i++)
		{
			var tds = trs.eq(j).find('td'); //Колонки в таблице
			
			/**
			* События для кнопок
			*/
			tds.eq(i).on('mouseenter', function(e){
				e.preventDefault();
				$(this).find('p').css({'display': 'block', 'width': '10px', 'height': '20px'})
			});
			
			tds.eq(i).on('mouseleave', function(e){
				e.preventDefault();
				$('table').find('p').css({'display': 'none'})
			});
		
			
			/**
			* Кнопка для редактирования цены
			*/
			if(ths.eq(i).is(':contains("<?php echo _JSHOP_PRICE;?>")'))
			{
				tds.eq(i).attr('id', 'product_price');
				tds.eq(i).append('<br /><p class="btn hasTooltip" style="display:none" onclick="showForm(this);" title="<?php echo _JSHOP_EDIT_PRICE;?>"><span class="icon-edit"></span></p>');
			}
			
			/**
			* Кнопка для редактирования производителя
			*/
			if(ths.eq(i).is(':contains("<?php echo _JSHOP_MANUFACTURER;?>")'))
			{
				tds.eq(i).attr('id', 'man_name');
				tds.eq(i).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEditManufacturers(this)" title="<?php echo _JSHOP_MANUFACTURER;?>"><span class="icon-edit"></span></p>');
			}
			
			/**
			* Кнопка для редактирования категорий
			*/
			if(ths.eq(i).is(':contains("<?php echo _JSHOP_CATEGORY;?>")'))
			{
				tds.eq(i).attr('id', 'category');
				tds.eq(i).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEditCategory(this)" title="<?php echo _JSHOP_CATEGORY;?>"><span class="icon-edit"></span></p>');
			}
			
			/**
			* Кнопка для редактирования названия и краткого описания
			*/
			if(ths.eq(i).is(':contains("<?php echo _JSHOP_TITLE;?>")'))
			{
				tds.eq(i).attr('id', 'short_desc');
				tds.eq(i).find('div').eq(0).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormDesc(this)" title="<?php echo _JSHOP_TITLE . ", " . _JSHOP_SHORT_DESCRIPTION;?>"><span class="icon-edit"></span></p>');
			}
			
			/**
			* Кнопка для редактирования кода товара
			*/
			if(ths.eq(i).is(':contains("<?php echo _JSHOP_EAN_PRODUCT;?>")'))
			{
				tds.eq(i).attr('id', 'product_ean');
				tds.eq(i).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEan(this)" title="<?php echo _JSHOP_EAN_PRODUCT;?>"><span class="icon-edit"></span></p>');
			}
			
			/**
			* Количество товара
			*/
			if(ths.eq(i).is(':contains("<?php echo _JSHOP_QUANTITY;?>")'))
			{
				tds.eq(i).attr('id', 'product_qty');
			}
		}
	}
});

/**
* Форма редактирования
*/
function showForm(e)
{
	var e = e; // кнопка в колонке таблицы
	showDiv();
	
	var tr = jQuery(e).parent().parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();

	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				var pr = parseFloat(response.data[0][0].product_price);
				var o_pr = parseFloat(response.data[0][0].product_old_price);
				//форма редактирования
				jQuery('<form id="edit_price" name="edit_price" action="#" onsubmit="return false"></fprm>').appendTo('#div_form');
				//цена
				jQuery('<span> <?php echo _JSHOP_PRICE;?>: </span>').appendTo('#edit_price');
				jQuery('<input type="text" id="input_price" style="width:80px" value="'+pr.toFixed(2)+'"/>').appendTo('#edit_price');
				
				//старая цена
				jQuery('<span> <?php echo _JSHOP_OLD_PRICE;?>: </span>').appendTo('#edit_price');
				jQuery('<input type="text" id="old_price" style="width:80px" value="'+o_pr.toFixed(2)+'"/>').appendTo('#edit_price');
				var cur_curr = response.data[0][0].currency_code;
				
				//валюта
				showCurrency(cur_curr);
				

				jQuery('<span> <?php echo _JSHOP_QUANTITY;?>: </span>').appendTo('#edit_price');
				jQuery('<input type="checkbox" id="chk" onclick="showHideEnterQty(this.checked)" value=""/><span> <?php echo _JSHOP_UNLIMITED;?></span>').appendTo('#edit_price');
				jQuery('<input type="text" id="qty" style="width:80px;margin-left:5px" value="'+parseFloat(response.data[0][0].product_quantity)+'"/>').appendTo('#edit_price');
									
				if(parseFloat(response.data[0][0].unlimited) == 1)
				{
					jQuery('#chk').attr('checked', 'checked');
					jQuery('#qty').css({'display': 'none'});
				}
				
				jQuery('<table class="table table-striped" id="t_attr"></table>').appendTo('#edit_price');
				showAttributes(e);

				jQuery('<input type="submit" class = "btn hasTooltip" value="<?php echo _JSHOP_SAVE;?>" title = "<?php echo _JSHOP_SAVE_MODIFICATION;?>"/>').appendTo('#edit_price').on('click', function(){editPriceAttr(e)});
			}
		});
}

/**
* Поле количества товаров (показать/скрыть)
*/
function showHideEnterQty(checked){
	if (checked){
		jQuery("#qty").hide();
		jQuery("#chk").attr("value", "1");
	}else{
		jQuery("#qty").show().css({'width': '80px', 'margin-left': '5px'}); //.attr('value', '1.00');
		jQuery("#chk").attr("value", "0");
	}
}

/**
* Редактируем цену, старую цену, валюту и количество
*/
function editPrice(e)
{
	var e = e; //кнопка в колонке таблицы
	var tr = jQuery(e).parent().parent();
	var td = jQuery(e).parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();
	
	var product_price =  jQuery('#input_price').val();
	var product_old_price = jQuery('#old_price').val();
	var currency_id = jQuery('#select_curr').val();
	var unlimited = jQuery('#chk').val();
	var product_qty = jQuery('#qty').val();

	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'product_price': product_price,
		'product_old_price': product_old_price,
		'currency_id': currency_id*1,
		'unlimited': unlimited*1,
		'product_qty': product_qty,
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				td.html(parseFloat(response.data[0][0].product_price).toFixed(2)+' '+response.data[0][0].currency_code+'<br /><p class="btn hasTooltip" style="display:none" onclick="showForm(this);" title="<?php echo _JSHOP_EDIT_PRICE;?>"><span class="icon-edit"></span></p>');
				
				tr.find('#product_qty').eq(0).remove(tr.find('#product_qty').eq(0).text());
				
				if(response.data[0][0].unlimited*1 == 1)
					tr.find('#product_qty').eq(0).text('<?php echo _JSHOP_UNLIMITED;?>');
				else
					tr.find('#product_qty').eq(0).text(parseFloat(response.data[0][0].product_quantity));

				//Close the form
				jQuery('#div_form').hide();
				jQuery('#div_form form').remove();
				jQuery('#bg').hide();
			}
		});
}

/**
* Выбор валюты
*/
function showCurrency(cur_curr){
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'currencies': 'currencies'
	}
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			if(response.success && response.data)
			{
				jQuery('<select id="select_curr"></select>').insertAfter('#old_price').css('width', '60px');
				jQuery('<span> <?php echo _JSHOP_CURRENCY_PARAMETERS;?>: </span>').insertBefore('#select_curr');
				
				for(var i=0; i<response.data[0].length; i++)
				{
					jQuery('#select_curr').append('<option value="'+response.data[0][i].currency_id+'">'+response.data[0][i].currency_code+'</option>');
					if(cur_curr.indexOf(response.data[0][i].currency_code)!=-1){
						jQuery('option').attr('selected', 'selected');
					}
				}
			}
		});
}

/**
* Таблица атрибутов
*/
function showAttributes(e){
	var e = e; //кнопка в колонке таблицы
	var tds = jQuery(e).parent().parent().find("td");
	var id =  tds.eq(tds.length-1).text();
	//параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'id_from_attr': id*1
	}
	
	//запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			if(response.success && response.data)
			{
				if(response.data.length>0)
				{
					var product_attr = String(response.data[0]);
					product_attr = product_attr.split('|');
					
					jQuery('#t_attr').append('<tr id="trh"></tr>');

					for(var i=1; i<product_attr[0].split(',').length; i++){
						jQuery('#trh').append('<th>'+product_attr[0].split(',')[i].split('-')[0]+'</th>');
					}
					
					//if(response.data[0].length>0)
					jQuery('#trh').append('<th><?php echo _JSHOP_DELETE;?></th>');
					
					for(var i=0; i<product_attr.length-1; i++){
						var parts = product_attr[i].split(',');
						jQuery('#t_attr').append('<tr id="row'+i+'"></tr>');
						for(var j=0; j<parts.length-4; j++){
							jQuery('#row'+i).append('<td>'+parts[j].split('-')[1]+'</td>');
						}
						
						jQuery('#row'+i).append('<td><input typy="text" value="'+parts[parts.length-4].split('-')[1]+'" style="width:60px;margin:0px;padding:1px;" /></td>');
						jQuery('#row'+i).append('<td><input typy="text" value="'+parts[parts.length-3].split('-')[1]+'" style="width:60px;margin:0px;padding:1px;" /></td>');
						jQuery('#row'+i).append('<td><input typy="text" value="'+parts[parts.length-2].split('-')[1]+'" style="width:60px;margin:0px;padding:1px;" /></td>');
						jQuery('#row'+i).append('<td><input typy="text" value="'+parts[parts.length-1].split('-')[1]+'" style="width:60px;margin:0px;padding:1px;" /></td>');
						jQuery('#row'+i).append('<td><button class="btn btn-micro" onclick="deleteAttr(this);" type="button" title="<?php print _JSHOP_DELETE;?>"><i class="icon-remove"></i></button></td>');
						jQuery('#row'+i).find('td').eq(0).css({'display': 'none'});
						
					}
				}
				
				if(document.getElementById('t_attr').getElementsByTagName('tr').length > 1){
					jQuery('#qty').attr('readonly', 'readonly');
					jQuery('#qty').attr('title', '<?php echo _JSHOP_INFO_PLEASE_EDIT_AMOUNT_FOR_ATTRIBUTE;?>');
				}
			}
		});
}

/**
* Редактируем атрибуты
*/
function editPriceAttr(e)
{
	var e = e; // кнопка в колонке таблицы
	var trs = jQuery('#t_attr').find('tr');
	if(trs.length>0){
		var product_quantity = 0;
		for(var i=1; i<trs.length; i++)
		{
			var tds = trs.eq(i).find('td');
			var old_price = tds.eq(tds.length-2).find('input').eq(0).val();
			var price = tds.eq(tds.length-3).find('input').eq(0).val();
			var ean = tds.eq(tds.length-4).find('input').eq(0).val();
			var qty = tds.eq(tds.length-5).find('input').eq(0).val();
			var product_attr_id = tds.eq(0).text();

			var request = {
				'option': 'com_ajax',
				'group': 'jshopping',
				'plugin': 'jshopping_ajax_edit_price',
				'format': 'json',
				'old_price': old_price,
				'price': price,
				'ean': ean,
				'qty': qty,
				'product_attr_id': product_attr_id*1
			}
			
			jQuery.ajax({
				type: 'POST',
				data: request
			});
			
			product_quantity += qty*1;
		}
		
		jQuery('#t_attr').parent().find('#qty').eq(0).val(product_quantity);

		editPrice(e);
		
	}else{
		editPrice(e);
	}
}

/**
* Удаляем атрибут
*/
function deleteAttr(e){
	if(confirm('<?php print _JSHOP_DELETE?>?')){
		var tr = e.parentNode.parentNode;
		var delete_attr = tr.getElementsByTagName('td')[0].firstChild.nodeValue;
		var request = {
			'option': 'com_ajax',
			'group': 'jshopping',
			'plugin': 'jshopping_ajax_edit_price',
			'format': 'json',
			'delete_attr': delete_attr
		}
		
		jQuery.ajax({
			type: 'POST',
			data: request
		})
			.done(function(response){
					tr.parentNode.removeChild(tr);
			});
	}
}

/**
* Закрытие формы
*/
function closeForm(){
	jQuery('#div_form').hide();
	jQuery('#div_form form').remove();
	jQuery('#bg').hide();
}

/**
* Форма редактирования категорий
*/
function showFormEditCategory(e)
{
	var e = e;
	showDiv();
	
	jQuery('#div_form').append('<form id="edit_category" action="#" onsubmit="return false"></form>');
	jQuery('<select id="sel_cats" size="15" multiple></select>').appendTo('#edit_category');
	jQuery('<option value="0" disabled><?php echo _JSHOP_SELECT;?></option>').appendTo('#edit_category select');
	jQuery('#edit_category').append('<br /><input class="btn hasTooltip" type="submit" value="<?php echo _JSHOP_SAVE;?>"/>');
	jQuery('#edit_category input').on('click', function(){editCategory(e)});
	
		// Формируем параметры запроса
		var request = {
			'option': 'com_ajax',
			'group': 'jshopping',
			'plugin': 'jshopping_ajax_edit_price',
			'format': 'json',
			'parent': parent*1
		};
		
		// Посылаем AJAX запрос
		jQuery.ajax({
			type: 'POST',
			data: request
		})
			.done(function(response){
				// Есть успешный ответ сервера и данные
				if (response.success && response.data) 
				{
					// Заполняем данными
					var cats = response.data[0];
					var parent = '0';
					var indent = '';
					buildOptns(e, response.data[0], 0, '');
				}
			});

}

/**
* Отображаем список категорий (<option>)
*/
function buildOptns(e, cats, parent, indent){
	for(var i=0; i<cats.length; i++){
		if(cats[i].category_parent_id == parent){
			jQuery('#edit_category select').append('<option id="op'+i+'" value="'+cats[i].category_id+'">'+indent+cats[i].name+'</option>');
			
			var category = jQuery(e).parent().contents().filter(function(){return this.nodeType === 3});
			for(var j=0; j<category.length; j++){
				if(jQuery.trim(jQuery(category[j]).text()).localeCompare(cats[i].name) == 0)
					jQuery('#sel_cats option#op'+i).attr('selected', 'selected');
			}
			buildOptns(e, cats, cats[i].category_id, indent+'-- '); 
		}
	}
	
}

/**
* Редактируем категории
*/
function editCategory(e){
	
	var e = e; 

	//получаем ID
	var tr = jQuery(e).parent().parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();
	var categories_id = jQuery('#edit_category select').val();

	// Формируем параметры запроса
		var request = {
			'option': 'com_ajax',
			'group': 'jshopping',
			'plugin': 'jshopping_ajax_edit_price',
			'format': 'json',
			'categories_id': categories_id,
			'product_id': product_id*1
		};
		
		// Посылаем AJAX запрос
		jQuery.ajax({
			type: 'POST',
			data: request
		})
			.done(function(response){
				// Есть успешный ответ сервера и данные
				if (response.success && response.data) 
				{
					// Меняем данными
					var category = '';
					for(var i=0; i<response.data[0].length; i++){
						category += response.data[0][i].name + '<br>';
					} 
					jQuery(e).parent().html(category + '<p class="btn hasTooltip" style="display:none"  onclick="showFormEditCategory(this)" title="<?php echo _JSHOP_CATEGORY;?>"><span class="icon-edit"></span></p>');

					//закрываем форму
					closeForm();
				}
			});
}

/**
* Форма редактирования производителя
*/
function showFormEditManufacturers(e)
{
	var e = e;
	showDiv();

	jQuery('#div_form').append('<form id="edit_manufacturer" action="#" onsubmit="return false"></form>');
	jQuery('<select></select>').appendTo('#edit_manufacturer');
	jQuery('<option value="0" disabled="disabled"><?php echo _JSHOP_SELECT;?></option>').appendTo('#edit_manufacturer select');
	jQuery('#edit_manufacturer').append('<br /><input class="btn hasTooltip" type="submit" value="<?php echo _JSHOP_SAVE;?>"/>');
	jQuery('#edit_manufacturer input').on('click', function(){editManufacturer(e)})
	
	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'manufacturer': '#__jshopping_manufacturers'
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				// Заполняем данными
				jQuery.each(response.data[0], function(index, value){
					jQuery('#edit_manufacturer select').append('<option value="'+value.manufacturer_id+'">'+value.man_name+'</option>');
					
					if(jQuery.trim(jQuery(e).parent().text()).localeCompare(value.man_name) == 0)
						jQuery('option').attr('selected', 'selected');
				});
			}
		});
}

/**
* Редактируем производителя
*/
function editManufacturer(e)
{
	var e = e;

	//получаем ID
	var tr = jQuery(e).parent().parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();
	var manufacturer_id = jQuery('#edit_manufacturer select').val();

	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'manufacturer_id': manufacturer_id*1,
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				// Меняем данными
				
				jQuery(e).parent().html(response.data[0][0].man_name + '<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEditManufacturers(this)" title="<?php echo _JSHOP_MANUFACTURER;?>"><span class="icon-edit"></span></p>')
				
				//закрываем форму
				closeForm();
			}
		});
}

/**
* Форма редактирования краткого описания и названия
*/
function showFormDesc(e)
{
	var e = e;
	showDiv();
	
	//получаем ID
	var tr = jQuery(e).parent().parent().parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();

	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				//форма редактирования краткого описания
				jQuery('<form id="edit_desc" name="edit_desc" action="#" onsubmit="return false"></form>').appendTo('#div_form');
				jQuery('<br /><input type="text" value="'+response.data[0][0].name+'"/>').appendTo('#edit_desc');
				jQuery('<br /><label><?php echo _JSHOP_SHORT_DESCRIPTION;?>: </label>').appendTo('#edit_desc');
				jQuery('<br /><textarea id="input_short_desc" rows="15" cols="100" style="width: 70%">'+ response.data[0][0].short_description+'</textarea>').appendTo('#edit_desc');

				var inputSub = jQuery('<br /><input type="submit" class="btn hasTooltip" value="<?php echo _JSHOP_SAVE;?>" title="<?php echo _JSHOP_SAVE_MODIFICATION;?>" />').appendTo('#edit_desc').val('<?php echo _JSHOP_SAVE;?>');
				inputSub.on('click', function(){editShortDesc(e)});
			}
		});
}

/**
* Редактируем краткое описание и название
*/
function editShortDesc(e)
{
	//получаем ID
	var tr = jQuery(e).parent().parent().parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();
	var short_description = jQuery('#edit_desc textarea').val();
	var product_name = jQuery('#edit_desc input').val();
	
	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'product_name': product_name,
		'short_description': short_description,
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				// Меняем данными
				var a = jQuery(e).parent().prev().find('a').eq(0);
				var name = a.text(response.data[0][0].name);
				
				jQuery(e).parent().html(response.data[0][0].short_description + '<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormDesc(this)" title="<?php echo _JSHOP_TITLE . ", " . _JSHOP_SHORT_DESCRIPTION;?>"><span class="icon-edit"></span></p>');
				
				//закрываем форму
				closeForm();
			}
		});	
} 

/**
* Форма редактирования кода товара
*/
function showFormEan(e)
{
	var e = e ;
	showDiv();
	
	//получаем ID
	var tds = jQuery(e).parent().parent().find('td');
	var product_id = tds.eq(tds.length-1).text();

	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				//форма редактирования
				jQuery('<form id="edit_ean" name="edit_ean" action="#" onsubmit="return false"></form>').appendTo('#div_form');
				jQuery('<br /><label><?php echo _JSHOP_EAN_PRODUCT;?>: </label>').appendTo('#edit_ean');
				jQuery('<br /><input type="text" id="input_ean" value="'+response.data[0][0].product_ean+'" />').appendTo('#edit_ean');
				jQuery('<br /><input type="submit" id="input_ean" class="btn hasTooltip" value="<?php echo _JSHOP_SAVE;?>" title="<?php echo _JSHOP_SAVE_MODIFICATION;?>" />').appendTo('#edit_ean');
				jQuery('#edit_ean input:last-child').on('click', function(){editEan(e)});
			}
		});
}

/**
* Редактируем код товара
*/
function editEan(e)
{
	//получаем ID
	var tr = jQuery(e).parent().parent();
	var tds = tr.find('td');
	var product_id = tds.eq(tds.length-1).text();
	var product_ean = jQuery('#edit_ean input').val();

	// Формируем параметры запроса
	var request = {
		'option': 'com_ajax',
		'group': 'jshopping',
		'plugin': 'jshopping_ajax_edit_price',
		'format': 'json',
		'product_ean': product_ean,
		'product_id': product_id*1
	};
	
	// Посылаем AJAX запрос
	jQuery.ajax({
		type: 'POST',
		data: request
	})
		.done(function(response){
			// Есть успешный ответ сервера и данные
			if (response.success && response.data) 
			{
				// Меняем данными
				jQuery(e).parent().html(response.data[0][0].product_ean + '<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEan(this)" title="<?php echo _JSHOP_EAN_PRODUCT;?>"><span class="icon-edit"></span></p>');

				//закрываем форму
				closeForm();
			}
		});
}
</script>