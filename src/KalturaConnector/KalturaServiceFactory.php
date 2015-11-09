<?php
namespace wcheng\KalturaEntriesToXML\Kaltura;

require_once __DIR__ . '/../../vendor/autoload.php';

define('CONFIG_FILE', 'config.ini');

use \Kaltura\Client\Client as KalturaClient;
use \Kaltura\Client\Configuration as KalturaConfiguration;
use \Kaltura\Client\Enum\SessionType as KalturaSessionType;

class KalturaServiceFactory {
	private $kalturaConfig;
	private $isAdmin = true;
	
	public function __construct(){
		$this->kalturaConfig = parse_ini_file(dirname(__FILE__) . '/../../' . CONFIG_FILE);
	}	

	public function getKalturaClient(){
		$kalturaConfiguration = $this->getKalturaConfiguration();
		$kalturaConfiguration->setServiceUrl($this->kalturaConfig['serviceUrl']);
        $kalturaConfiguration->setCurlTimeout(120);
        
        $kalturaClient = new KalturaClient($kalturaConfiguration);
        $sessionType = ($this->isAdmin) ? KalturaSessionType::ADMIN : KalturaSessionType::USER;

        $ks = $kalturaClient->generateSession($this->kalturaConfig['adminSecret'], $this->kalturaConfig['userId'], $sessionType, $this->kalturaConfig['partnerId']);
        $kalturaClient->setKs($ks);

        return $kalturaClient;
	}

	public function getKalturaConfiguration(){
		return new KalturaConfiguration();
	}
}