<div itemtype="#" itemscope="" class="sdsarticleCat clearfix">
    <div id="smartblogpost-{$post.id_post}">
    <div class="sdsarticleHeader">
         {assign var="options" value=null}
          {$options.id_post = $post.id_post} 
          {$options.slug = $post.link_rewrite}
          <p class='sdstitle_block{if $post.is_featured}_featured{/if}'><a title="{$post.meta_title}" href='{smartblog::GetSmartBlogLink('smartblog_post',$options)}'>{$post.meta_title}</a></p>
         {assign var="options" value=null}
          {$options.id_post = $post.id_post}
          {$options.slug = $post.link_rewrite}
          {assign var="catlink" value=null}
          {$catlink.id_category = $post.id_category}
          {$catlink.slug = $post.cat_link_rewrite}
         <span>
         {if $smartshowauthor == 1}
           {l s='Posted by' mod='smartblog'} 
           &nbsp;<i class="icon icon-user"></i>&nbsp; <span itemprop="author">{if $smartshowauthorstyle != 0}{$post.firstname} {$post.lastname}{else}{$post.lastname} {$post.firstname}{/if}</span>
         {/if}
         &nbsp;<i class="icon icon-calendar"></i>&nbsp;<span itemprop="dateCreated">{$post.created|date_format}</span>
          {if !empty($title_category) || strtolower($post.cat_name) != 'uncategories'}
          &nbsp;&nbsp;<i class="icon icon-tags"></i>&nbsp; <span itemprop="articleSection"><a href="{smartblog::GetSmartBlogLink('smartblog_category',$catlink)}">{if !empty($title_category)}{$title_category}{else}{$post.cat_name}{/if}</a></span>
          {/if}
           &nbsp;<span class="comment"> &nbsp;<i class="icon icon-comments"></i>&nbsp; <a title="{$post.totalcomment} Comments" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{$post.totalcomment} {l s=' Comments' mod='smartblog'}</a></span>
           {if $smartshowviewed ==1}&nbsp; <i class="icon icon-eye-open"></i>{l s=' views' mod='smartblog'} ({$post.viewed}){/if}
           {if $post.is_featured}&nbsp;&nbsp;<i class="icon icon-asterisk"></i><span> featured</span>{/if}
           </span>
          
    </div>
    <div class="articleContent">
          <a itemprop="url" title="{$post.meta_title}" class="imageFeaturedLink">
            {assign var="activeimgincat" value='0'}
            {$activeimgincat = $smartshownoimg} 
            {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
              <img itemprop="image" alt="{$post.meta_title}" src="{$modules_dir}/smartblog/images/{$post.post_img}-single-default.jpg" class="imageFeatured">
            {/if}
          </a>
    </div>
           <div class="sdsarticle-des">
          <span itemprop="description" class="clearfix"><div id="lipsum">
	{$post.short_description}</div></span>
         </div>
        <div class="sdsreadMore">
                  {assign var="options" value=null}
                        {$options.id_post = $post.id_post}  
                        {$options.slug = $post.link_rewrite}  
                         <span class="more"><a title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" class="r_more">{l s='Read more' mod='smartblog'} </a></span>
        </div>
        {if count($post.tags) > 0 && (is_array($post.tags[0]))}
                <div class="sdstags-update">
                    <span class="tags"><b>{l s='Tags:' mod='smartblog'} </b> 
                        {foreach from=$post.tags item=tag}
                            {assign var="options" value=null}
                            {$options.tag = $tag.name}
                            <a title="tag" href="{smartblog::GetSmartBlogLink('smartblog_tag',$options)}">{$tag.name}</a>
                        {/foreach}
                    </span>
                </div>
           {/if}
   </div>
</div>