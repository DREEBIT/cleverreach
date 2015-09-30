<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{cleverreach_legend},clr_wsdl_url, clr_api_key';

$GLOBALS['TL_DCA']['tl_settings']['fields']['clr_wsdl_url'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['clr_wsdl_url'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['clr_api_key'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['clr_api_key'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50')
);