<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('TURBO') ?></li>
        <li><?= $this->Html->link(__('Messages'), ['controller' => 'Messages','action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Participants'), ['controller' => 'Participants','action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Users'), ['controller' => 'Messages', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="users index large-9 medium-8 columns content">
    <h3><?= __('Welcome to Trubo') ?></h3>
    <table cellpadding="0" cellspacing="0">
        
    </table>
</div>
