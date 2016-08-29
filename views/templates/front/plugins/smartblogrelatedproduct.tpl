{if isset($products) AND !empty($products)}
    <div class="title_block">{l s='Related Products' mod='smartblog'}
   		</div>
    {include file="$tpl_dir./product-list.tpl" class='' id='relatedproduct'}
    
 
{/if}
 
