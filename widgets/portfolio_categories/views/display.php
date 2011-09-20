<?php if(is_array($categories)): ?>
<ul>
	<?php foreach($categories as $category): ?>    
	<li 
            <?php if ( strpos( uri_string(), "portfolio/category/{$category->slug}" ) !== false ) : ?>
                class="current"
            <?php endif; ?>
                
            <?php if ( isset($item->categories) && in_array( $category->id, $item->categories ) ) : ?>
                class="current"
            <?php endif; ?>            
        >
		<?php echo anchor("portfolio/category/{$category->slug}", $category->title); ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
