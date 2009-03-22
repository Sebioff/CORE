<select name="<?= $this->getID() ?>" id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? foreach($this->getValues() as $key => $value): ?>
		<option value="<?= $key ?>"<?= ($key == $this->getKey()) ? ' selected="selected"' : '' ?>><?= $value ?></option>
	<? endforeach ?>
</select>