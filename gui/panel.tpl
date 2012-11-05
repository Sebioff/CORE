<?
/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */
?>
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