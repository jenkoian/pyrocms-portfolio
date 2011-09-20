<?php
// Function in a view, yuck, should be done in the model this.
function getFirstImageInPost($itemBody) {
    preg_match('/<img[^>]+>/i',$itemBody, $result);     
    
    if (!empty($result[0])) {
        return $result[0];
    }
    
    return;
}
?>

<h1 id="page_title"><?php echo $client->title; ?></h1>
<?php if (!empty($portfolio)): ?>
    <div id="three-col">
        <?php foreach ($portfolio as $item): ?>
                <div class="column portfolio_item">		
                    <?php if ($item->thumbnail_id) : ?>
                        <?php echo  anchor('portfolio/' .date('Y/m', $item->created_on) .'/'. $item->slug, img(array('src' => site_url() . 'files/thumb/' . $item->thumbnail_id.'/1000/110', 'alt' => $item->title))); ?> 
                    <?php endif; ?>
                    <h2><?php echo  anchor('portfolio/' .date('Y/m', $item->created_on) .'/'. $item->slug, $item->title); ?></h2>									
                    <div class="item_body">
                        <?php echo word_limiter($item->intro, 15); ?>                    
                        <p><?php echo  anchor('portfolio/' .date('Y/m', $item->created_on) .'/'. $item->slug, 'See the work...'); ?></p>
                    </div>
                </div>    
        <?php endforeach; ?>
    </div>

<?php echo $pagination['links']; ?>

<?php else: ?>
	<p><?php echo lang('portfolio_currently_no_posts');?></p>
<?php endif; ?>