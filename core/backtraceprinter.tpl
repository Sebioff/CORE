<?php

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

/*
 * NOTE: no short open tags in this file because the error page must be as
 * robust as possible and work with disabled short open tags as well.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Error</title>
		<link rel="stylesheet" type="text/css" href="<?php echo Router::get()->getStaticRoute('core_css', 'reset.css'); ?>"/>
		<link rel="stylesheet" type="text/css" href="<?php echo Router::get()->transformPathToHTMLPath(dirname(__FILE__)); ?>/www/css/backtraceprinter.css"/>
	</head>
	<body>
		<div id="errorInfo">
			<span id="errorType"><?php echo $errorType; ?>!</span>
			<span id="customMessage"><?php echo $customMessage; ?></span>
		</div>
		<div id="backtrace">
			<?php $nr = 0; ?>
			<?php $traceCount = count($backtrace); ?>
			<?php foreach ($backtrace as $backtraceMessage): ?>
				<div class="traceLine clearfix">
					<div class="traceNumber">#<?php echo ($traceCount - $nr); ?></div>
					<div class="traceMessage">
						<?php echo isset($backtraceMessage['class']) ? $backtraceMessage['class'].$backtraceMessage['type'].$backtraceMessage['function'] : $backtraceMessage['function']; ?>(<?= isset($backtraceMessage['args']) ? implode(', ', $backtraceMessage['args']) : ''; ?>)
						<?php if (isset($backtraceMessage['file'])): ?>
							in <?php echo $backtraceMessage['file'].'('.$backtraceMessage['line'].')'; ?>
						<?php endif;?>
					</div>
				</div>
				<?php $nr++; ?>
			<?php endforeach; ?>
		</div>
	</body>
</html>