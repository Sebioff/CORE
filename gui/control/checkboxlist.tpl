<ul id="<?= $this->getID(); ?>" <?= $this->getAttributeString(); ?>>
	<? foreach ($this->getItems() as $checkbox): ?>
		<li><? $checkbox->display(); ?> <? $this->displayLabelForPanel($checkbox->getName()); ?></li>
	<? endforeach; ?>
</ul>