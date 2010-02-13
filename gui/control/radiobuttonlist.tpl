<ul id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? foreach ($this->getItems() as $radio): ?>
		<li><? $radio->display(); ?></li>
	<? endforeach; ?>
</ul>