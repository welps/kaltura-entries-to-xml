<?php
namespace wcheng\KalturaEntriesToXML\Kaltura;

require_once __DIR__ . '/../../vendor/autoload.php';

define('CONFIG_FILE', 'config.ini');

use \Kaltura\Client\Client as KalturaClient;
use \Kaltura\Client\Configuration as KalturaConfiguration;
use \Kaltura\Client\Enum\SessionType as KalturaSessionType;
use \Kaltura\Client\Plugin\Metadata\Service\MetadataService as KalturaMetadataService;
use \Kaltura\Client\Plugin\Metadata\Service\MetadataProfileService as KalturaMetadataProfileService;
use \Kaltura\Client\Plugin\Metadata\Type\MetadataFilter as KalturaMetadataFilter;

class KalturaServiceFactory {
	private $kalturaConfig;
	private $isAdmin = true;
	private $kalturaClient;
	
	public function __construct(){
		$this->kalturaConfig = parse_ini_file(dirname(__FILE__) . '/../../' . CONFIG_FILE);
	}	

	public function getKalturaClient(){
		$kalturaConfiguration = $this->getKalturaConfiguration();
		$kalturaConfiguration->setServiceUrl($this->kalturaConfig['serviceUrl']);
        $kalturaConfiguration->setCurlTimeout(120);
        
        $this->kalturaClient = new KalturaClient($kalturaConfiguration);
        $sessionType = ($this->isAdmin) ? KalturaSessionType::ADMIN : KalturaSessionType::USER;

        $ks = $this->kalturaClient->generateSession($this->kalturaConfig['adminSecret'], $this->kalturaConfig['userId'], $sessionType, $this->kalturaConfig['partnerId']);
        $this->kalturaClient->setKs($ks);

        return $this->kalturaClient;
	}

	public function getKalturaConfiguration(){
		return new KalturaConfiguration();
	}

	public function getKalturaMetadataService(){
		return new KalturaMetadataService($this->kalturaClient);
	}

	public function getKalturaMetadataProfileService(){
		return new KalturaMetadataProfileService($this->kalturaClient);
	}

	public function getKalturaMetadataFilter(){
		return new KalturaMetadataFilter();
	}
}