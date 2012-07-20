<div class="adminUsers view">
<h2><?php  echo __('Admin User'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($adminUser['AdminUser']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
			<?php echo h($adminUser['AdminUser']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($adminUser['AdminUser']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($adminUser['AdminUser']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($adminUser['AdminUser']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Admin User'), array('action' => 'edit', $adminUser['AdminUser']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Admin User'), array('action' => 'delete', $adminUser['AdminUser']['id']), null, __('Are you sure you want to delete # %s?', $adminUser['AdminUser']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Admin Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Admin User'), array('action' => 'add')); ?> </li>
	</ul>
</div>
