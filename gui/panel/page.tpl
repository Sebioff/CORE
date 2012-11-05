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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= Language_Scriptlet::get()->getCurrentLanguage() ?>" xml:lang="<?= Language_Scriptlet::get()->getCurrentLanguage() ?>">
	<head>
		<title><?= $this->getPageTitle(); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="imagetoolbar" content="no" />
		<? foreach ($this->getMetaTags() as $key => $value): ?>
			<meta name="<?= $key; ?>" content="<?= $value; ?>" />
		<? endforeach; ?>
		<? foreach ($this->getCssRouteReferences() as $cssRoute): ?>
			<link rel="stylesheet" type="text/css" href="<?= $cssRoute; ?>" />
		<? endforeach; ?>
	</head>
	<body <?= $this->getAttributeString(); ?>>
		<? $this->display(); ?>
		<? foreach ($this->getJsRouteReferences() as $jsRoute): ?>
			<script type="text/javascript" src="<?= $jsRoute; ?>"></script>
		<? endforeach; ?>
		<? if ($jsAfterContent = $this->getJsAfterContent()): ?>
			<script type="text/javascript">
				<?= $jsAfterContent; ?>
			</script>
		<? endif ?>
	</body>
</html>