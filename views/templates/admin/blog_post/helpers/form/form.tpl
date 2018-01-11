
{if isset($fields.title)}<h3>{$fields.title}</h3>{/if}
{if isset($tabs) && $tabs|count}
<script type="text/javascript">
	var helper_tabs = {$tabs|json_encode};
	var unique_field_id = '';
</script>
{/if}
{block name="defaultForm"}
{if isset($identifier_bk) && $identifier_bk == $identifier}{capture name='identifier_count'}{counter name='identifier_count'}{/capture}{/if}
{assign var='identifier_bk' value=$identifier scope='parent'}
{if isset($table_bk) && $table_bk == $table}{capture name='table_count'}{counter name='table_count'}{/capture}{/if}
{assign var='table_bk' value=$table scope='parent'}
<form id="{if isset($fields.form.form.id_form)}{$fields.form.form.id_form|escape:'html':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table}_form{/if}{if isset($smarty.capture.table_count) && $smarty.capture.table_count}_{$smarty.capture.table_count|intval}{/if}{/if}" class="defaultForm form-horizontal{if isset($name_controller) && $name_controller} {$name_controller}{/if}"{if isset($current) && $current} action="{$current|escape:'html':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}"{/if} method="post" enctype="multipart/form-data"{if isset($style)} style="{$style}"{/if} novalidate>
	{if $form_id}
		<input type="hidden" name="{$identifier}" id="{$identifier}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}" value="{$form_id}" />
	{/if}
	{if !empty($submit_action)}
		<input type="hidden" name="{$submit_action}" value="1" />
	{/if}
	{foreach $fields as $f => $fieldset}
		{block name="fieldset"}
		{capture name='fieldset_name'}{counter name='fieldset_name'}{/capture}
		<div class="panel" id="fieldset_{$f}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}{if $smarty.capture.fieldset_name > 1}_{($smarty.capture.fieldset_name - 1)|intval}{/if}">
			{foreach $fieldset.form as $key => $field}
				
				{if $key == 'gallary'}
				
		

				 <div id="product-images" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Images" />	
	<div class="panel-heading tab" >
		{l s='Images'}
	 
	</div>
 
			 {if  isset($id_smart_blog_post) &&   ($id_smart_blog_post>0)}
 				
	<div class="row">
		<div class="form-group">
			<label class="control-label col-lg-3 file_upload_label">
				<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Format:'} JPG, GIF, PNG. {l s='Filesize:'} {$max_image_size|string_format:"%.2f"} {l s='MB max.'}">
					{if isset($id_image)}{l s='Edit this gallery\'s image:'}{else}{l s='Add a new image to this Gallery'}{/if}
				</span>
			</label>
			<div class="col-lg-9">
				{$image_uploader}
				
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
				<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Invalid characters:'} <>;=#{}">
					{l s='Caption'}
				</span>			
			</label>
			<div class="col-lg-9">
			{foreach from=$languages item=language}
				{if $languages|count > 1}
				<div class="translatable-field row lang-{$language.id_lang}">
					<div class="col-lg-6">
				{/if}
						 <input type="text"
						id="legend_{$language.id_lang}"
						{if isset($input_class)}class="{$input_class}"{/if}
						name="legend_{$language.id_lang}"
						value=""
					 />
				{if $languages|count > 1}
					</div>
					<div class="col-lg-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
							{$language.iso_code}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
							<li>
								<a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
							</li>
							{/foreach}
						</ul>
					</div>
				</div>
				{/if}
			{/foreach}
			</div>
		</div>
	</div>
	<table class="table tableDnD" id="imageTable">
		<thead>
			<tr class="nodrag nodrop"> 
				<th class="fixed-width-lg"><span class="title_box">{l s='Image'}</span></th>
				<th class="fixed-width-lg"><span class="title_box">{l s='Caption'}</span></th>
				<th class="fixed-width-xs"><span class="title_box">{l s='Position'}</span></th>
 
			 
				<th></th> <!-- action -->
			</tr>
		</thead>
		<tbody id="imageList">
		</tbody>
	</table>
	<table id="lineType" style="display:none;">
		<tr id="image_id">
			<td>				
				<a href="{$gallary_path}image_path.jpg" class="fancybox">                                    
					<img
						src="{$gallary_path}{$iso_lang}-default-{$imageType}.jpg"
						alt="legend"
						title="legend"
						class="img-thumbnail" />
				</a>
			</td>
			<td>legend</td>
			<td id="td_image_id" class="pointer dragHandle center positionImage">
				<div class="dragGroup">
					<div class="positions">
						image_position
                                        </div>
                                </div>
			</td>
 
			 
			<td>
				<a href="#" class="delete_product_image pull-right btn btn-default" >
					<i class="icon-trash"></i> {l s='Delete this image'}
				</a>
			</td>
		</tr>
	</table>
 
	<script type="text/javascript">
		var upbutton = '{l s='Upload an image'}';
		var come_from = '{$table}';
		var success_add =  '{l s='The image has been successfully added.'}';
		var id_tmp = 0;
		 
		{literal}
		//Ready Function

		function imageLine(id, path, position, cover, shops, legend)
		{
			line = $("#lineType").html();
		 

			line = line.replace(/image_id/g, id);
			line = line.replace(/(\/)?[a-z]{0,2}-default/g, function($0, $1){
				return $1 ? $1 + path : $0;
			});

			line = line.replace(/image_path/g, path);
			line = line.replace(/image_position/g, position);
			line = line.replace(/legend/g, legend);
			line = line.replace(/icon-check-empty/g, cover);
			line = line.replace(/<tbody>/gi, "");
			line = line.replace(/<\/tbody>/gi, "");
			if (shops != false)
			{
				$.each(shops, function(key, value){
					if (value == 1)
						line = line.replace('id="' + key + '' + id + '"','id="' + key + '' + id + '" checked=checked');
				});
			}      
                        
			$("#imageList").append(line);
		}

		$(document).ready(function(){
			{/literal}
			{foreach from=$images item=image}
				assoc = {literal}"{"{/literal};
 
				if (assoc != {literal}"{"{/literal})
				{
					assoc = assoc.slice(0, -1);
					assoc += {literal}"}"{/literal};
					assoc = jQuery.parseJSON(assoc);
				}
				else
					assoc = false;
				imageLine({$image->id}, "{$image->getExistingImgPath()}", {$image->position}, "{if $image->cover}icon-check-sign{else}icon-check-empty{/if}", assoc, "{$image->legend[$default_language]|@addcslashes:'\"'}");
		
			{/foreach}
			{literal}
			var originalOrder = false;

			$("#imageTable").tableDnD(
			{	dragHandle: 'dragHandle',
                                onDragClass: 'myDragClass',
                                onDragStart: function(table, row) {
                                        originalOrder = $.tableDnD.serialize();
                                        reOrder = ':even';
                                        if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
                                                reOrder = ':odd';
                                        $(table).find('#' + row.id).parent('tr').addClass('myDragClass');
                                },
				onDrop: function(table, row) {
					if (originalOrder != $.tableDnD.serialize()) {
						current = $(row).attr("id");
						stop = false;
						image_up = "{";
						$("#imageList").find("tr").each(function(i) {
							$("#td_" +  $(this).attr("id")).html('<div class="dragGroup"><div class="positions">'+(i + 1)+'</div></div>');
							if (!stop || (i + 1) == 2)
								image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
						});
						image_up = image_up.slice(0, -1);
						image_up += "}";
						updateImagePosition(image_up);
					}
				}
			});
			/**
			 * on success function 
			 */
			function afterDeleteGallaryImage(data)
			{
				//console.log(data);
				data = $.parseJSON(data);
				if (data)
				{
					cover = 0;
					id = data.content.id;
					if (data.status == 'ok')
					{
						 
						$("#" + id).remove();
					}
					 
					$("#countImage").html(parseInt($("#countImage").html()) - 1);
					refreshImagePositions($("#imageTable"));
					showSuccessMessage(data.confirmations);
				}
			}

			$('.delete_product_image').die().live('click', function(e)
			{
				e.preventDefault();
				id = $(this).parent().parent().attr('id');
				if (confirm("{/literal}{l s='Are you sure?' js=1}{literal}"))
				doAdminAjax({
						"action":"deleteGallaryImage",
						"id_smart_blog_gallary_images":id,
						"id_smart_blog_post" : {/literal}{$id_smart_blog_post}{literal},
						 "token" : "{/literal}{$token}{literal}",
						"tab" : "AdminBlogPost",
						"ajax" : 1 }, afterDeleteGallaryImage
				);
			});
					/**
		 * Update the product image list position buttons
		 *
		 * @param DOM table imageTable
		 */
		function refreshImagePositions(imageTable)
		{
			var reg = /_[0-9]$/g;
			var up_reg  = new RegExp("imgPosition=[0-9]+&");

			imageTable.find("tbody tr").each(function(i,el) {
				$(el).find("td.positionImage").html(i + 1);
			});
			imageTable.find("tr td.dragHandle a:hidden").show();
			imageTable.find("tr td.dragHandle:first a:first").hide();
			imageTable.find("tr td.dragHandle:last a:last").hide();
		}
			 
			
			$('.image_shop').die().live('click', function()
			{
				active = false;
				if ($(this).attr("checked"))
					active = true;
				id = $(this).parent().parent().attr('id');
				id_shop = $(this).attr("id").replace(id, "");
				doAdminAjax(
				{
					"action":"UpdateProductImageShopAsso",
					"id_image":id,
					"id_smart_blog_post":id_smart_blog_post,
					 "active":active,
					"token" : "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
					"tab" : "AdminBlogPost",
					"ajax" : 1 
				});
			});
			
			function updateImagePosition(json)
			{
				doAdminAjax(
				{
					"action":"UpdateGallaryImagePosition",
					"json":json,
					"token" : "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
					"tab" : "AdminBlogPost",
					"ajax" : 1
				});
			}
			
			function delQueue(id)
			{
				$("#img" + id).fadeOut("slow");
				$("#img" + id).remove();
			}
			
			
			$('.fancybox').fancybox();
		});

		hideOtherLanguage(default_language);
		{/literal}
	</script>
		{else}
	<div class="alert alert-warning">  {l s='You must save this post before adding images.'}  </div>
 
			
	{/if} 	 {* end of post image gallary empty message*}	 

