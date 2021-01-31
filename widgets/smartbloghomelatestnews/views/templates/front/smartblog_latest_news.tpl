<div class="block">
    <h2 class='title_block'>{l s='Latest News' mod='smartblog'}</h2>
    <div class="sdsblog-box-content">
        {if isset($view_data) AND !empty($view_data)}
            {foreach from=$view_data item=post}
                {assign var='img_url' value=$smartbloglink->getImageLink($post.link_rewrite, $post.id, 'home-default')}
                <div id="sds_blog_post" class="col-xs-12 col-sm-4 col-md-4">
                    <span class="news_module_image_holder news_home_image_holder">
                        {if $img_url != 'false'}
                        <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">
                        <img class="replace-2x img-responsive" src="{$img_url}" alt="{$post.title|escape:'html':'UTF-8'}" title="{$post.title|escape:'html':'UTF-8'}"   itemprop="image" />
                        </a>
                        {/if}
                    </span>
                    <h4 class="sds_post_title sds_post_title_home"><a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">{SmartBlogPost::subStr($post.title,60)}</a></h4>
                    <i class="icon icon-calendar"></i>
                    <span class="sds_post_date">{$post.date_added}</span>
			 
					 
                </div>
            {/foreach}
        {/if}
     </div>
</div>