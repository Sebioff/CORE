<? if ($this->hasErrors()): ?>
	<div class="core_common_error">
		<? $this->displayErrors(); ?>
	</div>
<? endif; ?>

<? foreach ($this->panels as $panel): ?>
	<? if (!($panel instanceof GUI_Control_Submitbutton) && $panel->getTitle()): ?>
		<? $this->displayLabelForPanel($panel->getName()); ?>:
	<? endif; ?>
	<? $panel->display(); ?> <? $panel->displayErrors(); ?>
<? endforeach; ?>