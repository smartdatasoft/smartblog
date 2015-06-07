{capture name=path}
    <a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='All Blog News' mod='smartblog'}</a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    {$meta_title}
{/capture}
<div id="content" class="block">
   <div itemtype="#" itemscope="" id="sdsblogArticle" class="blog-post">
   		<div class="page-item-title">
   			<h1>{$meta_title}</h1>
   		</div>
      <div class="post-info">
         {assign var="catOptions" value=null}
                        {$catOptions.id_category = $id_category}
                        {$catOptions.slug = $cat_link_rewrite}
                     <span>
                        {l s='Posted by ' mod='smartblog'} 
                        {if $smartshowauthor ==1}
                            &nbsp;<i class="icon icon-user"></i>&nbsp; 
                            <span itemprop="author">
                                {if $smartshowauthorstyle != 0}
                                    {$firstname} {$lastname}
                                {else}
                                    {$lastname} {$firstname}
                                {/if}
                            </span>&nbsp; 
                            <i class="icon icon-calendar"></i>&nbsp; 
                            <span itemprop="dateCreated">
                                {Tools::displayDate($created, null, $smartshowtime)}
                            </span>
                        {/if}&nbsp;&nbsp;
                        <i class="icon icon-tags"></i>&nbsp;
                        <span itemprop="articleSection">
                            <a href="{smartblog::GetSmartBlogLink('smartblog_category',$catOptions)}">{$title_category}</a>
                        </span> &nbsp;
                        <i class="icon icon-comments"></i>&nbsp; 
                        {l s='Comments' mod='smartblog'} 
                        ({if $countcomment != ''}{$countcomment}{else}{l s='0' mod='smartblog'}{/if})
                     </span>
                    <a title="" style="display:none" itemprop="url" href="#"></a>
      </div>
      <div itemprop="articleBody">
            <div id="lipsum" class="articleContent">
                    {assign var="activeimgincat" value='0'}
                    {$activeimgincat = $smartshownoimg} 
                    {if ($post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                        <a id="post_images" href="{$modules_dir}/smartblog/images/{$post_img}-single-default.jpg"><img src="{$modules_dir}/smartblog/images/{$post_img}-single-default.jpg" alt="{$meta_title}"></a>
                    {/if}
             </div>
            <div class="sdsarticle-des">
               {$content}
            </div>
            {if $tags != ''}
                <div class="sdstags-update">
                    <span class="tags"><b>{l s='Tags:' mod='smartblog'} </b> 
                        {foreach from=$tags item=tag}
                            {assign var="options" value=null}
                            {$options.tag = $tag.name|urlencode}
                            <a title="tag" href="{smartblog::GetSmartBlogLink('smartblog_tag',$options)|escape:'html':'UTF-8'}">{$tag.name}</a>
                        {/foreach}
                    </span>
                </div>
           {/if}
      </div>
     
      <div class="sdsarticleBottom">
        {$HOOK_SMART_BLOG_POST_FOOTER}
      </div>
   </div>

{if $countcomment != ''}
<div id="articleComments">
            <h3>
                {l s='Comments' mod='smartblog'}
                ({if $countcomment != ''}{$countcomment}{else}{l s='0' mod='smartblog'}{/if})
                <span></span>
            </h3>
        <div id="comments">      
            <ul class="commentList">
                  {$i=1}
                {foreach from=$comments item=comment}
                    
                       {include file="./comment_loop.tpl" childcommnets=$comment}
                   
                  {/foreach}
            </ul>
        </div>
</div>
 {/if}

</div>
{if Configuration::get('smartenablecomment') == 1}
{if $comment_status == 1}
<div class="smartblogcomments" id="respond">
    <!-- <h4 id="commentTitle">{l s='Leave a Comment'  mod='smartblog'}</h4> -->
    <h4 class="comment-reply-title" id="reply-title">{l s='Leave a Reply'  mod='smartblog'} <small style="float:right;">
                <a style="display: none;" href="/wp/sellya/sellya/this-is-a-post-with-preview-image/#respond" 
                   id="cancel-comment-reply-link" rel="nofollow">{l s='Cancel Reply'  mod='smartblog'}</a>
            </small>
        </h4>
		<div id="commentInput">
	<table>
            <form action="" method="post" id="commentform">
		<tbody><tr>
	<td><span class="required">*</span> <b>{l s='Name:'  mod='smartblog'} </b></td>
		<td>
	<input type="text" tabindex="1" class="inputName form-control grey" value="" name="name">																	
		</td>
	</tr>
        <tr>
		<td><span class="required">*</span> <b>{l s='E-mail:'  mod='smartblog'} </b><span class="note">{l s='(Not Published)'  mod='smartblog'}</span></td>
			<td>
		<input type="text" tabindex="2" class="inputMail form-control grey" value="" name="mail">
			</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;<b>{l s='Website:'  mod='smartblog'} </b><span class="note"> {l s='(Site url with'  mod='smartblog'}http://)</span></td>
		<td><input type="text" tabindex="3" value="" name="website" class="form-control grey"></td>
		</tr>
			<tr>
			<td><span class="required">*</span> <b> {l s='Comment:'  mod='smartblog'}</b></td>
		<td>
		<textarea tabindex="4" class="inputContent form-control grey" rows="8" cols="50" name="comment"></textarea>
	</td>
	</tr>
	{if Configuration::get('smartcaptchaoption') == '1'}
		<tr>
			<td></td><td><img src="{$modules_dir}smartblog/classes/CaptchaSecurityImages.php?width=100&height=40&characters=5"></td>
		</tr><tr>
		<td><b>{l s='Type Code' mod='smartblog'}</b></td><td><input type="text" tabindex="" value="" name="smartblogcaptcha" class="smartblogcaptcha form-control grey"></td>
		</tr>
	{/if}
	</tbody></table>
                 <input type='hidden' name='comment_post_ID' value='1478' id='comment_post_ID' />
                  <input type='hidden' name='id_post' value='{$id_post}' id='id_post' />

                <input type='hidden' name='comment_parent' id='comment_parent' value='0' />
	<div class="right">
      
        <div class="submit">
            <input type="submit" name="addComment" id="submitComment" class="bbutton btn btn-default button-medium" value="{l s='Submit'  mod='smartblog'}">
		</div>

        </form>
		</div>
		</div>
</div>

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
	  url: baseDir + 'modules/smartblog/ajax.php',
	  data: data,
	  
	  dataType: 'json',
	  
	  beforeSend: function() {
				$('.success, .warning, .error').remove();
				$('#submitComment').attr('disabled', true);
				$('#commentInput').before('<div class="attention"><img src="http://321cart.com/sellya/catalog/view/theme/default/image/loading.gif" alt="" />Please wait!</div>');

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
		return document.getElementById(e);
	}
};

      
      
</script>
{/if}
{/if}
{if isset($smartcustomcss)}
    <style>
        {$smartcustomcss}
    </style>
{/if}
