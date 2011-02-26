<ul id="<?= $this->getID(); ?>" <?= $this->getAttributeString(); ?>>
	<? foreach ($this->getItems() as $radio): ?>
		<li><? $radio->display(); ?> <? $this->displayLabelForPanel($radio->getName()); ?></li>
	<? endforeach; ?>
</ul>