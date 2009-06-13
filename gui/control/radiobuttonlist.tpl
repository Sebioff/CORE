<? foreach ($this->getItems() as $radio): ?>
	<? $radio->display(); ?>
<? endforeach; ?>
<?= $this->getTitle(); ?>