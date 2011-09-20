<h1><?php echo $item->title; ?></h1>

<p style="float:left; width: 40%;">
	<?php echo anchor('portfolio/' .date('Y/m', $item->created_on) .'/'. $item->slug, NULL, 'target="_blank"'); ?>
</p>

<p style="float:right; width: 40%; text-align: right;">
	<?php echo anchor('admin/portfolio/edit/'. $item->id, lang('portfolio_edit_label'), ' target="_parent"'); ?>
</p>

<iframe src="<?php echo site_url('portfolio/' .date('Y/m', $item->created_on) .'/'. $item->slug); ?>" width="99%" height="400"></iframe>