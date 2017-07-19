<?php
	include(dirname(__FILE__)."/lang/ru-RU/ru-RU.php");
?>
<script>
function getXmlHttpRequest()
{
	if (window.XMLHttpRequest)
	{
		try
		{
			return new XMLHttpRequest();
		}catch (e){}
	}else if (window.ActiveXObject)
	{
		try
		{
			return new ActiveXObject('Msxml2.XMLHTTP');
		}catch (e){}
		try
		{
			return new ActiveXObject('Microsoft.XMLHTTP');
		}catch (e){}
	}
	return null;
}

jQuery(function($){
	for(var j=1; j<$('table').find('tr').length-1; j++)
	{
		for(var i=0; i<$('table').find('tr').eq(0).find('th').length; i++)
		{
			$('table').find('tr').eq(j).find('td').eq(i).on('mouseenter', function(e){
				e.preventDefault();
				$(this).find('p').css({'display': 'block', 'width': '10px', 'height': '20px'})
			});
			
			$('table').find('tr').eq(j).find('td').eq(i).on('mouseleave', function(e){
				e.preventDefault();
				$('table').find('p').css({'display': 'none'})
			});
		
			if($('table').find('tr').eq(0).find('th').eq(i).is(':contains("<?php echo _JSHOP_PRICE;?>")'))
			{
				$('table').find('tr').eq(j).find('td').eq(i).attr('id', 'product_price');
				$('table').find('tr').eq(j).find('td').eq(i).append('<br /><p class="btn hasTooltip" style="display:none" onclick="showForm(this)" title="<?php echo _JSHOP_EDIT_PRICE;?>"><span class="icon-edit"></span></p>');
			}
			
			if($('table').find('tr').eq(0).find('th').eq(i).is(':contains("<?php echo _JSHOP_MANUFACTURER;?>")'))
			{
				$('table').find('tr').eq(j).children('td').eq(i).attr('id', 'man_name');
				$('table').find('tr').eq(j).children('td').eq(i).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEditManufacturers(this)" title="<?php echo _JSHOP_MANUFACTURER;?>"><span class="icon-edit"></span></p>');
			}
			
			if($('table').find('tr').eq(0).find('th').eq(i).is(':contains("<?php echo _JSHOP_CATEGORY;?>")'))
			{
				$('table').find('tr').eq(j).children('td').eq(i).attr('id', 'category');
				$('table').find('tr').eq(j).children('td').eq(i).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEditCategory(this)" title="<?php echo _JSHOP_CATEGORY;?>"><span class="icon-edit"></span></p>');
			}
			
			if($('table').find('tr').eq(0).find('th').eq(i).is(':contains("<?php echo _JSHOP_TITLE;?>")'))
			{
				$('table').find('tr').eq(j).children('td').eq(i).attr('id', 'short_desc');
				$('table').find('tr').eq(j).children('td').eq(i).children('div').eq(0).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormDesc(this)" title="<?php echo _JSHOP_TITLE . ", " . _JSHOP_SHORT_DESCRIPTION;?>"><span class="icon-edit"></span></p>');
			}
			
			if($('table').find('tr').eq(0).find('th').eq(i).is(':contains("<?php echo _JSHOP_EAN_PRODUCT;?>")'))
			{
				$('table').find('tr').eq(j).children('td').eq(i).attr('id', 'product_ean');
				$('table').find('tr').eq(j).children('td').eq(i).append('<br /><p class="btn hasTooltip" style="display:none"  onclick="showFormEan(this)" title="<?php echo _JSHOP_EAN_PRODUCT . ", " . _JSHOP_SHORT_DESCRIPTION;?>"><span class="icon-edit"></span></p>');
			}
			
			if($('table').find('tr').eq(0).find('th').eq(i).is(':contains("<?php echo _JSHOP_QUANTITY;?>")'))
			{
				$('table').find('tr').eq(j).children('td').eq(i).attr('id', 'product_qty');
			}
		}
	}
});

function showDiv(e)
{
	//позиция в окне
	var l = (window.innerWidth-800)/2;
	var t = (window.innerHeight-450)/2;
	
	//затемнение окна
	jQuery(e).parent().append('<div id="bg"></div>');
	jQuery('#bg').css({
						'display': 'none', 
						'width': window.innerWidth+'px', 
						'height': window.innerHeight+'px',
						'position': 'fixed', 
						'top': '0',
						'left': '0',
						'background': '#333',
						'opacity': '0.8'
						});
	jQuery('#bg').show()

	//блок формы
	jQuery(e).parent().append('<div id="div_form"><div id="close_form"><i class="icon-remove"></i></div></div>');
	jQuery('#div_form').css({
							'display': 'none',
							'overflow-y': 'auto',
							'padding': '20px',
							'width': '800px',
							'height': '450px',
							'position': 'fixed',
							'top': t+'px',
							'left': l+'px',
							'border': 'solid 1px #ccc',
							'border-radius': '5px',
							'background': '#fff',
							'box-shadow': '5px 5px 10px #000'
							});


	//кнопка зыкрыть
	jQuery('#close_form').css({
						'display': 'block', 
						'float': 'left',
						'position': 'fixed',
						'border': 'solid #ccc 2px', 
						'box-shadow': '2px 2px 7px #000',
						'border-radius': '18px', 
						'padding-left': '4px', 
						'padding-top': '4px', 
						'background': '#fff', 
						'width': '18px', 
						'height': '18px', 
						'cursor': 'pointer', 
						'top': (t-10)+'px', 
						'right': (l-70)+'px'
					});
	jQuery('#close_form').on('click', function(e){
							jQuery(this).remove();
							jQuery('#bg').remove();
							jQuery('#div_form').remove();
						});
	jQuery('#close_form').attr('title', '<?php echo _JSHOP_CLOSE;?>');
	jQuery('#div_form').show();
}

