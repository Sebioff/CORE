<div id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? foreach($this->panels as $panel): ?>
		<? if(!($panel instanceof GUI_Control_Submitbutton) && $panel->getTitle()): ?>
			<? $this->displayLabelForPanel($panel->getName()) ?>: 
		<? endif; ?>
		<? $panel->display() ?>
	<? endforeach; ?>
	
	<? $this->displayPanel('pages'); ?>
</div>