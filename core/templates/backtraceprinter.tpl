<html>
  <head>
    <title>B‰‰m!</title>
  	<link rel="stylesheet" type="text/css" href="CORE/core/www/css/backtraceprinter.css"/>
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
			  <?= isset($backtraceMessage['class'])?$backtraceMessage['class'].$backtraceMessage['type'].$backtraceMessage['function']:$backtraceMessage['function'] ?>('<?= isset($backtraceMessage['args']) ? join("', '",$backtraceMessage['args']) : '' ?>')
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