function closeForm(){
	jQuery('#close_form').remove();
	jQuery('#div_form').remove();
	jQuery('#bg').remove();
}

function showFormEditCategory(e)
{
	var e = e;
	showDiv(e);
	
	jQuery('#div_form').append('<form id="edit_category" action="#" onsubmit="return false"></form>');
	jQuery('<select id="sel_cats" size="15" multiple></select>').appendTo('#edit_category');
	jQuery('<option value="0" disabled><?php echo _JSHOP_SELECT;?></option>').appendTo('#edit_category select');
	jQuery('#edit_category').append('<br /><input class="btn hasTooltip" type="submit" onclick="editCategory(this)" value="<?php echo _JSHOP_SAVE;?>"/>');
	
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
					buildOptns(response.data[0], 0, '');
				}
			});

}

function buildOptns(cats, parent, indent){
	for(var i=0; i<cats.length; i++){
		if(cats[i].category_parent_id == parent){
			jQuery('#edit_category select').append('<option id="op'+i+'" value="'+cats[i].category_id+'">'+indent+cats[i].name+'</option>');
			
			var children = document.getElementById('sel_cats').parentNode.parentNode.parentNode.childNodes;
			for(var j=0; j<children.length; j++){
				if(children[j].nodeType == 3 && jQuery.trim(children[j].nodeValue).localeCompare(cats[i].name)==0)
					jQuery('#sel_cats option#op'+i).attr('selected', 'selected');
			}
			
			buildOptns(cats, cats[i].category_id, indent+'-- ');
		}
	}
}

function editCategory(e){
	
	var e = e;

	//получаем ID
	var td = e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;
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
					var br = e.parentNode.parentNode.parentNode.getElementsByTagName('br');
						for(var j=0; j<br.length; j++){
							e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.firstChild);
							e.parentNode.parentNode.parentNode.removeChild(br[0]);
						}

					for(var i=0; i<response.data[0].length; i++){
						e.parentNode.parentNode.parentNode.insertBefore(document.createTextNode(response.data[0][i].name), e.parentNode.parentNode.parentNode.getElementsByTagName('p')[0]);
						e.parentNode.parentNode.parentNode.insertBefore(document.createElement('br'), e.parentNode.parentNode.parentNode.getElementsByTagName('p')[0]);
					}

					//закрываем форму
					closeForm();
				}
			});
}

function showFormEditManufacturers(e)
{
	var e = e;
	showDiv(e);

	jQuery('#div_form').append('<form id="edit_manufacturer" action="#" onsubmit="return false"></form>');
	jQuery('<select></select>').appendTo('#edit_manufacturer');
	jQuery('<option value="0" disabled="disabled"><?php echo _JSHOP_SELECT;?></option>').appendTo('#edit_manufacturer select');
	jQuery('#edit_manufacturer').append('<br /><input class="btn hasTooltip" type="submit" onclick="editManufacturer(this)" value="<?php echo _JSHOP_SAVE;?>"/>');
	
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
					if(e.parentNode.firstChild.nodeValue.indexOf(value.man_name)!=-1)
						jQuery('option').attr('selected', 'selected');
				});
			}
		});
}

function editManufacturer(e)
{
	var e = e;

	//получаем ID
	var td = e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;
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
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.firstChild);
				e.parentNode.parentNode.parentNode.insertBefore(document.createTextNode(response.data[0][0].man_name), e.parentNode.parentNode.parentNode.getElementsByTagName('br')[0]);
				
				//закрываем форму
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('bg'))
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('div_form'))
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('close_form'))
			}
		});
}

function showFormDesc(e)
{
	var e = e;
	showDiv(e);
	
	//получаем ID
	var td = e.parentNode.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;

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

				var inputSub = jQuery('<br /><input type="submit" class="btn hasTooltip" onclick="editShortDesc(this)" value="<?php echo _JSHOP_SAVE;?>" title="<?php echo _JSHOP_SAVE_MODIFICATION;?>" />').appendTo('#edit_desc').val('<?php echo _JSHOP_SAVE;?>');
			}
		});
}

