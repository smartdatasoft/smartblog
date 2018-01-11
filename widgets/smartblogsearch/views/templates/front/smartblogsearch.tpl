<div class="block blogModule boxPlain clearfix" id="smartblogsearch">
      <h2 class='sdstitle_block'><a href='{smartblog::GetSmartBlogLink('smartblog_list')}'>{l s="Blog Search" mod="smartblogsearch"}</a></h2>
	<div id="sdssearch_block_top" class="block_content">
		<form action="{smartblog::GetSmartBlogLink('smartblog_search')}" method="post" id="searchbox">
		    <input type="hidden" value="0" name="smartblogaction">
			<input type="text" value="" placeholder="Search" name="smartsearch" id="search_query_top" class="search_query form-control ac_input" autocomplete="off">
			<button class="btn btn-default button-search" name="smartblogsubmit" type="submit">
			<span>{l s='Search' mod='smartblogsearch'}</span>
			</button>
		</form>
	</div>
</div>




