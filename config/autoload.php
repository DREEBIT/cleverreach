<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Dreebit',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Dreebit\CleverReach'          => 'system/modules/dreebit_cleverreach/classes/CleverReach.php',

	// Modules
	'Dreebit\ModuleClrSubscribe'   => 'system/modules/dreebit_cleverreach/modules/ModuleClrSubscribe.php',
	'Dreebit\ModuleClrUnsubscribe' => 'system/modules/dreebit_cleverreach/modules/ModuleClrUnsubscribe.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'clr_subscribe' => 'system/modules/dreebit_cleverreach/templates/newsletter',
));
