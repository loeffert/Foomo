<div id="page">
	<div id="innerPage">
		<?= $view->partial('header') ?>
		<?= $view->partial('menu') ?>


		<?php
		/*
		$coreModules = array(\Foomo\Module::NAME, 'Foomo.TestRunner', 'Foomo.Services', 'Foomo.Docs' );
		$enabledModules = Foomo\Modules\Manager::getEnabledModules();
		$moduleLinks = array();
		foreach($enabledModules as $enabledModuleName) {
			if(Foomo\Modules\Manager::moduleHasFrontEnd($enabledModuleName)) {
				$moduleLinks[$enabledModuleName] = '<li><a href="modules/' . $enabledModuleName . '/index.php" title="' . Foomo\Modules\Manager::getModuleDescription($enabledModuleName) . '">'.$enabledModuleName.'</a></li>';
			}
		}
		*/
		?>
		<div id="fullContent">
			<h1>
			<?
				$hour = date('H');
				$user = $_SERVER['PHP_AUTH_USER'];
				switch(true) {
					case ($hour < 10 && $hour > 6):
						$key = 'GREET_GOOD_MORNING';
						break;
					case ($hour > 12 && $hour < 13):
						$key = 'GREET_LUNCH';
						break;
					case ($hour > 20 && $hour < 24):
						$key = 'GREET_LATE';
						break;
					case ($hour > 0 && $hour < 6):
						$key = 'GREET_LAUNCH';
						break;
					default:
						$key = 'GREET_DEFAULT';
				}
				printf($view->_($key), $view->escape($user));
			?>
			</h1>

			<hr>
			<br>

			<h2>Shortcuts</h2>

			<div class="greyBox">
				<div class="innerBox" style="margin: 5px 5px 5px 10px;">
					<b><?= $view->link('Reset the autoloader', 'resetAutoloader', array(), array('title' => 'when you write new classes you need to reset the autoloader in order to use them')); ?></b>
				</div>
			</div>

			<? if ($model->classMap): ?>
			<div class="whiteBox">
				<div class="innerBox" style="margin: 5px 5px 5px 10px;">
					<?= $model->classMap ?>
				</div>
			</div>
			<? endif; ?>

			<div class="greyBox">
				<div class="innerBox" style="margin: 5px 5px 5px 10px;">
					<b><a title="wanna start over?" href="<?= $view->asset('setup.php') ?>">Setup</a></b>
				</div>
			</div>

			<? if(Foomo\Session::getConf() && Foomo\Session::getConf()->enabled): ?>
			<div class="greyBox">
				<div class="innerBox" style="margin: 5px 5px 5px 10px;">
					<b><a class="overlay" href="<?= $view->asset('sessionGc.php') ?>">Collect Session Garbage</a></b>
				</div>
			</div>
			<? endif; ?>

			<div class="greyBox">
				<div class="innerBox" style="margin: 5px 5px 5px 10px;">
					<b><a title="may be your life saver" href="<?= $view->asset('hiccup.php') ?>">Hiccup</a></b> (You may want to bookmark that one!)
				</div>
			</div>

		</div>
		<?= $view->partial('footer') ?>
	</div>
</div>
<?= $view->partial('overlay') ?>

