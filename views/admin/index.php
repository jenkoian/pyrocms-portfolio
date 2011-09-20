<?php if ($portfolio): ?>

	<?php echo form_open('admin/portfolio/action'); ?>

	<table border="0" class="table-list">
		<thead>
			<tr>
				<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
				<th><?php echo lang('portfolio_item_label'); ?></th>
				<th><?php echo lang('portfolio_client_label'); ?></th>
				<th><?php echo lang('portfolio_date_label'); ?></th>
				<th><?php echo lang('portfolio_written_by_label'); ?></th>
				<th><?php echo lang('portfolio_status_label'); ?></th>
				<th width="320" class="align-center"><span><?php echo lang('portfolio_actions_label'); ?></span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($portfolio as $item): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $item->id); ?></td>
					<td><?php echo $item->title; ?></td>
					<td><?php echo $item->client_title; ?></td>
					<td><?php echo format_date($item->created_on); ?></td>
					<td>
					<?php if ($item->author): ?>
						<?php echo anchor('user/' . $item->author_id, $item->author->display_name, 'target="_blank"'); ?>
					<?php else: ?>
						<?php echo lang('portfolio_author_unknown'); ?>
					<?php endif; ?>
					</td>
					<td><?php echo lang('portfolio_'.$item->status.'_label'); ?></td>
					<td class="align-center buttons buttons-small">
						<?php echo anchor('admin/portfolio/preview/' . $item->id, lang($item->status == 'live' ? 'portfolio_view_label' : 'portfolio_preview_label'), 'rel="modal-large" class="iframe button preview" target="_blank"'); ?>
						<?php echo anchor('admin/portfolio/edit/' . $item->id, lang('portfolio_edit_label'), 'class="button edit"'); ?>
						<?php echo anchor('admin/portfolio/delete/' . $item->id, lang('portfolio_delete_label'), array('class'=>'confirm button delete')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="buttons align-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete', 'publish'))); ?>
	</div>

	<?php echo form_close(); ?>

<?php else: ?>
	<div class="blank-slate">
		<h2><?php echo lang('portfolio_currently_no_items'); ?></h2>
	</div>
<?php endif; ?>