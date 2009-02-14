<? foreach($this->panels as $panel): ?>
	<? if(!($panel instanceof GUI_Control_Submitbutton)): ?>
		<? $this->displayLabelForPanel($panel->getName()) ?>: 
	<? endif; ?>
	<? $panel->display() ?>
	
<? endforeach; ?>