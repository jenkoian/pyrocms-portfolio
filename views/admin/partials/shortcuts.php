<nav id="shortcuts">
	<h6><?php echo lang('cp_shortcuts_title'); ?></h6>
	<ul>
		<li><?php echo anchor('admin/portfolio/create', lang('portfolio_create_title'), 'class="add"') ?></li>
		<li><?php echo anchor('admin/portfolio', lang('portfolio_list_title')); ?></li>
		<li><?php echo anchor('admin/portfolio/clients/create', lang('client_create_title'), 'class="add"'); ?></li>
		<li><?php echo anchor('admin/portfolio/clients', lang('client_list_title'))?></li>
		<li><?php echo anchor('admin/portfolio/categories/create', lang('category_create_title'), 'class="add"'); ?></li>
		<li><?php echo anchor('admin/portfolio/categories', lang('category_list_title'))?></li>                
	</ul>
	<br class="clear-both" />
</nav>