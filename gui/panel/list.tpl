<ul id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? foreach ($this->getItems() as $item): ?>
		<li>
			<? if ($item instanceof GUI_Panel): ?>
				<? $item->display(); ?>
			<? else: ?>
				<?= $item; ?>
			<? endif; ?>
		</li>
	<? endforeach; ?>
</ul>