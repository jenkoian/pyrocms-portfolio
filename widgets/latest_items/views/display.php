<ul class="navigation">
	<?php foreach($portfolio_widget as $item_widget): ?>
		<li><?php echo anchor('item/'.date('Y/m', $item_widget->created_on) .'/'.$item_widget->slug, $item_widget->title); ?></li>
	<?php endforeach; ?>
</ul>