<?php if(is_array($clients)): ?>
<ul>
	<?php foreach($clients as $client): ?>    
	<li 
            <?php if ( strpos( uri_string(), "portfolio/client/{$client->slug}" ) !== false ) : ?>
                class="current"
            <?php endif; ?>                       
                
            <?php if ( isset($item->client_id) && $item->client_id == $client->id ) : ?>
                class="current"
            <?php endif; ?>                
        >
		<?php echo anchor("portfolio/client/{$client->slug}", $client->title); ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
