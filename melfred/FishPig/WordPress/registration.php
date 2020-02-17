<?php
/*
 *
 * WordPress
 * https://fishpig.co.uk/
 *
 */
use \Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
	ComponentRegistrar::MODULE,
	'FishPig_WordPress',
	BP . '/app/code/FishPig/WordPress'
);
