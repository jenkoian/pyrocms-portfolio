<h2 id="page_title"><?php echo lang('portfolio_archive_title');?></h2>
<h3><?php echo $month_year;?></h3>
<?php if (!empty($portfolio)): ?>
<?php foreach ($portfolio as $item): ?>
	<div class="portfolio_item">		
		<div class="item_heading">
			<h2><?php echo  anchor('portfolio/' .date('Y/m', $item->created_on) .'/'. $item->slug, $item->title); ?></h2>
			<p class="item_date"><?php echo lang('portfolio_itemed_label');?>: <?php echo format_date($item->created_on); ?></p>
			<?php if($item->client_slug): ?>
			<p class="item_client">
				<?php echo lang('portfolio_client_label');?>: <?php echo anchor('portfolio/client/'.$item->client_slug, $item->client_title);?>
			</p>
			<?php endif; ?>
		</div>
		<div class="item_body">
			<?php echo $item->intro; ?>
		</div>
	</div>
<?php endforeach; ?>

<?php echo $pagination['links']; ?>

<?php else: ?>
	<p><?php echo lang('portfolio_currently_no_items');?></p>
<?php endif; ?>