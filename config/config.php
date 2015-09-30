<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD'], 4, array
(
	'cleverReach' => array
	(
		'clr_subscribe'   => 'Dreebit\ModuleClrSubscribe',
		'clr_unsubscribe'   => 'Dreebit\ModuleClrUnsubscribe',
	)
));


/**
 * Register hooks
 */
$GLOBALS['TL_HOOKS']['deactivateRecipient'][] = array('Dreebit\Hooks\Newsletter', 'deactivateRecipient');
$GLOBALS['TL_HOOKS']['activateRecipient'][] = array('Dreebit\Hooks\Newsletter', 'activateRecipient');