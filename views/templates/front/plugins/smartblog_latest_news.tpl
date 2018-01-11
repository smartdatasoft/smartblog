<div class="block">
    <h2 class='title_block'>{l s='Latest News' mod='smartblog'}</h2>
    <div class="sdsblog-box-content">
        {if isset($view_data) AND !empty($view_data)}
            {assign var='i' value=1}
            {foreach from=$view_data item=post}

                    <div id="sds_blog_post" class="col-xs-12 col-sm-4 col-md-3">
                        <span class="news_module_image_holder">
                             <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">
                            {if $smartbloglink->getImageLink($post.link_rewrite, $post.id, 'home-default') != 'false'}
                                <img class="replace-2x img-responsive" src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id, 'home-default')}" alt="{$post.title|escape:'html':'UTF-8'}" title="{$post.title|escape:'html':'UTF-8'}"   itemprop="image" />
                            {/if}
                            </a>
                        </span>
                        <i class="icon icon-calendar"></i>
                        <span>{$post.date_added}</span>
                        <h4 class="sds_post_title"><a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">{$post.title}</a></h4>
                        <p>
                            {$post.short_description|escape:'htmlall':'UTF-8'}
                        </p>
                        <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}"  class="r_more btn btn-default button button-small"><span>{l s='Read More' mod='smartblog'}<i class="icon-chevron-right right"></i></span></a>
				 
						 
                    </div>
                
                {$i=$i+1}
            {/foreach}
        {/if}
     </div>
</div>