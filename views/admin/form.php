<?php if ($this->method == 'create'): ?>
	<h3><?php echo lang('portfolio_create_title'); ?></h3>
<?php else: ?>
		<h3><?php echo sprintf(lang('portfolio_edit_title'), $item->title); ?></h3>
<?php endif; ?>

<?php echo form_open(uri_string(), 'class="crud"'); ?>

<div class="tabs">

	<ul class="tab-menu">
		<li><a href="#portfolio-content-tab"><span><?php echo lang('portfolio_content_label'); ?></span></a></li>
		<li><a href="#portfolio-options-tab"><span><?php echo lang('portfolio_options_label'); ?></span></a></li>
	</ul>

	<!-- Content tab -->
	<div id="portfolio-content-tab">
		<ol>
			<li>
				<label for="title"><?php echo lang('portfolio_title_label'); ?></label>
				<?php echo form_input('title', htmlspecialchars_decode($item->title), 'maxlength="100"'); ?>
				<span class="required-icon tooltip"><?php echo lang('required_label'); ?></span>
			</li>
			<li class="even">
				<label for="slug"><?php echo lang('portfolio_slug_label'); ?></label>
				<?php echo form_input('slug', $item->slug, 'maxlength="100" class="width-20"'); ?>
				<span class="required-icon tooltip"><?php echo lang('required_label'); ?></span>
			</li>
			<li>
				<label for="status"><?php echo lang('portfolio_status_label'); ?></label>
				<?php echo form_dropdown('status', array('draft' => lang('portfolio_draft_label'), 'live' => lang('portfolio_live_label')), $item->status) ?>
			</li>
                        <li class="thumbnail-manage even">
                                <label for="thumbnail_id"><?php echo lang('portfolio_thumbnail_label'); ?></label>
                                <select name="thumbnail_id" id="thumbnail_id">

                                        <?php if ( ! empty($item->thumbnail_id) ): ?>
                                        <!-- Current thumbnail -->
                                        <optgroup label="Current">
                                                <?php foreach ( $portfolio_images as $image ): if ( $image->id == $item->thumbnail_id ): ?>
                                                <option value="<?php echo $item->thumbnail_id; ?>">
                                                        <?php echo $image->name; ?>
                                                </option>
                                                <?php break; endif; endforeach; ?>
                                        </optgroup>
                                        <?php endif; ?>

                                        <!-- Available thumbnails -->
                                        <optgroup label="Thumbnails">
                                                <option value="0"><?php echo lang('portfolio_no_thumb_label'); ?></option>
                                                <?php foreach ( $portfolio_images as $image ): ?>
                                                <option value="<?php echo $image->id; ?>">
                                                        <?php echo $image->name; ?>
                                                </option>
                                                <?php endforeach; ?>
                                        </optgroup>

                                </select>
                        </li>                        
			<li>
				<label class="intro" for="intro"><?php echo lang('portfolio_intro_label'); ?></label>
				<?php echo form_textarea(array('id' => 'intro', 'name' => 'intro', 'value' => $item->intro, 'rows' => 5, 'class' => 'wysiwyg-simple')); ?>
			</li>
			<li class="even">
				<?php echo form_textarea(array('id' => 'body', 'name' => 'body', 'value' => $item->body, 'rows' => 50, 'class' => 'wysiwyg-advanced')); ?>
			</li>
	
		</ol>
	</div>

	<!-- Options tab -->
	<div id="portfolio-options-tab">
		<ol>
			<li>
				<label for="client_id"><?php echo lang('portfolio_client_label'); ?></label>
				<?php echo form_dropdown('client_id', array(lang('portfolio_no_client_select_label')) + $clients, @$item->client_id) ?>
					[ <?php echo anchor('admin/portfolio/clients/create', lang('portfolio_new_client_label'), 'target="_blank"'); ?> ]
			</li>
			<li class="even category">
				<label for="category_id"><?php echo lang('portfolio_category_label'); ?></label>
				<?php echo form_multiselect('category_id[]', array(lang('portfolio_no_category_select_label')) + $categories, @$item->categories) ?>
					[ <?php echo anchor('admin/portfolio/categories/create', lang('portfolio_new_category_label'), 'target="_blank"'); ?> ]
			</li>                        
			<li class="date-meta">
				<label><?php echo lang('portfolio_date_label'); ?></label>
				<div style="float:left;">
					<?php echo form_input('created_on', date('Y-m-d', $item->created_on), 'maxlength="10" id="datepicker" class="text width-20"'); ?>
				</div>
				<label class="time-meta"><?php echo lang('portfolio_time_label'); ?></label>
				<?php echo form_dropdown('created_on_hour', $hours, date('H', $item->created_on)) ?>
				<?php echo form_dropdown('created_on_minute', $minutes, date('i', ltrim($item->created_on, '0'))) ?>
			</li>
		</ol>
	</div>

</div>

<div class="buttons float-right padding-top">
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); ?>
</div>

<?php echo form_close(); ?>