<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('请注册帐号'); ?></legend>
    <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->input('repassword',array('type'=>'password'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('注册')); ?>
</div>