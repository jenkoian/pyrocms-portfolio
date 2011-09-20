<?php if ($clients): ?>

	<h3><?php echo lang('client_list_title'); ?></h3>

	<?php echo form_open('admin/portfolio/clients/delete'); ?>

	<table border="0" class="table-list">
		<thead>
		<tr>
			<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
			<th><?php echo lang('client_client_label'); ?></th>
			<th width="200" class="align-center"><span><?php echo lang('client_actions_label'); ?></span></th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($clients as $client): ?>
			<tr>
				<td><?php echo form_checkbox('action_to[]', $client->id); ?></td>
				<td><?php echo $client->title; ?></td>
				<td class="align-center buttons buttons-small">
					<?php echo anchor('admin/portfolio/clients/edit/' . $client->id, lang('client_edit_label'), 'class="button edit"'); ?>
					<?php echo anchor('admin/portfolio/clients/delete/' . $client->id, lang('client_delete_label'), 'class="confirm button delete"') ;?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="buttons align-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete') )); ?>
	</div>

	<?php echo form_close(); ?>

<?php else: ?>
	<div class="blank-slate">
		<h2><?php echo lang('client_no_clients'); ?></h2>
	</div>
<?php endif; ?>