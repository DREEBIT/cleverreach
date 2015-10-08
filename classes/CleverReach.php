<?php

/**
 * CleverReach SOAP for Contao Open Source CMS
 *
 * Copyright (C) 2015 dreebit.com
 *
 * @package    dreebit_cleverreach
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace Dreebit;

use Exception;
use SoapClient;
use Contao\Backend;
use Contao\Config;
use Contao\Environment;

class CleverReach extends Backend
{
	/**
	 * @var string
	 */
	protected static $strApiKey;

	/**
	 * @var string
	 */
	protected static $strWsdlUrl;

	/**
	 * @var SoapClient
	 */
	protected $soap;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		try
		{
			$this->apiKey = Config::get('clr_api_key');
			$this->soap = new SoapClient(Config::get('clr_wsdl_url'));
		}
		catch (Exception $e)
		{
			$this->log('Kein API-Key oder WSDL-File für cleverreach.de in den Einstellungen hinterlegt', 'CleverReach::construct', TL_ERROR);
		}

		parent::__construct();
	}


	/**
	 * @param $strEmail
	 * @param int $intGroupId
	 *
	 * @return bool
	 */
	public function addReceiver($strEmail, $intGroupId)
	{
		if ($this->soap)
		{
			$strNewRecipient = array(
				'email' => $strEmail,
				'registered' => time(),
				'source' => 'CMS CONTAO'
			);

			$objReturn = $this->soap->receiverAdd($this->apiKey, $intGroupId, $strNewRecipient);

			if ($objReturn->status == 'SUCCESS')
			{
				$this->log($strEmail . ' hinzugefügt zu cleaverreach.de ' . $objReturn->message, 'CleverReach::addRecipient', TL_NEWSLETTER);
				return true;

			}
			else
			{
				$this->log('Fehler beim Hinzufügen von  ' . $strEmail . ' zu cleaverreach.de - ' . $objReturn->message, 'CleverReach::addRecipient', TL_ERROR);
				return false;
			}
		}
		else
		{
			$this->log('Fehler beim Hinzufügen von  ' . $strEmail . ' zu cleaverreach.de - Keine Verbindung zur SOAP-Schnittstelle', 'CleverReach::addRecipient', TL_ERROR);
			return false;
		}
	}


	/**
	 * @param string $strEmail
	 * @param int $intFormId
	 *
	 * @return bool
	 */
	public function sendActivationMail($strEmail, $intFormId)
	{
		if ($this->soap)
		{
			$objReturn = $this->soap->formsSendActivationMail($this->apiKey, $intFormId, $strEmail, array(
				'user_ip' => '0.0.0.0',
				'user_agent' => Environment::get('httpUserAgent'),
				'referer' => Environment::get('uri'),
			));

			if ($objReturn->status == 'SUCCESS')
			{
				$this->log('E-Mail erfolgreich versendet an ' . $strEmail . ' - ' . $objReturn->message, 'CleverReach::sendActivationMail', TL_NEWSLETTER);
				return true;
			}
			else
			{
				$this->log('Fehler beim versenden an ' . $strEmail . ' - ' . $objReturn->message, 'CleverReach::sendActivationMail', TL_ERROR);
				return false;
			}
		}
		else
		{
			$this->log('Fehler beim versenden an ' . $strEmail . ' - Keine Verbindung zur SOAP-Schnittstelle', 'CleverReach::sendActivationMail', TL_ERROR);
			return false;
		}
	}


	/**
	 * @param string $strEmail
	 * @param int $intGroupId
	 *
	 * @return bool
	 */
	public function receiverSetInactive($strEmail, $intGroupId)
	{
		if ($this->soap)
		{
			$objReturn = $this->soap->receiverSetInactive($this->apiKey, $intGroupId, $strEmail);

			if ($objReturn->status == 'SUCCESS')
			{
				return !$objReturn->data->active ? true : false;
			}
		}
		else
		{
			$this->log('Fehler beim aktualisieren von  ' . $strEmail . ' von cleaverreach.de - Keine Verbindung zur SOAP-Schnittstelle', 'CleverReach::receiverSetInactive', TL_ERROR);
			return false;
		}
	}


	/**
	 * @param string $strEmail
	 * @param int $intGroupId
	 *
	 * @return bool
	 */
	public function receiverDelete($strEmail, $intGroupId)
	{
		if ($this->soap)
		{
			$objReturn = $this->soap->receiverDelete($this->apiKey, $intGroupId, $strEmail);

			if ($objReturn->status == 'SUCCESS')
			{
				$this->log($strEmail . ' aus cleaverreach.de entfernt.', 'CleverReach::receiverDelete', TL_NEWSLETTER);
				return true;

			}
		}
		else
		{
			$this->log('Fehler beim entfernen von  ' . $strEmail . ' von cleaverreach.de - Keine Verbindung zur SOAP-Schnittstelle', 'CleverReach::receiverDelete', TL_ERROR);
			return false;
		}
	}


	/**
	 * @param string $strEmail
	 * @param int $intFormId
	 *
	 * @return bool
	 */
	public function sendUnsubscribeMail($strEmail, $intFormId)
	{
		if ($this->soap)
		{
			$objReturn = $this->soap->formsSendUnsubscribeMail($this->apiKey, $intFormId, $strEmail);

			if ($objReturn->status == 'SUCCESS')
			{
				$this->log('E-Mail erfolgreich versendet an ' . $strEmail . ' - ' . $objReturn->message, 'CleverReach::sendUnsubscribeMail', TL_NEWSLETTER);
				return true;
			}
			else
			{
				$this->log('Fehler beim versenden an ' . $strEmail . ' - ' . $objReturn->message, 'CleverReach::sendUnsubscribeMail', TL_ERROR);
				return false;
			}
		}
		else
		{
			$this->log('Fehler beim versenden an ' . $strEmail . ' - Keine Verbindung zur SOAP-Schnittstelle', 'CleverReach::sendUnsubscribeMail', TL_ERROR);
			return false;
		}
	}


	/**
	 * @return array
	 */
	public function getGroupList()
	{
		$arrGroups = array();

		if ($this->soap)
		{
			$objReturn = $this->soap->groupGetList($this->apiKey);

			if ($objReturn->status == "SUCCESS")
			{
				$arrGroups = $objReturn->data;
			}
		}
		else
		{
			$this->log("Fehler beim Auslesen der Gruppen auf cleaverreach.de - Keine Verbindung zur SOAP-Schnittstelle", "CleverReach::getGroupDetails", TL_ERROR);
		}

		return $arrGroups;
	}


	/**
	 * @param int $intGroupId
	 *
	 * @return array
	 */
	public function getFormList($intGroupId)
	{
		$arrForms = array();

		if ($this->soap)
		{
			$objReturn = $this->soap->formsGetList($this->apiKey, $intGroupId);

			if ($objReturn->status == "SUCCESS")
			{
				$arrForms = $objReturn->data;
			}
		}
		else
		{
			$this->log("Fehler beim Auslesen der Formulare auf cleaverreach.de - Keine Verbindung zur SOAP-Schnittstelle", "CleverReach::getGroupDetails", TL_ERROR);
		}

		return $arrForms;
	}
}