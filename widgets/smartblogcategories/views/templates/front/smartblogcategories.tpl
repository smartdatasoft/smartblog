{if isset($categories) AND !empty($categories)}
  {if $display_dropdown}
    <div id="smartblogcategories"  class="block blogModule boxPlain">
      <h5 class='title_block'>{l s='Blog Categories' mod='smartblog'}</h5>
       <div id="category_blog_block_left" class="block_content ">
             <select onchange="document.location.href=this.options[this.selectedIndex].value;">
              {$categories}
            </select>
       </div>
    </div> 
  {else}
    <div id="smartblogcategories"  class="block blogModule boxPlain">
      <h5 class='title_block'>{l s='Blog Categories' mod='smartblog'}</h5>
       <div id="category_blog_block_left" class="block_content ">
             <ul class="tree {if $isDhtml}dhtml{/if}">
              {$categories}
            </ul>
       </div>
    </div>

    <script type="text/javascript">
    // <![CDATA[
        // we hide the tree only if JavaScript is activated
        $('div#smartblogcategories ul.dhtml').hide();
    // ]]>
    </script> 
  {/if}
{/if}