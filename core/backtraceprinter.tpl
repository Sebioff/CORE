<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Error</title>
		<link rel="stylesheet" type="text/css" href="<?= Router::get()->getStaticRoute('core_css', 'reset.css') ?>"/>
		<link rel="stylesheet" type="text/css" href="<?= Router::get()->transformPathToHTMLPath(dirname(__FILE__)) ?>/www/css/backtraceprinter.css"/>
	</head>
	<body>
		<div id="errorInfo">
			<span id="errorType"><?= $errorType ?>!</span>
			<span id="customMessage"><?= $customMessage ?></span>
		</div>
		<div id="backtrace">
			<? $nr = 0; ?>
			<? $traceCount = count($backtrace); ?>
			<? foreach ($backtrace as $backtraceMessage): ?>
				<div class="traceLine clearfix">
					<div class="traceNumber">#<?= $traceCount-$nr ?></div>
					<div class="traceMessage">
						<?= isset($backtraceMessage['class'])?$backtraceMessage['class'].$backtraceMessage['type'].$backtraceMessage['function']:$backtraceMessage['function'] ?>('<?= isset($backtraceMessage['args']) ? implode("', '", $backtraceMessage['args']) : '' ?>')
						<? if (isset($backtraceMessage['file'])): ?>
							in <?= $backtraceMessage['file'].'('.$backtraceMessage['line'].')' ?>
						<? endif;?>
					</div>
				</div>
				<? $nr++; ?>
			<? endforeach; ?>
		</div>
	</body>
</html>