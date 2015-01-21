<?php
require_once __DIR__ . '/../../vendor/autoload.php';

define('CONFIG_FILE', 'config.ini');

use \Kaltura\Client\Client as KalturaClient;
use \Kaltura\Client\Configuration as KalturaConfiguration;
use \Kaltura\Client\Enum\SessionType as KalturaSessionType;

class KalturaConnector
{
    private $kalturaConfig;
    private $isAdmin = true;

    public function __construct()
    {
        $this->kalturaConfig = parse_ini_file(dirname(__FILE__) . '/../../' . CONFIG_FILE);
    }

    public function startKalturaConnection()
    {
        return $this->getKalturaClient();
    }

    private function getKalturaClient()
    {
        $kConfig = new KalturaConfiguration();
        $kConfig->setServiceUrl($this->kalturaConfig['serviceUrl']);
        $kConfig->setCurlTimeout(120);
        $client = new KalturaClient($kConfig);
        $sessionType = ($this->isAdmin) ? KalturaSessionType::ADMIN : KalturaSessionType::USER;

        $ks = $client->generateSession($this->kalturaConfig['adminSecret'], $this->kalturaConfig['userId'], $sessionType, $this->kalturaConfig['partnerId']);
        $client->setKs($ks);

        return $client;
    }
}
