<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Messages'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Home'), ['controller' => 'TurboHome', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="messages form large-9 medium-8 columns content">
    <?= $this->Form->create($message) ?>
    <fieldset>
        <legend><?= __('Send Money') ?></legend>
        <?php
            echo $this->Form->input('phone');
            echo $this->Form->input('recipient');
            echo $this->Form->input('amount', array('id' => 'sendB2C'));
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