function editShortDesc(e)
{
	//получаем ID
	var td = e.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;
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
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.firstChild);
				e.parentNode.parentNode.parentNode.insertBefore(document.createTextNode(response.data[0][0].short_description), e.parentNode.parentNode.parentNode.getElementsByTagName('br')[0]);
				e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('b')[0].getElementsByTagName('a')[0].removeChild(e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('b')[0].getElementsByTagName('a')[0].firstChild);
				e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('b')[0].getElementsByTagName('a')[0].appendChild(document.createTextNode(response.data[0][0].name));
				
				//закрываем форму
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('bg'));
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('div_form'));
			}
		});	
} 

function showFormEan(e)
{
	var e = e ;
	showDiv(e);
	
	//получаем ID
	var td = e.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;

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
				jQuery('<br /><input type="submit" id="input_ean" class="btn hasTooltip" onclick="editEan(this)" value="<?php echo _JSHOP_SAVE;?>" title="<?php echo _JSHOP_SAVE_MODIFICATION;?>" />').appendTo('#edit_ean');
			}
		});
}

function editEan(e)
{
	//получаем ID
	var td = e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;
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
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.firstChild);
				e.parentNode.parentNode.parentNode.insertBefore(document.createTextNode(response.data[0][0].product_ean), e.parentNode.parentNode.parentNode.getElementsByTagName('br')[0]);
				
				//закрываем форму
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('bg'))
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('div_form'))
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.getElementById('close_form'))
			}
		});
}

function showForm(e)
{
	var e = e;
	showDiv(e);
	
	var td = e.parentNode.parentNode.getElementsByTagName('td');
	var product_id = td[td.length-1].firstChild.nodeValue;

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

				jQuery('<input type="submit" class = "btn hasTooltip" onclick = "editPriceAttr(this)" value="<?php echo _JSHOP_SAVE;?>" title = "<?php echo _JSHOP_SAVE_MODIFICATION;?>"/>').appendTo('#edit_price');
			}
		});
}

function showHideEnterQty(checked){
	if (checked){
		jQuery("#qty").hide();
		jQuery("#chk").attr("value", "1");
	}else{
		jQuery("#qty").show().css({'width': '80px', 'margin-left': '5px'}); //.attr('value', '1.00');
		jQuery("#chk").attr("value", "0");
	}
}

function editPrice(e)
{
	var e = e;
	var tds = e.parentNode.parentNode.parentNode.parentNode.getElementsByTagName("td");
	var product_id =  tds[tds.length-1].firstChild.nodeValue;
	
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
				e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode.parentNode.firstChild);
				e.parentNode.parentNode.parentNode.insertBefore(document.createTextNode(parseFloat(response.data[0][0].product_price).toFixed(2)+' '+response.data[0][0].currency_code), e.parentNode.parentNode.parentNode.firstChild);
				
				e.parentNode.parentNode.parentNode.parentNode.getElementById('product_qty').removeChild(e.parentNode.parentNode.parentNode.parentNode.getElementById('product_qty').firstChild);
				if(response.data[0][0].unlimited*1 == 1)
					e.parentNode.parentNode.parentNode.parentNode.getElementById('product_qty').appendChild(document.createTextNode('<?php echo _JSHOP_UNLIMITED;?>'));
				else
					e.parentNode.parentNode.parentNode.parentNode.getElementById('product_qty').appendChild(document.createTextNode(parseFloat(response.data[0][0].product_quantity)));

				//editPriceAttr(e);
				closeForm();
			}
		});
}

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

function showAttributes(e){
	var tds = e.parentNode.parentNode.getElementsByTagName("td");
	var id =  tds[tds.length-1].firstChild.nodeValue;

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

function editPriceAttr(e)
{
	var e = e;
	var trs = document.getElementById('t_attr').getElementsByTagName('tr');
	if(trs.length>0){
		for(var i=1; i<trs.length; i++)
		{
			var old_price = trs[i].lastChild.previousSibling.lastChild.value;
			var price = trs[i].lastChild.previousSibling.previousSibling.lastChild.value;
			var ean = trs[i].lastChild.previousSibling.previousSibling.previousSibling.lastChild.value;
			var qty = trs[i].lastChild.previousSibling.previousSibling.previousSibling.previousSibling.lastChild.value;
			var product_attr_id = trs[i].firstChild.firstChild.nodeValue;
			
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
			})
				.done(function(response){
					if(response.success && response.data)
					{
						e.previousSibling.previousSibling.setAttribute('value', response.data[0].product_quantity);
						
						editPrice(e);
						//закрываем форму
						//closeForm();
					}
				});
		}
	}else{
		editPrice(e);
	}
}

function deleteAttr(e){
	if(confirm('<?php print _JSHOP_DELETE?>?')){
		var delete_attr = e.parentNode.parentNode.getElementsByTagName('td')[0].firstChild.nodeValue;
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
					e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
			});
	}
}
</script>