</div>	 	
	
	{/if}
{* end of post image gallary*}
				{if $key == 'legend'}


					{block name="legend"}
						<div class="panel-heading">
							{if isset($field.image) && isset($field.title)}<img src="{$field.image}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
							{if isset($field.icon)}<i class="{$field.icon}"></i>{/if}
							{$field.title}
						</div>
					{/block}
				{elseif $key == 'description' && $field}
					<div class="alert alert-info">{$field}</div>
				{elseif $key == 'input'}
					<div class="form-wrapper">
					{foreach $field as $input}
						{block name="input_row"}
						<div class="form-group{if isset($input.form_group_class)} {$input.form_group_class}{/if}{if $input.type == 'hidden'} hide{/if}"{if $input.name == 'id_state'} id="contains_states"{if !$contains_states} style="display:none;"{/if}{/if}{if isset($tabs) && isset($input.tab)} data-tab-id="{$input.tab}"{/if}>
						{if $input.type == 'hidden'}
							<input type="hidden" name="{$input.name}" id="{$input.name}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
						{else}
							{block name="label"}
								{if isset($input.label)}
									<label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
										{if isset($input.hint)}
										<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
													{foreach $input.hint as $hint}
														{if is_array($hint)}
															{$hint.text|escape:'html':'UTF-8'}
														{else}
															{$hint|escape:'html':'UTF-8'}
														{/if}
													{/foreach}
												{else}
													{$input.hint|escape:'html':'UTF-8'}
												{/if}">
										{/if}
										{$input.label}
										{if isset($input.hint)}
										</span>
										{/if}
									</label>
								{/if}
							{/block}

							{block name="field"}
								<div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if} {if !isset($input.label)}col-lg-offset-3{/if}">

								{block name="input"}
								{if $input.type == 'text' || $input.type == 'tags'}
									{if isset($input.lang) AND $input.lang}
									{if $languages|count > 1}
									<div class="form-group">
									{/if}
									{foreach $languages as $language}
										{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
										{if $languages|count > 1}
										<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
											<div class="col-lg-9">
										{/if}

												{if $input.type == 'tags'}
													{literal}
														<script type="text/javascript">
															$().ready(function () {
																var input_id = '{/literal}{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}{literal}';
																$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
																$({/literal}'#{$table}{literal}_form').submit( function() {
																	$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
																});
															});
														</script>
													{/literal}
												{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												<div class="input-group{if isset($input.class)} {$input.class}{/if}">
												{/if}
												{if isset($input.maxchar)}
												<span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
													<span class="text-count-down">{$input.maxchar}</span>
												</span>
												{/if}
												{if isset($input.prefix)}
													<span class="input-group-addon">
													  {$input.prefix}
													</span>
													{/if}
												<input type="text"
													id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
													name="{$input.name}_{$language.id_lang}"
													class="{if isset($input.class)}{$input.class}{/if}{if $input.type == 'tags'} tagify{/if}"
													value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
													onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
													{if isset($input.size)} size="{$input.size}"{/if}
													{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
													{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
													{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
													{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
													{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
													{if isset($input.required) && $input.required} required="required" {/if}
													{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
													{if isset($input.suffix)}
													<span class="input-group-addon">
													  {$input.suffix}
													</span>
													{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												</div>
												{/if}
										{if $languages|count > 1}
											</div>
											<div class="col-lg-2">
												<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
													{$language.iso_code}
													<i class="icon-caret-down"></i>
												</button>
												<ul class="dropdown-menu">
													{foreach from=$languages item=language}
													<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
													{/foreach}
												</ul>
											</div>
										</div>
										{/if}
									{/foreach}
									{if isset($input.maxchar)}
									<script type="text/javascript">
									function countDown($source, $target) {
										var max = $source.attr("data-maxchar");
										$target.html(max-$source.val().length);

										$source.keyup(function(){
											$target.html(max-$source.val().length);
										});
									}

									$(document).ready(function(){
									{foreach from=$languages item=language}
										countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
									{/foreach}
									});
									</script>
									{/if}
									{if $languages|count > 1}
									</div>
									{/if}
									{else}
										{if $input.type == 'tags'}
											{literal}
											<script type="text/javascript">
												$().ready(function () {
													var input_id = '{/literal}{if isset($input.id)}{$input.id}{else}{$input.name}{/if}{literal}';
													$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
													$({/literal}'#{$table}{literal}_form').submit( function() {
														$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
													});
												});
											</script>
											{/literal}
										{/if}
										{assign var='value_text' value=$fields_value[$input.name]}
										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										<div class="input-group{if isset($input.class)} {$input.class}{/if}">
										{/if}
										{if isset($input.maxchar)}
										<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar}</span></span>
										{/if}
										{if isset($input.prefix)}
										<span class="input-group-addon">
										  {$input.prefix}
										</span>
										{/if}
										<input type="text"
											name="{$input.name}"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
											class="{if isset($input.class)}{$input.class}{/if}{if $input.type == 'tags'} tagify{/if}"
											{if isset($input.size)} size="{$input.size}"{/if}
											{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
											{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
											{if isset($input.required) && $input.required} required="required" {/if}
											{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if}
											/>
										{if isset($input.suffix)}
										<span class="input-group-addon">
										  {$input.suffix}
										</span>
										{/if}
										
										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										</div>
										{/if}
										{if isset($input.maxchar)}
										<script type="text/javascript">
										function countDown($source, $target) {
											var max = $source.attr("data-maxchar");
											$target.html(max-$source.val().length);

											$source.keyup(function(){
												$target.html(max-$source.val().length);
											});
										}
										$(document).ready(function(){
											countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
										});
										</script>
										{/if}
									{/if}
								{elseif $input.type == 'blog_post_type'}
									{*I am blog_post_type*}
									{foreach $input.values as $value}
										<div class="radio {if isset($input.class)}{$input.class}{/if}">
											{strip}
											<label>
											<input type="radio"	name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
												<i class="{$value.icon}"></i> {$value.label}
											</label>
											{/strip}
										</div>
										{if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
									{/foreach}


								{elseif $input.type == 'associations'}
								 
								<div class="col-lg-5">
								<input type="hidden" name="{$input.name}" id="inputAccessories" value="{foreach from=$accessories item=accessory}{$accessory.id_product}-{/foreach}" />
								<input type="hidden" name="nameAccessories" id="nameAccessories" value="{foreach from=$accessories item=accessory}{$accessory.name|escape:'html':'UTF-8'}¤{/foreach}" />
								<div id="ajax_choose_product">
									<div class="input-group">
										<input type="text" id="product_autocomplete_input" name="product_autocomplete_input"/>
										<span class="input-group-addon"><i class="icon-search"></i></span>
									</div>
								</div>
							<div id="divAccessories">
								{foreach from=$accessories item=accessory} 
								<div class="form-control-static">
									<button type="button" class="btn btn-default delAccessory" name="{$accessory.id_product}">
										<i class="icon-remove text-danger"></i>
									</button>
									{$accessory.name|escape:'html':'UTF-8'}{if !empty($accessory.reference)}&nbsp;{l s='(ref: %s)' sprintf=$accessory.reference}{/if}
								</div>
								{/foreach}
								</div>
							</div>
								
<script type="text/javascript">
$(document).ready(function(){  

	var id_product = $('input[name=id_product]').first().val();

	$('#product_autocomplete_input')
			.autocomplete('ajax_products_list.php', {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:false,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
					return item[1]+' - '+item[0];
				}
			}).result( addAccessory);
			$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : getAccessoriesIds()
			}
		});
		function delAccessory (id)
	{
		var div = getE('divAccessories');
		var input = getE('inputAccessories');
		var name = getE('nameAccessories');

		// Cut hidden fields in array
		var inputCut = input.value.split('-');
		var nameCut = name.value.split('¤');

		if (inputCut.length != nameCut.length)
			return jAlert('Bad size');

		// Reset all hidden fields
		input.value = '';
		name.value = '';
		div.innerHTML = '';
		for (i in inputCut)
		{
			// If empty, error, next
			if (!inputCut[i] || !nameCut[i])
				continue ;

			// Add to hidden fields no selected products OR add to select field selected product
			if (inputCut[i] != id)
			{
				input.value += inputCut[i] + '-';
				name.value += nameCut[i] + '¤';
				div.innerHTML += '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + inputCut[i] +'"><i class="icon-remove text-danger"></i></button>&nbsp;' + nameCut[i] + '</div>';
			}
			else
				$('#selectAccessories').append('<option selected="selected" value="' + inputCut[i] + '-' + nameCut[i] + '">' + inputCut[i] + ' - ' + nameCut[i] + '</option>');
		}

		$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : getAccessoriesIds()
			}
		});
	};
	
		$('#divAccessories').delegate('.delAccessory', 'click', function(){
			delAccessory($(this).attr('name'));
		});
		
	/*function addAccessory()	
	{
		console.log("i am fire addAccessory");

	}*/
function getAccessoriesIds()	{
	
		if ($('#inputAccessories').val() === undefined)
			return id_product;
		return id_product + ',' + $('#inputAccessories').val().replace(/\-/g,',');
	}
	function addAccessory(event, data, formatted)
	{
		if (data == null)
			return false;
		var productId = data[1];
		var productName = data[0];

		var $divAccessories = $('#divAccessories');
		var $inputAccessories = $('#inputAccessories');
		var $nameAccessories = $('#nameAccessories');

		/* delete product from select + add product line to the div, input_name, input_ids elements */
		$divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;'+ productName +'</div>');
		$nameAccessories.val($nameAccessories.val() + productName + '¤');
		$inputAccessories.val($inputAccessories.val() + productId + '-');
		$('#product_autocomplete_input').val('');
		$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds:getAccessoriesIds()
			}
		});
	};

	
	});


			</script>

	

								{elseif $input.type == 'textbutton'}
									{assign var='value_text' value=$fields_value[$input.name]}
									<div class="row">
										<div class="col-lg-9">
										{if isset($input.maxchar)}
										<div class="input-group">
											<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar}</span>
											</span>
										{/if}
										<input type="text"
											name="{$input.name}"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
											class="{if isset($input.class)}{$input.class}{/if}{if $input.type == 'tags'} tagify{/if}"
											{if isset($input.size)} size="{$input.size}"{/if}
											{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
											{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
											{if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
											/>
										{if isset($input.suffix)}{$input.suffix}{/if}
										{if isset($input.maxchar)}
										</div>
										{/if}
										</div>
										<div class="col-lg-2">
											<button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']}{/if}{if isset($input.button.class)} {$input.button.class}{/if}"
												{foreach from=$input.button.attributes key=name item=value}
													{if $name|lower != 'class'}
													 {$name|escape:'html':'UTF-8'}="{$value|escape:'html':'UTF-8'}"
													{/if}
												{/foreach} >
												{$input.button.label}
											</button>
										</div>
									</div>
									{if isset($input.maxchar)}
									<script type="text/javascript">
										function countDown($source, $target) {
											var max = $source.attr("data-maxchar");
											$target.html(max-$source.val().length);
											$source.keyup(function(){
												$target.html(max-$source.val().length);
											});
										}
										$(document).ready(function() {
											countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
										});
									</script>
									{/if}
								{elseif $input.type == 'select'}
									{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
										{$input.empty_message}
										{$input.required = false}
										{$input.desc = null}
									{else}
										<select name="{$input.name|escape:'html':'utf-8'}"
												class="{if isset($input.class)}{$input.class|escape:'html':'utf-8'}{/if} fixed-width-xl"
												id="{if isset($input.id)}{$input.id|escape:'html':'utf-8'}{else}{$input.name|escape:'html':'utf-8'}{/if}"
												{if isset($input.multiple)}multiple="multiple" {/if}
												{if isset($input.size)}size="{$input.size|escape:'html':'utf-8'}"{/if}
												{if isset($input.onchange)}onchange="{$input.onchange|escape:'html':'utf-8'}"{/if}>
											{if isset($input.options.default)}
												<option value="{$input.options.default.value|escape:'html':'utf-8'}">{$input.options.default.label|escape:'html':'utf-8'}</option>
											{/if}
											{if isset($input.options.optiongroup)}
												{foreach $input.options.optiongroup.query AS $optiongroup}
													<optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
														{foreach $optiongroup[$input.options.options.query] as $option}
															<option value="{$option[$input.options.options.id]}"
																{if isset($input.multiple)}
																	{foreach $fields_value[$input.name] as $field_value}
																		{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
																	{/foreach}
																{else}
																	{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
																{/if}
															>{$option[$input.options.options.name]}</option>
														{/foreach}
													</optgroup>
												{/foreach}
											{else}
												{foreach $input.options.query AS $option}
													{if is_object($option)}
														<option value="{$option->$input.options.id}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option->$input.options.id}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option->$input.options.id}
																	selected="selected"
																{/if}
															{/if}
														>{$option->$input.options.name}</option>
													{elseif $option == "-"}
														<option value="">-</option>
													{else}
														<option value="{$option[$input.options.id]}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option[$input.options.id]}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option[$input.options.id]}
																	selected="selected"
																{/if}
															{/if}
														>{$option[$input.options.name]}</option>

													{/if}
												{/foreach}
											{/if}
										</select>
									{/if}
								{elseif $input.type == 'radio'}
									{foreach $input.values as $value}
										<div class="radio {if isset($input.class)}{$input.class}{/if}">
											{strip}
											<label>
											<input type="radio"	name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
												{$value.label}
											</label>
											{/strip}
										</div>
										{if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
									{/foreach}
								{elseif $input.type == 'switch'}
									<span class="switch prestashop-switch fixed-width-lg">
										{foreach $input.values as $value}
										<input type="radio" name="{$input.name}"{if $value.value == 1} id="{$input.name}_on"{else} id="{$input.name}_off"{/if} value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
										{strip}
										<label {if $value.value == 1} for="{$input.name}_on"{else} for="{$input.name}_off"{/if}>
											{if $value.value == 1}
												{l s='Yes'}
											{else}
												{l s='No'}
											{/if}
										</label>
										{/strip}
										{/foreach}
										<a class="slide-button btn"></a>
									</span>
								{elseif $input.type == 'textarea'}
									{assign var=use_textarea_autosize value=true}
									{if isset($input.lang) AND $input.lang}
									{foreach $languages as $language}
									{if $languages|count > 1}
									<div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
										<div class="col-lg-9">
									{/if}
											<textarea name="{$input.name}_{$language.id_lang}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{if isset($input.class)} {$input.class}{/if}{else}{if isset($input.class)} {$input.class}{else} textarea-autosize{/if}{/if}">{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
									{if $languages|count > 1}	
										</div>
										<div class="col-lg-2">
											<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
												{$language.iso_code}
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												{foreach from=$languages item=language}
												<li>
													<a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
												</li>
												{/foreach}
											</ul>
										</div>
									</div>
									{/if}
									{/foreach}

									{else}
										<textarea name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" {if isset($input.cols)}cols="{$input.cols}"{/if} {if isset($input.rows)}rows="{$input.rows}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{if isset($input.class)} {$input.class}{/if}{else} textarea-autosize{/if}">{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
									{/if}

								{elseif $input.type == 'checkbox'}
									{if isset($input.expand)}
										<a class="btn btn-default show_checkbox{if strtolower($input.expand.default) == 'hide'} hidden{/if}" href="#">
											<i class="icon-{$input.expand.show.icon}"></i>
											{$input.expand.show.text}
											{if isset($input.expand.print_total) && $input.expand.print_total > 0}
												<span class="badge">{$input.expand.print_total}</span>
											{/if}
										</a>
										<a class="btn btn-default hide_checkbox{if strtolower($input.expand.default) == 'show'} hidden{/if}" href="#">
											<i class="icon-{$input.expand.hide.icon}"></i>
											{$input.expand.hide.text}
											{if isset($input.expand.print_total) && $input.expand.print_total > 0}
												<span class="badge">{$input.expand.print_total}</span>
											{/if}
										</a>
									{/if}
									{foreach $input.values.query as $value}
										{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
										<div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
											{strip}
												<label for="{$id_checkbox}">
													<input type="checkbox" name="{$id_checkbox}" id="{$id_checkbox}" class="{if isset($input.class)}{$input.class}{/if}"{if isset($value.val)} value="{$value.val|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
													{$value[$input.values.name]}
												</label>
											{/strip}
										</div>
									{/foreach}
								{elseif $input.type == 'change-password'}
									<div class="row">
										<div class="col-lg-12">
											<button type="button" id="{$input.name}-btn-change" class="btn btn-default">
												<i class="icon-lock"></i>
												{l s='Change password...'}
											</button>
											<div id="{$input.name}-change-container" class="form-password-change well hide">
												<div class="form-group">
													<label for="old_passwd" class="control-label col-lg-2 required">
														{l s='Current password'}
													</label>
													<div class="col-lg-10">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-unlock"></i>
															</span>
															<input type="password" id="old_passwd" name="old_passwd" class="form-control" value="" required="required" autocomplete="off">
														</div>
													</div>
												</div>
												<hr />
												<div class="form-group">
													<label for="{$input.name}" class="required control-label col-lg-2">
														<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Password should be at least 8 characters long.">						
															{l s='New password'}
														</span>
													</label>
													<div class="col-lg-9">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-key"></i>
															</span>
															<input type="password" id="{$input.name}" name="{$input.name}" class="{if isset($input.class)}{$input.class}{/if}" value="" required="required" autocomplete="off"/>
														</div>
														<span id="{$input.name}-output"></span>
													</div>
												</div>
												<div class="form-group">
													<label for="{$input.name}2" class="required control-label col-lg-2">
														{l s='Confirm password'}
													</label>
													<div class="col-lg-4">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-key"></i>
															</span>
															<input type="password" id="{$input.name}2" name="{$input.name}2" class="{if isset($input.class)}{$input.class}{/if}" value="" autocomplete="off"/>
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-lg-10 col-lg-offset-2">
														<input type="text" class="form-control fixed-width-md pull-left" id="{$input.name}-generate-field" disabled="disabled">
														<button type="button" id="{$input.name}-generate-btn" class="btn btn-default">
															<i class="icon-random"></i>
															{l s='Generate password'}
														</button>
													</div>
												</div>
												<div class="form-group">
													<div class="col-lg-10 col-lg-offset-2">
														<p class="checkbox">
															<label for="{$input.name}-checkbox-mail">
																<input name="passwd_send_email" id="{$input.name}-checkbox-mail" type="checkbox" checked="checked">
																{l s='Send me this new password by Email'}
															</label>
														</p>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12">
														<button type="button" id="{$input.name}-cancel-btn" class="btn btn-default">
															<i class="icon-remove"></i>
															{l s='Cancel'}
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									<script>
										$(function(){
											var $oldPwd = $('#old_passwd');
											var $passwordField = $('#{$input.name}');
											var $output = $('#{$input.name}-output');
											var $generateBtn = $('#{$input.name}-generate-btn');
											var $generateField = $('#{$input.name}-generate-field');
											var $cancelBtn = $('#{$input.name}-cancel-btn');
											
											var feedback = [
												{ badge: 'text-danger', text: '{l s="Invalid" js=1}' },
												{ badge: 'text-warning', text: '{l s="Okay" js=1}' },
												{ badge: 'text-success', text: '{l s="Good" js=1}' },
												{ badge: 'text-success', text: '{l s="Fabulous" js=1}' }
											];
											$.passy.requirements.length.min = 8;
											$.passy.requirements.characters = 'DIGIT';
											$passwordField.passy(function(strength, valid) {
												$output.text(feedback[strength].text);
												$output.removeClass('text-danger').removeClass('text-warning').removeClass('text-success');
												$output.addClass(feedback[strength].badge);
												if (valid){
													$output.show();
												}
												else {
													$output.hide();
												}
											});
											var $container = $('#{$input.name}-change-container');
											var $changeBtn = $('#{$input.name}-btn-change');
											var $confirmPwd = $('#{$input.name}2');

											$changeBtn.on('click',function(){
												$container.removeClass('hide');
												$changeBtn.addClass('hide');
											});
											$generateBtn.click(function() {
												$generateField.passy( 'generate', 8 );
												var generatedPassword = $generateField.val();
												$passwordField.val(generatedPassword);
												$confirmPwd.val(generatedPassword);
											});
											$cancelBtn.on('click',function() {
												$container.find("input").val("");
												$container.addClass('hide');
												$changeBtn.removeClass('hide');
											});

											$.validator.addMethod('password_same', function(value, element) {
												return $passwordField.val() == $confirmPwd.val();
											}, '{l s="Invalid password confirmation" js=1}');

											$('#employee_form').validate({
												rules: {
													"email": {
														email: true
													},
													"{$input.name}" : {
														minlength: 8
													},
													"{$input.name}2": {
														password_same: true
													},
													"old_passwd" : {},
												},
												// override jquery validate plugin defaults for bootstrap 3
												highlight: function(element) {
													$(element).closest('.form-group').addClass('has-error');
												},
												unhighlight: function(element) {
													$(element).closest('.form-group').removeClass('has-error');
												},
												errorElement: 'span',
												errorClass: 'help-block',
												errorPlacement: function(error, element) {
													if(element.parent('.input-group').length) {
														error.insertAfter(element.parent());
													} else {
														error.insertAfter(element);
													}
												}
											});
										});
									</script>
								{elseif $input.type == 'password'}
									<div class="input-group fixed-width-lg">
										<span class="input-group-addon">
											<i class="icon-key"></i>
										</span>
										<input type="password"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											name="{$input.name}"
											class="{if isset($input.class)}{$input.class}{/if}"
											value=""
											{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
											{if isset($input.required) && $input.required } required="required" {/if} />
									</div>

								{elseif $input.type == 'birthday'}
								<div class="form-group">
									{foreach $input.options as $key => $select}
									<div class="col-lg-2">
										<select name="{$key}" class="fixed-width-lg{if isset($input.class)} {$input.class}{/if}">
											<option value="">-</option>
											{if $key == 'months'}
												{*
													This comment is useful to the translator tools /!\ do not remove them
													{l s='January'}
													{l s='February'}
													{l s='March'}
													{l s='April'}
													{l s='May'}
													{l s='June'}
													{l s='July'}
													{l s='August'}
													{l s='September'}
													{l s='October'}
													{l s='November'}
													{l s='December'}
												*}
												{foreach $select as $k => $v}
													<option value="{$k}" {if $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v}</option>
												{/foreach}
											{else}
												{foreach $select as $v}
													<option value="{$v}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v}</option>
												{/foreach}
											{/if}
										</select>
									</div>
									{/foreach}
								</div>
								{elseif $input.type == 'group'}
									{assign var=groups value=$input.values}
									{include file='helpers/form/form_group.tpl'}
								{elseif $input.type == 'shop'}
									{$input.html}
 

								{elseif $input.type == 'categories'}
									{$categories_tree}
								{elseif $input.type == 'file'}
									 
									 {assign var='show_thumbnail' value=false}
									 {if isset($input.image) && !empty($input.image)}
										{assign var='show_thumbnail' value=true}
									{/if}
																	 
									 {if $show_thumbnail}
										{assign var='id' value=$input.name}
										{assign var='max_files' value=1}
										 <div class="form-group">
											<div class="col-lg-12" id="{$id|escape:'html':'UTF-8'}-images-thumbnails">
												 
												 	{if isset($input.image) && $input.type == 'file'}
												<div>
													{$input.image}
													{if isset($input.size)}<p>{l s='File size'} {$input.size}kb</p>{/if}
													 
													{if isset($input.delete_url)}
													<p>
														<a class="btn btn-default" href="{$input.delete_url}">
															<i class="icon-trash"></i> {l s='Delete'}
														</a>
													</p>
													{/if}
												</div>
											 {/if}
											 
											</div>
										</div>
										{else}
										{assign var='id' value=$input.name}
										{assign var='max_files' value=1}
										 

											<div class="form-group">
												<div class="col-sm-6">
													<input id="{$id|escape:'html':'UTF-8'}" type="file" name="{$input.name|escape:'html':'UTF-8'}{if isset ($multiple) && $multiple}[]{/if}"{if isset($multiple) && $multiple} multiple="multiple"{/if} class="hide" />
													<div class="dummyfile input-group">
														<span class="input-group-addon"><i class="icon-file"></i></span>
														<input id="{$id|escape:'html':'UTF-8'}-name" type="text" name="filename" readonly />
														<span class="input-group-btn">
															<button id="{$id|escape:'html':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
																<i class="icon-folder-open"></i> {if isset($multiple) && $multiple}{l s='Add files'}{else}{l s='Add file'}{/if}
															</button>
															{if (!isset($multiple) || !$multiple) && isset($files) && $files|count == 1 && isset($files[0].download_url)}
																<a href="{$files[0].download_url|escape:'html':'UTF-8'}" class="btn btn-default">
																	<i class="icon-cloud-download"></i>
																	{if isset($size)}{l s='Download current file (%skb)' sprintf=$size}{else}{l s='Download current file'}{/if}
																</a>
															{/if}
														</span>
													</div>
												</div>
											</div>
											<script type="text/javascript">
											{if isset($multiple) && isset($max_files)}
												var {$id|escape:'html':'UTF-8'}_max_files = {$max_files - $files|count};
											{/if}

												$(document).ready(function(){
													$('#{$id|escape:'html':'UTF-8'}-selectbutton').click(function(e) {
														$('#{$id|escape:'html':'UTF-8'}').trigger('click');
													});

													$('#{$id|escape:'html':'UTF-8'}-name').click(function(e) {
														$('#{$id|escape:'html':'UTF-8'}').trigger('click');
													});

													$('#{$id|escape:'html':'UTF-8'}-name').on('dragenter', function(e) {
														e.stopPropagation();
														e.preventDefault();
													});

													$('#{$id|escape:'html':'UTF-8'}-name').on('dragover', function(e) {
														e.stopPropagation();
														e.preventDefault();
													});

													$('#{$id|escape:'html':'UTF-8'}-name').on('drop', function(e) {
														e.preventDefault();
														var files = e.originalEvent.dataTransfer.files;
														$('#{$id|escape:'html':'UTF-8'}')[0].files = files;
														$(this).val(files[0].name);
													});

													$('#{$id|escape:'html':'UTF-8'}').change(function(e) {
														if ($(this)[0].files !== undefined)
														{
															var files = $(this)[0].files;
															var name  = '';

															$.each(files, function(index, value) {
																name += value.name+', ';
															});

															$('#{$id|escape:'html':'UTF-8'}-name').val(name.slice(0, -2));
														}
														else // Internet Explorer 9 Compatibility
														{
															var name = $(this).val().split(/[\\/]/);
															$('#{$id|escape:'html':'UTF-8'}-name').val(name[name.length-1]);
														}
													});

													if (typeof {$id|escape:'html':'UTF-8'}_max_files !== 'undefined')
													{
														$('#{$id|escape:'html':'UTF-8'}').closest('form').on('submit', function(e) {
															if ($('#{$id|escape:'html':'UTF-8'}')[0].files.length > {$id|escape:'html':'UTF-8'}_max_files) {
																e.preventDefault();
																alert('{l s='You can upload a maximum of %s files'|sprintf:$max_files}');
															}
														});
													}
												});
											</script>

 									{/if}
								{elseif $input.type == 'categories_select'}
									{$input.category_tree}
								{elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
									{$asso_shop}
								{elseif $input.type == 'color'}
								<div class="form-group">
									<div class="col-lg-2">
										<div class="row">
											<div class="input-group">
												<input type="color"
												data-hex="true"
												{if isset($input.class)} class="{$input.class}"
												{else} class="color mColorPickerInput"{/if}
												name="{$input.name}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											</div>
										</div>
									</div>
								</div>
								{elseif $input.type == 'date'}
									<div class="row">
										<div class="input-group col-lg-4">
											<input
												id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
												type="text"
												data-hex="true"
												{if isset($input.class)} class="{$input.class}"
												{else}class="datepicker"{/if}
												name="{$input.name}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											<span class="input-group-addon">
												<i class="icon-calendar-empty"></i>
											</span>
										</div>
									</div>
								{elseif $input.type == 'datetime'}
									<div class="row">
										<div class="input-group col-lg-4">
											<input
												id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
												type="text"
												data-hex="true"
												{if isset($input.class)} class="{$input.class}"
												{else} class="datetimepicker"{/if}
												name="{$input.name}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											<span class="input-group-addon">
												<i class="icon-calendar-empty"></i>
											</span>
										</div>
									</div>

								{elseif $input.type == 'free'}
									{$fields_value[$input.name]}
								{elseif $input.type == 'html'}
									{if isset($input.html_content)}
										{$input.html_content}
									{else}
										{$input.name}
									{/if}
								{/if}
								{/block}{* end block input *}
								{block name="description"}
									{if isset($input.desc) && !empty($input.desc)}
										<p class="help-block">
											{if is_array($input.desc)}
												{foreach $input.desc as $p}
													{if is_array($p)}
														<span id="{$p.id}">{$p.text}</span><br />
													{else}
														{$p}<br />
													{/if}
												{/foreach}
											{else}
												{$input.desc}
											{/if}
										</p>
									{/if}
								{/block}
								</div>
							{/block}{* end block field *}
						{/if}
						</div>
						{/block}
					
					{/foreach}
					{hook h='displayAdminForm' fieldset=$f}
					{if isset($name_controller)}
						{capture name=hookName assign=hookName}display{$name_controller|ucfirst}Form{/capture}
						{hook h=$hookName fieldset=$f}
					{elseif isset($smarty.get.controller)}
						{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}Form{/capture}
						{hook h=$hookName fieldset=$f}
					{/if}
				</div><!-- /.form-wrapper -->
				{elseif $key == 'desc'}
					<div class="alert alert-info col-lg-offset-3">
						{if is_array($field)}
							{foreach $field as $k => $p}
								{if is_array($p)}
									<span{if isset($p.id)} id="{$p.id}"{/if}>{$p.text}</span><br />
								{else}
									{$p}
									{if isset($field[$k+1])}<br />{/if}
								{/if}
							{/foreach}
						{else}
							{$field}
						{/if}
					</div>
				{/if}
				{block name="other_input"}{/block}
			{/foreach}
			{block name="footer"}
			{capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
				{if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
					<div class="panel-footer">
						{if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
						<button type="submit" value="1"	id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']}{else}{$table}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']}{else}{$submit_action}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}" class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']}{else}btn btn-default pull-right{/if}">
							<i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']}
						</button>
						{/if}
						{if isset($show_cancel_button) && $show_cancel_button}
						<a href="{$back_url|escape:'html':'UTF-8'}" class="btn btn-default" onclick="window.history.back();">
							<i class="process-icon-cancel"></i> {l s='Cancel'}
						</a>
						{/if}
						{if isset($fieldset['form']['reset'])}
						<button
							type="reset"
							id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']}{else}{$table}_form_reset_btn{/if}"
							class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']}{else}btn btn-default{/if}"
							>
							{if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']}"></i> {/if} {$fieldset['form']['reset']['title']}
						</button>
						{/if}
						{if isset($fieldset['form']['buttons'])}
						{foreach from=$fieldset['form']['buttons'] item=btn key=k}
							{if isset($btn.href) && trim($btn.href) != ''}
								<a href="{$btn.href}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title}</a>
							{else}
								<button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" name="{if isset($btn['name'])}{$btn['name']}{else}submitOptions{$table}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title}</button>
							{/if}
						{/foreach}
						{/if}
					</div>
				{/if}
			{/block}
		</div>
		{/block}
		{block name="other_fieldsets"}{/block}
	{/foreach}
</form>
{/block}
{block name="after"}{/block}
 
{if isset($tinymce) && $tinymce}
<script type="text/javascript">
	var iso = '{$iso|addslashes}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
	var ad = '{$ad|addslashes}';

	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"autoload_rte"
			});
		{/block}
	});
</script>
{/if}
{if $firstCall}
	<script type="text/javascript">
		var module_dir = '{$smarty.const._MODULE_DIR_}';
		var id_language = {$defaultFormLanguage|intval};
		var languages = new Array();
		var vat_number = {if $vat_number}1{else}0{/if};
		// Multilang field setup must happen before document is ready so that calls to displayFlags() to avoid
		// precedence conflicts with other document.ready() blocks
		{foreach $languages as $k => $language}
			languages[{$k}] = {
				id_lang: {$language.id_lang},
				iso_code: '{$language.iso_code}',
				name: '{$language.name}',
				is_default: '{if isset($language.is_default)}{$language.is_default}{/if}'
			};
		{/foreach}
		// we need allowEmployeeFormLang var in ajax request
		allowEmployeeFormLang = {$allowEmployeeFormLang|intval};
		displayFlags(languages, id_language, allowEmployeeFormLang);

		$(document).ready(function() {

			$(".show_checkbox").click(function () {
				$(this).addClass('hidden')
				$(this).siblings('.checkbox').removeClass('hidden');
				$(this).siblings('.hide_checkbox').removeClass('hidden');
				return false;
			});
			$(".hide_checkbox").click(function () {
				$(this).addClass('hidden')
				$(this).siblings('.checkbox').addClass('hidden');
				$(this).siblings('.show_checkbox').removeClass('hidden');
				return false;
			});

			{if isset($fields_value.id_state)}
				if ($('#id_country') && $('#id_state'))
				{
					ajaxStates({$fields_value.id_state});
					$('#id_country').change(function() {
						ajaxStates();
					});
				}
			{/if}

			if ($(".datepicker").length > 0)
				$(".datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});

			if ($(".datetimepicker").length > 0)
			$('.datetimepicker').datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				// Define a custom regional settings in order to use PrestaShop translation tools
				currentText: '{l s='Now'}',
				closeText: '{l s='Done'}',
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: '{l s='Choose Time' js=1}',
				timeText: '{l s='Time' js=1}',
				hourText: '{l s='Hour' js=1}',
				minuteText: '{l s='Minute' js=1}',
			});
			{if isset($use_textarea_autosize)}
			$(".textarea-autosize").autosize();
			{/if}
		});
	state_token = '{getAdminToken tab='AdminStates'}';
	{block name="script"}{/block}
	</script>
{/if}
