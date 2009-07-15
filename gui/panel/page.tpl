<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= Language_Scriptlet::get()->getCurrentLanguage() ?>" xml:lang="<?= Language_Scriptlet::get()->getCurrentLanguage() ?>">
	<head>
		<title><?= $this->getPageTitle() ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="imagetoolbar" content="no" />
		<? foreach ($this->getMetaTags() as $key => $value): ?>
			<meta name="<?= $key ?>" content="<?= $value ?>" />
		<? endforeach ?>
		<? foreach ($this->getCssRouteReferences() as $cssRoute): ?>
			<link rel="stylesheet" type="text/css" href="<?= $cssRoute ?>" /> 
		<? endforeach ?>
		<? foreach ($this->getJsRouteReferences() as $jsRoute): ?>
			<script type="text/javascript" src="<?= $jsRoute ?>"></script>
		<? endforeach ?>
	</head>
	<body <?= $this->getAttributeString() ?>>
		<? $this->display() ?>
		<? if ($jsAfterContent = $this->getJsAfterContent()): ?>
			<script type="text/javascript">
				<?= $jsAfterContent ?>
			</script>
		<? endif ?>
	</body>
</html>