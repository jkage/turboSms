<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Transactions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Home'), ['controller' => 'TurboHome', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="messages form large-9 medium-8 columns content">

    <div class="col-md-8">
        <?= $this->Form->create('Payments',['type' => 'file','url' => ['controller'=>'Payments','action' => 'filesend'],'class'=>'form-inline','role'=>'form',]) ?>
        <div class="form-group">
            <label class="sr-only" for="csv"> CSV </label>
            <?php echo $this->Form->input('csv', ['type'=>'file','class' => 'form-control', 'label' => false, 'placeholder' => 'csv upload',]); ?>
        </div>
        <button type="submit" class="btn btn-default"> Upload </button>
    <?= $this->Form->end() ?>
</div>
    
</div>
