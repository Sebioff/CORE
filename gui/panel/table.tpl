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
		</tbody>
	<? endif; ?>
</table>