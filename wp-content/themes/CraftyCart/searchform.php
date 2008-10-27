<div class="sidebar">
	<form method="get" id="search-form" action="<?php bloginfo('url'); ?>/">
		<fieldset>
			<label for="s" class="overlabel">Search...</label>
			<input class="text" type="text" name="s" id="s" size="20" value="<?php if(is_search()){the_search_query();} ?>" />
			<input class="button-search" type="image" src="<?php bloginfo('template_url'); ?>/images/button-search.png" alt="Search" title="Search" value="Search" id="searchsubmit" />
		</fieldset>
	</form>
</div>