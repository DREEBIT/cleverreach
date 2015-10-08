<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Dreebit;

use Contao\BackendTemplate;
use Contao\Input;
use Contao\Idna;
use Contao\Validator;

/**
 * Front end module "CleverReach unsubscribe".
 *
 * @author Nico Ziegler <https://github.com/dreebit>
 */
class ModuleClrUnsubscribe extends \Module
{
	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['clr_unsubscribe'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->clr_groups = deserialize($this->clr_groups);

		// Return if there are no groups
		if (!is_array($this->clr_groups) || empty($this->clr_groups))
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		if (Input::get('email'))
		{
			$this->removeSubscriber();
		}
	}


	/**
	 * Remove the recipient
	 */
	protected function removeSubscriber()
	{
		$varInput = Idna::encodeEmail(Input::get('email', true));

		// Validate e-mail address
		if (!Validator::isEmail($varInput))
		{
			$_SESSION['UNSUBSCRIBE_ERROR'] = $GLOBALS['TL_LANG']['ERR']['email'];
			$this->redirect($this->generateFrontendUrl($this->objModel->getRelated('jumpTo')->row()));
		}

		$objCleverReach = new CleverReach();

		switch ($this->clr_unsubscribe)
		{
			case 'inactive':
				foreach ($this->clr_groups AS $strGroupId)
				{
					$objCleverReach->receiverSetInactive($varInput, $strGroupId);
				}
				break;
			case 'delete':
				foreach ($this->clr_groups AS $strGroupId)
				{
					$objCleverReach->receiverDelete($varInput, $strGroupId);
				}
				break;
			case 'email':
			default:
				$objCleverReach->sendUnsubscribeMail($varInput, $this->clr_form);
				break;
		}

		$this->redirect($this->generateFrontendUrl($this->objModel->getRelated('jumpTo')->row()));
	}
}
