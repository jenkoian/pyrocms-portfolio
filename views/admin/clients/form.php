<?php if ($this->controller == 'admin_clients' && $this->method === 'edit'): ?>
<h3><?php echo sprintf(lang('client_edit_title'), $client->title);?></h3>

<?php else: ?>
<h3><?php echo lang('client_create_title');?></h3>

<?php endif; ?>

<?php echo form_open($this->uri->uri_string(), 'class="crud" id="clients"'); ?>

<fieldset>
	<ol>
		<li class="even">
		<label for="title"><?php echo lang('client_title_label');?></label>
		<?php echo  form_input('title', $client->title); ?>
		<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
		</li>
	</ol>

	<div class="buttons float-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
	</div>
</fieldset>

<?php echo form_close(); ?>