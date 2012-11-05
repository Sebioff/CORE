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
<? if (count($this->getLines()) + count($this->getHeaders()) + count($this->getFooters()) == 0): ?>
	<? $this->addError('Die Tabelle enthält keine Zeilen'); ?>
<? endif; ?>
<? if ($this->hasErrors()): ?>
	<? $this->displayErrors(); ?>
<? endif; ?>
<table id="<?= $this->getID(); ?>" <?= $this->getAttributeString(); ?>>
	<? $rows = 0; ?>
	<? if (count($this->getHeaders()) > 0): ?>
		<thead>
			<? foreach ($this->getHeaders() as $header): ?>
				<tr <?= $this->getTrAttributeString($rows); ?>>
					<? $columns = 0; ?>
					<? foreach ($header as $column): ?>
						<th <?= $this->getTdAttributeString($columns, $rows); ?>>
							<? $this->displayCell($column); ?>
						</th>
						<? $columns++; ?>
					<? endforeach; ?>
				</tr>
				<? $rows++; ?>
			<? endforeach; ?>
		</thead>
	<? endif; ?>
	<? if (count($this->getFooters()) > 0): ?>
		<tfoot>
			<? foreach ($this->getFooters() as $row): ?>
				<tr <?= $this->getTrAttributeString($rows); ?>>
					<? $columns = 0; ?>
					<? foreach ($row as $column): ?>
						<td <?= $this->getTdAttributeString($columns, $rows); ?>>
							<? $this->displayCell($column); ?>
						</td>
						<? $columns++; ?>
					<? endforeach; ?>
				</tr>
				<? $rows++; ?>
			<? endforeach; ?>
		</tfoot>
	<? endif; ?>
	<? if (count($this->getLines()) > 0): ?>
		<tbody>
			<? foreach ($this->getLines() as $row): ?>
				<tr <?= $this->getTrAttributeString($rows); ?>>
					<? $columns = 0; ?>
					<? foreach ($row as $column): ?>
						<td <?= $this->getTdAttributeString($columns, $rows); ?>>
							<? $this->displayCell($column); ?>
						</td>
						<? $columns++; ?>
					<? endforeach; ?>
				</tr>
				<? $rows++; ?>
			<? endforeach; ?>
			<? if ($this->getFoldEvery() > 0): ?>
				<tr id="<?= $this->getAjaxID(); ?>-fold">
					<td colspan="<?= $this->getColumnCount(); ?>">
						<? $this->displayPanel('foldlink'); ?>
					</td>
				</tr>
			<? endif; ?>
		</tbody>
	<? endif; ?>
</table>