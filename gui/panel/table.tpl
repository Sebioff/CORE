<? if (count($this->getLines()) + count($this->getHeaders()) + count($this->getFooters()) == 0): ?>
	<? $this->addError('Die Tabelle enthält keine Zeilen'); ?>
<? endif; ?> 
<? if ($this->hasErrors()): ?>
	<? $this->displayErrors(); ?>
<? endif; ?>
<table id="<?= $this->getID(); ?>" <?= $this->getAttributeString(); ?>>
	<? $i = 0; ?>
	<? if (count($this->getHeaders()) > 0): ?>
		<thead>
			<? foreach ($this->getHeaders() as $row): ?>
				<tr>
					<? foreach ($row as $column): ?>
						<th>
							<? if ($column instanceof GUI_Panel): ?>
								<? $column->display(); ?>
							<? else: ?>
								<?= $column; ?>
							<? endif; ?>
						</th>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
		</thead>
	<? endif; ?>
	<? if (count($this->getLines()) > 0): ?>
		<tbody>
			<? foreach ($this->getLines() as $row): ?>
				<tr>
					<? foreach ($row as $column): ?>
						<td>
							<? if ($column instanceof GUI_Panel): ?>
								<? $column->display(); ?>
							<? else: ?>
								<?= $column; ?>
							<? endif; ?>
						</td>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
		</tbody>
	<? endif; ?>
	<? if (count($this->getFooters()) > 0): ?>
		<tfoot>
			<? foreach ($this->getFooters() as $row): ?>
				<tr>
					<? foreach ($row as $column): ?>
						<td>
							<? if ($column instanceof GUI_Panel): ?>
								<? $column->display(); ?>
							<? else: ?>
								<?= $column; ?>
							<? endif; ?>
						</td>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
		</tfoot>
	<? endif; ?>
</table>