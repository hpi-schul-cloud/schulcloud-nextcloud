<?php
/** @var $l \OCP\IL10N */
/** @var $_ array */

script('groupfoldersautomation', 'script');		// JavaScript file
style('groupfoldersautomation', 'style');		// CSS file
?>

<div id="app-settings">
	<?php if (empty($_['this_config_var'])): ?>

	<h2><?php p($l->t('There is currently no configuration available')); ?></h2>

	<?php endif; ?>
</div>
