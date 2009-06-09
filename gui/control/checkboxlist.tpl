<ul id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? foreach ($this->getItems() as $checkbox): ?>
		<li><? $checkbox->display(); ?></li>
	<? endforeach; ?>
</ul>