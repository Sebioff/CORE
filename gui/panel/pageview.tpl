<div id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? foreach($this->panels as $panel): ?>
		<? if ($panel->getName() != 'pages'): ?>
			<? if(!($panel instanceof GUI_Control_Submitbutton) && $panel->getTitle()): ?>
				<? $this->displayLabelForPanel($panel->getName()) ?>: 
			<? endif; ?>
			<? $panel->display() ?>
		<? endif; ?>
	<? endforeach; ?>
	
	<? $this->displayPanel('pages'); ?>
</div>