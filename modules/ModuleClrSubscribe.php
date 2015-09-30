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
use Contao\Environment;
use Contao\Idna;
use Contao\Validator;

/**
 * Front end module "CleverReach subscribe".
 *
 * @author Nico Ziegler <https://github.com/dreebit>
 */
class ModuleClrSubscribe extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'clr_subscribe';


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

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['clr_subscribe'][0]) . ' ###';
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
		// Overwrite default template
		if ($this->clr_template)
		{
			$this->Template = new \FrontendTemplate($this->clr_template);
			$this->Template->setData($this->arrData);
		}

		// Subscribe
		if (Input::post('FORM_SUBMIT') == 'clr_subscribe')
		{
			$this->addSubscriber();
		}

		$blnHasError = false;

		// Error message
		if (strlen($_SESSION['SUBSCRIBE_ERROR']))
		{
			$blnHasError = true;
			$this->Template->mclass = 'error';
			$this->Template->message = $_SESSION['SUBSCRIBE_ERROR'];
			$_SESSION['SUBSCRIBE_ERROR'] = '';
		}

		// Confirmation message
		if (strlen($_SESSION['SUBSCRIBE_CONFIRM']))
		{
			$this->Template->mclass = 'confirm';
			$this->Template->message = $_SESSION['SUBSCRIBE_CONFIRM'];
			$_SESSION['SUBSCRIBE_CONFIRM'] = '';
		}

		// Default template variables
		$this->Template->email = '';
		$this->Template->submit = specialchars($GLOBALS['TL_LANG']['MSC']['subscribe']);
		$this->Template->emailLabel = $GLOBALS['TL_LANG']['MSC']['emailAddress'];
		$this->Template->action = Environment::get('indexFreeRequest');
		$this->Template->formId = 'clr_subscribe';
		$this->Template->id = $this->id;
		$this->Template->hasError = $blnHasError;
	}


	protected function addSubscriber()
	{
		$varInput = Idna::encodeEmail(Input::post('email', true));

		// Validate the e-mail address
		if (!Validator::isEmail($varInput))
		{
			$_SESSION['SUBSCRIBE_ERROR'] = $GLOBALS['TL_LANG']['ERR']['email'];
			$this->reload();
		}

		$objCleverReach = new CleverReach();

		foreach ($this->clr_groups AS $strGroupId)
		{
			$objCleverReach->addReceiver($varInput, $strGroupId);
		}

		$objCleverReach->sendActivationMail($varInput, $this->clr_form);

		// Redirect to the jumpTo page
		if ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null)
		{
			$this->redirect($this->generateFrontendUrl($objTarget->row()));
		}

		$_SESSION['SUBSCRIBE_CONFIRM'] = $GLOBALS['TL_LANG']['MSC']['nl_confirm'];
		$this->reload();
	}
}
