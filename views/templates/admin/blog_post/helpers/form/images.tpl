 
<div id="product-images" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Images" />	
	<div class="panel-heading tab" >
		{l s='Images'}
	 <span class="badge" id="countImage">{$count}</span>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-lg-3 file_upload_label">
				<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Format:'} JPG, GIF, PNG. {l s='Filesize:'} {$max_image_size|string_format:"%.2f"} {l s='MB max.'}">
					{if isset($id_image)}{l s='Edit this product\'s image:'}{else}{l s='Add a new image to this Book'}{/if}
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
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminBlogPost')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddBlogPost" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddBlogPostAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
	</div>
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
			function afterDeleteBookImage(data)
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
						"action":"deleteBookImage",
						"id_book_image":id,
						"id_smart_blog_post" : {/literal}{$id_smart_blog_post}{literal},
						 "token" : "{/literal}{$token_book}{literal}",
						"tab" : "AdminBlogPost",
						"ajax" : 1 }, afterDeleteBookImage
				);
			});
			
			 
			
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
					"action":"updateImagePosition",
					"json":json,
					"token" : "{/literal}{$token_book|escape:'html':'UTF-8'}{literal}",
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
</div>	 	
		