<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

use Contao\ModuleModel;
use Dreebit\CleverReach;

/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['clr_subscribe']   = '{title_legend},name,headline,type;{config_legend},clr_groups,clr_form;{redirect_legend},jumpTo;{template_legend:hide},clr_template,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['clr_unsubscribe'] = '{title_legend},name,headline,type;{config_legend},clr_groups;{redirect_legend},jumpTo;{template_legend:hide},clr_template,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['clr_groups'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['clr_groups'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_clr', 'getGroups'),
	'eval'                    => array('multiple'=>true, 'mandatory'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['clr_form'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['clr_form'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options_callback'        => array('tl_module_clr', 'getForms'),
	'eval'                    => array('multiple'=>true, 'mandatory'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['clr_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['clr_template'],
	'default'                 => 'clr_subscribe',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_clr', 'getCleverReachTemplates'),
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_module_clr', 'jumpToMandatory');

class tl_module_clr extends Backend
{
	/**
	 * @param DataContainer $dc
	 */
	public function jumpToMandatory(DataContainer $dc)
	{
		if (ModuleModel::findById($dc->id)->type === 'clr_unsubscribe')
		{
			$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['mandatory'] = true;
		}
	}


	/**
	 * Get all groups and return them as array
	 *
	 * @return array
	 */
	public function getGroups()
	{
		if (isset($this->arrCache['clr_groups']))
		{
			return $this->arrCache['clr_groups'];
		}

		$this->arrCache['clr_groups'] = array();

		$objCleverReach = new CleverReach();
		$arrGroups = $objCleverReach->getGroupList();

		foreach ($arrGroups AS $objGroup)
		{
			$this->arrCache['clr_groups'][$objGroup->id] = $objGroup->name;
		}

		return $this->arrCache['clr_groups'];
	}


	/**
	 * Get all forms and return them as array
	 *
	 * @return array
	 */
	public function getForms()
	{
		$arrResult = array();

		$arrGroups = $this->getGroups();

		foreach ($arrGroups AS $strId => $strName)
		{
			$objCleverReach = new CleverReach();
			$arrGroups = $objCleverReach->getFormList($strId);

			foreach ($arrGroups AS $objGroup)
			{
				$arrResult[$objGroup->id] = sprintf(
					'<span style="color:#b3b3b3">[%s]</span> %s',
					$objGroup->name,
					$strName
				);
			}
		}

		return $arrResult;
	}


	/**
	 * Return all clr templates as array
	 *
	 * @return array
	 */
	public function getCleverReachTemplates()
	{
		return $this->getTemplateGroup('clr_');
	}
}