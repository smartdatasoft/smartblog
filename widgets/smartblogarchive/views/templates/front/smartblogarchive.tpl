{if isset($archives) AND !empty($archives)}
<div id="smartblogarchive"  class="block blogModule boxPlain">
    <h2 class='sdstitle_block'><a href="{smartblog::GetSmartBlogLink('smartblog_archive')}">{l s='Blog Archive' mod='smartblogarchive'}</a></h2>
   <div class="block_content">

{if $archive_type==2}
    <ul class="tree {if $isDhtml}dhtml{/if}">
             {foreach from=$archives item="archive"}    
              {assign var="linkurl_year" value=null}
              {$linkurl_year.year = $archive.year}                                   
    <li>
       <a   href="{smartblog::GetSmartBlogLink('smartblog_year',$linkurl_year)}" >{$archive.year}</a>
        <ul>
          {foreach from=$archive.month item="months"}
                   
                        {assign var="linkurl" value=null}
                                {$linkurl.year = $archive.year}
                            {$linkurl.month = $months.month}
                            {assign var="monthname" value=null}
                            {if $months.month == 1}{$monthname = 'January'}{elseif $months.month == 2}{$monthname = 'February'}{elseif $months.month == 3}
                            {$monthname = 'March'} {elseif $months.month == 4} {$monthname = 'Aprill'}{elseif $months.month == 5}{$monthname = 'May'}
                            {elseif $months.month == 6}{$monthname = 'June'}{elseif $months.month == 7}{$monthname = 'July'} {elseif $months.month == 8}
                            {$monthname = 'August'} {elseif $months.month == 9}{$monthname = 'September'}{elseif $months.month == 10} {$monthname = 'October'}
                            {elseif $months.month == 11}{$monthname = 'November'}{elseif $months.month == 12} {$monthname = 'December'}{/if}
           
            <li >
            <a   href="{smartblog::GetSmartBlogLink('smartblog_month',$linkurl)}" >{$monthname}-{$archive.year}</a>
     
            </li>
        {/foreach}
        </ul>
    </li>
     {/foreach}
      </ul>
      {* Javascript moved here to fix bug #PSCFI-151 *}
        <script type="text/javascript">
        // <![CDATA[
            // we hide the tree only if JavaScript is activated
            $('div#smartblogarchive ul.dhtml').hide();
        // ]]>
        </script>
{else}
                       
         <ul>
	{foreach from=$archives item="archive"}
                {foreach from=$archive.month item="months"}
               
                    {assign var="linkurl" value=null}
                            {$linkurl.year = $archive.year}
                        {$linkurl.month = $months.month}
                        {assign var="monthname" value=null}
                        {if $months.month == 1}{$monthname = 'January'}{elseif $months.month == 2}{$monthname = 'February'}{elseif $months.month == 3}
                        {$monthname = 'March'} {elseif $months.month == 4} {$monthname = 'Aprill'}{elseif $months.month == 5}{$monthname = 'May'}
                        {elseif $months.month == 6}{$monthname = 'June'}{elseif $months.month == 7}{$monthname = 'July'} {elseif $months.month == 8}
                        {$monthname = 'August'} {elseif $months.month == 9}{$monthname = 'September'}{elseif $months.month == 10} {$monthname = 'October'}
                        {elseif $months.month == 11}{$monthname = 'November'}{elseif $months.month == 12} {$monthname = 'December'}{/if}
              
    <li>
		  <a href="{smartblog::GetSmartBlogLink('smartblog_month',$linkurl)}">{$monthname}-{$archive.year}</a>
		</li>
                {/foreach}
	{/foreach}
        </ul>
    {/if} <!-- end of month year wise -->
   </div>
</div>
{/if}