<div id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? $pages = count($this->panels); ?>
	<? $i = 0; ?>
	<? foreach($this->panels as $panel): ?>
		<? $i++; ?>
		<? $panel->display() ?><?= ($i < $pages) ? ', ' : '' ?>
	<? endforeach; ?>
</div>