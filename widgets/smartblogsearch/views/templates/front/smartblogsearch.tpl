<div class="block block-blog blogModule boxPlain clearfix" id="smartblogsearch">
      <h4 class="text-uppercase h6 hidden-sm-down"><a href='{smartblog::GetSmartBlogLink('smartblog_list')}'>{l s="Blog Search" mod="smartblogsearch"}</a></h4>
	<div id="sdssearch_block_top" class="block_content">
		<form action="{smartblog::GetSmartBlogLink('smartblog_search')}" method="post" id="searchbox">
		    <input type="hidden" value="0" name="smartblogaction">
			<input type="text" placeholder="Search" name="smartsearch" id="search_query_top" class="search_query form-control ac_input" autocomplete="off" value="{$smartsearch}">
			<button class="btn btn-default btn-blog-search" name="smartblogsubmit" type="submit">
				<i class="material-icons search"></i>
			<span>{l s='' mod='smartblogsearch'}</span>
			</button>
		</form>
	</div>
</div>




