<?php echo form_open('admin/portfolio/action');?>

<h3><?php echo lang('portfolio_list_title');?></h3>

<?php if (!empty($portfolio)): ?>

	<table border="0" class="table-list">
		<thead>
			<tr>
				<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
				<th><?php echo lang('portfolio_item_label');?></th>
				<th class="width-10"><?php echo lang('portfolio_client_label');?></th>
				<th class="width-10"><?php echo lang('portfolio_date_label');?></th>
				<th class="width-5"><?php echo lang('portfolio_status_label');?></th>
				<th class="width-10"><span><?php echo lang('portfolio_actions_label');?></span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<div class="inner filtered"><?php $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($portfolio as $item): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $item->id);?></td>
					<td><?php echo $item->title;?></td>
					<td><?php echo $item->client_title;?></td>
					<td><?php echo format_date($item->created_on);?></td>
					<td><?php echo lang('portfolio_'.$item->status.'_label');?></td>
					<td>
						<?php echo anchor('admin/portfolio/preview/' . $item->id, lang($item->status == 'live' ? 'portfolio_view_label' : 'portfolio_preview_label'), 'rel="modal-large" class="iframe" target="_blank"') . ' | '; ?>
						<?php echo anchor('admin/portfolio/edit/' . $item->id, lang('portfolio_edit_label'));?> |
						<?php echo anchor('admin/portfolio/delete/' . $item->id, lang('portfolio_delete_label'), array('class'=>'confirm')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="buttons float-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete', 'publish'))); ?>
	</div>

<?php else: ?>
	<p><?php echo lang('portfolio_no_items');?></p>
<?php endif; ?>

<?php echo form_close();?>