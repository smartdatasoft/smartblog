{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file='page.tpl'}
{block name='breadcrumb'}
	{if isset($breadcrumb)}
		<nav class="breadcrumb">
		  <ol>
		      <li>
		        <a href="{$breadcrumb.links[0].url}">
		          <span>{$breadcrumb.links[0].title}</span>
		        </a>
		      </li>
		      <li>
		        <a href="{smartblog::GetSmartBlogLink('smartblog')}">
		        <span>{l s='All Post' mod='smartblog'}</span>
		        </a>
		      </li>
		      {if $title_category != ''}
		     {assign var="link_detail" value=null}
		    {$link_detail.id_post = $id_post} 
		    {$link_detail.slug = $link_rewrite_}
		      <li>
		        <a href="{smartblog::GetSmartBlogLink('smartblog_post',$link_detail)}">
		        <span>{$meta_title}</span>
		        </a>
		      </li>
		    {/if}
		  </ol>
		</nav>
	{/if}
{/block}
{block name='page_content'}
		{capture name=path}<a href="{smartblog::GetSmartBlogLink('smartblog')|escape:'htmlall':'UTF-8'}">{l s='All Blog News' mod='smartblog'}</a><span class="navigation-pipe"></span>{$meta_title|escape:'htmlall':'UTF-8'}{/capture}
		<div id="content" class="block">
			<div itemtype="#" itemscope="" id="sdsblogArticle" class="blog-post">
				<div class="title_block">{$meta_title|escape:'htmlall':'UTF-8'}</div>
				<div class="sdsarticleHeader">
					<span>
						{if $smartshowauthor ==1} {l s='Posted by ' mod='smartblog'} &nbsp;<i class="icon icon-user"></i>
						<span itemprop="author">{if $smartshowauthorstyle != 0}{$firstname} {$lastname}{else}{$lastname} {$firstname}{/if}</span>{/if}&nbsp;
						<i class="icon icon-calendar"></i>&nbsp;<span itemprop="dateCreated">{$created|escape:'htmlall':'UTF-8'}</span>
                   		<span itemprop="articleSection">
                   			{$assocCats = BlogCategory::getPostCategoriesFull($post.id_post)}
							{$catCounts = 0}
							{if !empty($assocCats)}
                        		&nbsp;&nbsp;<i class="icon icon-tags"></i>&nbsp;
                            	{foreach $assocCats as $catid=>$assoCat}
	                                {if $catCounts > 0}, {/if}
	                                {$catlink=[]}
	                                {$catlink.id_category = $assoCat.id_category}
	                                {$catlink.slug = $assoCat.link_rewrite}
	                                <a href="{$smartbloglink->getSmartBlogCategoryLink($assoCat.id_category,$assoCat.link_rewrite)|escape:'htmlall':'UTF-8'}">
	                                    {$assoCat.name|escape:'htmlall':'UTF-8'}
	                                </a>
	                                {$catCounts = $catCounts + 1}
								{/foreach}
							{/if}
						</span>
                       &nbsp;<i class="icon icon-comments"></i>&nbsp;
                       {if $countcomment != ''}{$countcomment|escape:'htmlall':'UTF-8'}{else}{l s='0' mod='smartblog'}{/if}
                       {l s=' Comments' mod='smartblog'}
                   	</span>
					<a title="" style="display:none" itemprop="url" href="#"></a>
      			</div>
				<div itemprop="articleBody">
					<div class="articleContent">                    
	                    {if isset($ispost) && !empty($ispost)}
	                   		<a itemprop="url" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.cat_link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">
	                    {/if}

	                    {if $smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default') != 'false'}
	                        <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}" src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}" class="imageFeatured">                   
	                    {/if}
	                           
	                    {if isset($ispost) && !empty($ispost)}
	                    	</a>
	                    {/if}
						<div class="sdsarticle-des" style="text-align: left;">
	                    {$post.content nofilter}
	                   	</div>
	                   {$displayBackOfficeSmartBlog}
	            	</div>
	                   
	            	<div class="sdsarticle-des"></div>
	            	{if $tags != ''}
		                <div class="sdstags-update">
		                    <span class="tags"><b>{l s='Tags:' mod='smartblog'} </b> 
		                        {foreach from=$tags item=tag}
		                          <a title="tag" href="{$smartbloglink->getSmartBlogTag($tag.name|urlencode)|escape:'htmlall':'UTF-8'}">{$tag.name|escape:'htmlall':'UTF-8'}</a>
		                        
		                        {/foreach}
		                    </span>
		                </div>
	           		{/if}
	      		</div>
      			<div class="sdsarticleBottom"></div>
		      	{if isset($HOOK_SMART_BLOG_POST_FOOTER)}
		            {$HOOK_SMART_BLOG_POST_FOOTER nofilter}
		        {/if}
			</div>
		</div>
		<div id="product_comments_block_tab">
			<ul class="footer_links clearfix">
				{foreach from=$posts_previous item="post"}
        			{if isset($post.id_smart_blog_post)}
						<li>
							<a title="{l s='Prevoius Post' mod='smartblog'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small"><span><i class="icon-chevron-left"></i> {l s='Prevoius Post' mod='smartblog'}</span></a>
						</li>
					{/if}
				{/foreach}
				{foreach from=$posts_next item="post"}
					{if isset($post.id_smart_blog_post)}
						<li class="pull-right">
							<a title="{l s='Next Post' mod='smartblog'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small"><span>{l s='Next Post' mod='smartblog'} <i class="icon-chevron-right"></i> </span></a>
						</li>
					{/if}
				{/foreach}
			</ul>
		</div>

		{if $countcomment != ''}
			<div id="articleComments">
        		<h3>{if $countcomment != ''}{$countcomment|escape:'htmlall':'UTF-8'}{else}{l s='0' mod='smartblog'}{/if}{l s=' Comments' mod='smartblog'}<span></span></h3>
   				 <div id="comments">      
        			<ul class="commentList">
              			{$i=1}
       					{foreach from=$comments item=comment}
               				{include file="module:smartblog/views/templates/front/comment_loop.tpl" childcommnets=$comment}
          				{/foreach}
       				</ul>
    			</div>
			</div>
		{/if}

		{if ($enableguestcomment==0) && isset($is_looged) && $is_looged==''}
			<section class="page-product-box">
				<h3 class="page-product-heading">{l s='Comments' mod='smartblog'}</h3>
			    {l s='Log in or register to post comments' mod='smartblog'}
			</section>
		{else}
			{if Configuration::get('smartenablecomment') == 1}
				{if $comment_status == 1}
					<div class="smartblogcomments" id="respond">
				    	<h4 class="comment-reply-title" id="reply-title">{l s='Leave a Reply'  mod='smartblog'}
				    		<small style="float:right;">
				        		<a style="display: none;" href="/wp/sellya/sellya/this-is-a-post-with-preview-image/#respond" id="cancel-comment-reply-link" rel="nofollow">{l s='Cancel Reply'  mod='smartblog'}</a>
				            </small>
				        </h4>
						<div id="commentInput">
							<form action="" method="post" id="commentform">
								<table>
									<tbody>
										{if ($enableguestcomment==0) && isset($is_looged) && $is_looged>0}
											<tr>
												<td>
													<input type="hidden" tabindex="1" class="inputName form-control grey" value="{$is_looged_fname|escape:'htmlall':'UTF-8'}" name="name" id="name">
												</td>
											</tr>
											<tr>
												<td>
													<input type="hidden" tabindex="2" class="inputMail form-control grey" value="{$is_looged_email|escape:'htmlall':'UTF-8'}" name="mail" id="mail">
												</td>
											</tr>
											<tr>
												<td><input type="hidden" tabindex="3" value="" name="website" class="form-control grey"></td>
											</tr>
										{else}
											<tr>
												<td>
													<span class="required">*</span> <b>{l s='Name:'  mod='smartblog'} </b>
												</td>
												<td>
													<input type="text" tabindex="1" class="inputName form-control grey" value="" name="name">
												</td>
											</tr>
        									<tr>
												<td>
													<span class="required">*</span> <b>{l s='E-mail:' mod='smartblog'} </b><span class="note">{l s='(Not Published)'  mod='smartblog'}</span>
												</td>
												<td>
													<input type="text" tabindex="2" class="inputMail form-control grey" value="" name="mail">
												</td>
											</tr>
											<tr>
												<td>&nbsp;&nbsp;&nbsp;<b>{l s='Website:'  mod='smartblog'} </b><span class="note"> {l s='(Site url with'  mod='smartblog'}http://)</span></td>
												<td><input type="text" tabindex="3" value="" name="website" class="form-control grey"></td>
											</tr>
										{/if}	
										<tr>
											<td><span class="required">*</span> <b> {l s='Comment:'  mod='smartblog'}</b></td>
											<td><textarea tabindex="4" class="inputContent form-control grey" rows="8" cols="50" name="comment"></textarea></td>
										</tr>
										{if Configuration::get('smartcaptchaoption') == '1'}
											<tr>
												<td></td><td><img src="{$modules_dir|escape:'htmlall':'UTF-8'}smartblog/classes/CaptchaSecurityImages.php?width=100&height=40&characters=5"></td>
											</tr>
											<tr>
												<td><b>{l s='Type Code' mod='smartblog'}</b></td><td><input type="text" tabindex="" value="" name="smartblogcaptcha" class="smartblogcaptcha form-control grey"></td>
											</tr>
										{/if}
									</tbody>
								</table>
             					<input type='hidden' name='comment_post_ID' value='1478' id='comment_post_ID' />
             					<input type='hidden' name='id_post' value='{$id_post|escape:'htmlall':'UTF-8'}' id='id_post' />
            					<input type='hidden' name='comment_parent' id='comment_parent' value='0' />
								<div class="right">
							        <div class="submit">
										<button type="submit" name="addComment" id="submitComment" class="bbutton btn btn-default button-medium" >{l s='Submit' mod='smartblog'}</button>
									</div>
								</div>
    						</form>
						</div>
					</div>
				{/if}
				<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
				<script type="text/javascript">
					$('#submitComment').bind('click',function(event) {
					event.preventDefault();
					 
					 
					var data = { 'action':'postcomment', 
					'id_post':$('input[name=\'id_post\']').val(),
					'comment_parent':$('input[name=\'comment_parent\']').val(),
					'name':$('input[name=\'name\']').val(),
					'website':$('input[name=\'website\']').val(),
					'smartblogcaptcha':$('input[name=\'smartblogcaptcha\']').val(),
					'comment':$('textarea[name=\'comment\']').val(),
					'mail':$('input[name=\'mail\']').val() };
						$.ajax( {
						  url: '{$baseDir}modules/smartblog/ajax.php',
						  data: data,
						  method: 'POST',
						  dataType: 'json',
						  
						  beforeSend: function() {
									$('.success, .warning, .error').remove();
									$('#submitComment').attr('disabled', true);
									$('#commentInput').before('<div class="attention"><img src="views/img/loading.gif" alt="" />Please wait!</div>');

									},
									complete: function() {
									$('#submitComment').attr('disabled', false);
									$('.attention').remove();
									},
							success: function(json) {
								if (json['error']) {
										 
											$('#commentInput').before('<div class="warning">' + '<i class="icon-warning-sign icon-lg"></i>' + json['error']['common'] + '</div>');
											
											if (json['error']['name']) {
												$('.inputName').after('<span class="error">' + json['error']['name'] + '</span>');
											}
											if (json['error']['mail']) {
												$('.inputMail').after('<span class="error">' + json['error']['mail'] + '</span>');
											}
											if (json['error']['comment']) {
												$('.inputContent').after('<span class="error">' + json['error']['comment'] + '</span>');
											}
											if (json['error']['captcha']) {
												$('.smartblogcaptcha').after('<span class="error">' + json['error']['captcha'] + '</span>');
											}
										}
										
										if (json['success']) {
											$('input[name=\'name\']').val('');
											$('input[name=\'mail\']').val('');
											$('input[name=\'website\']').val('');
											$('textarea[name=\'comment\']').val('');
									 		$('input[name=\'smartblogcaptcha\']').val('');
										
											$('#commentInput').before('<div class="success">' + json['success'] + '</div>');
											setTimeout(function(){
												$('.success').fadeOut(300).delay(450).remove();
																		},2500);
										
										}
									}
								} );
							} );
					    var addComment = {
						moveForm : function(commId, parentId, respondId, postId) {

							var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');
							if ( ! comm || ! respond || ! cancel || ! parent )
								return;
					                    
					 		t.I('mail').value='{$is_looged_email|escape:'htmlall':'UTF-8'}';
					 		t.I('name').value='{$is_looged_fname|escape:'htmlall':'UTF-8'}';
							t.respondId = respondId;
							postId = postId || false;

							if ( ! t.I('wp-temp-form-div') ) {
								div = document.createElement('div');
								div.id = 'wp-temp-form-div';
								div.style.display = 'none';
								respond.parentNode.insertBefore(div, respond);
							}


							comm.parentNode.insertBefore(respond, comm.nextSibling);
							if ( post && postId )
								post.value = postId;
							parent.value = parentId;
							cancel.style.display = '';

							cancel.onclick = function() {
								var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

								if ( ! temp || ! respond )
									return;

								t.I('comment_parent').value = '0';
								t.I('mail').value='{$is_looged_email|escape:'htmlall':'UTF-8'}';
					 			t.I('name').value='{$is_looged_fname|escape:'htmlall':'UTF-8'}';
								temp.parentNode.insertBefore(respond, temp);
								temp.parentNode.removeChild(temp);
								this.style.display = 'none';
								this.onclick = null;
								return false;
							};

							try { t.I('comment').focus(); }
							catch(e) {}

							return false;
						},

						I : function(e) {
							var elem = document.getElementById(e);
					                if(!elem){
					                    return document.querySelector('[name="'+e+'"]');
					                }else{
					                    return elem;
					                }
						}
					}; 
				</script>
			{/if}
		{/if}
		{if isset($smartcustomcss)}
		    <style>
		        {$smartcustomcss|escape:'htmlall':'UTF-8'}
		    </style>
		{/if}
{/block}