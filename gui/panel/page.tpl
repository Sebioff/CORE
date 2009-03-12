<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= Language_Scriptlet::get()->getCurrentLanguage() ?>" xml:lang="<?= Language_Scriptlet::get()->getCurrentLanguage() ?>">
	<head>
		<title><?= $this->getPageTitle() ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="imagetoolbar" content="no" />
	</head>
	<body>
		<? $this->display() ?>
	</body>
</html>