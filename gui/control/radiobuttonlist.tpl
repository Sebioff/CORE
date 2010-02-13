<?= $this->getTitle(); ?>
<br class="clear" />
<? foreach ($this->getItems() as $radio): ?>
	<? $radio->display(); ?>
	<br class="clear" />
<? endforeach; ?>