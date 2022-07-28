<?php
/**
 * Parallels Functionality
 *
 * API Documentation at: .. ill fill this in later from forum posts
 *
 * @author Joe Huss <detain@interserver.net>
 * @copyright 2019
 * @package MyAdmin
 * @category Licenses
 */

namespace Detain\Parallels;

require_once __DIR__.'/../../../workerman/statistics/Applications/Statistics/Clients/StatisticClient.php';

use XML_RPC2_Client;

/**
 * Parallels
 *
 * @access public
 */
class Parallels
{
    public $licenseType = 'billing'; // billing or purchase
    private $xmlOptions = ['sslverify' => false];
    private $defaultUrl = 'https://ka.parallels.com:7050/';
    private $defaultDemoUrl = 'https://kademo.parallels.com:7050/';
    public $url = '';
    public $response;
    private $client = '';
    private $login = '';
    private $password = '';
    public $xml;

    /**
     * @param NULL|string $login api login, NULL(default) to use the PARALLELS_KA_LOGIN setting
     * @param NULL|string $password api password, NULL(default) to use the PARALLELS_KA_PASSWORD setting
     * @param NULL|string $client api client, NULL(default) to use the PARALLELS_KA_CLIENT setting
     * @param bool $demo defaults to FALSE, whether or not to use the demo interface instae dof the normal one
     * @param NULL|array $xmlOptions array of optoins ot pass to xmlrpc2 client
     */
    public function __construct($login = null, $password = null, $client = null, $demo = false, $xmlOptions = null)
    {
        if (null === $login && defined('PARALLELS_KA_LOGIN')) {
            $this->login = constant('PARALLELS_KA_LOGIN');
        } else {
            $this->login = $login;
        }
        if (null === $password && defined('PARALLELS_KA_PASSWORD')) {
            $this->password = constant('PARALLELS_KA_PASSWORD');
        } else {
            $this->password = $password;
        }
        if (null !== $client) {
            $this->client = $client;
        } elseif (defined('PARALLELS_KA_CLIENT')) {
            $this->client = constant('PARALLELS_KA_CLIENT');
        }
        if ($demo === true) {
            $this->url = $this->defaultDemoUrl;
        } elseif ($demo === false) {
            $this->url = $this->defaultUrl;
        } else {
            $this->url = $demo;
        }
        if (null !== $xmlOptions) {
            $this->xmlOptions = $xmlOptions;
        }
        require_once 'XML/RPC2/Client.php';
        $this->xml = \XML_RPC2_Client::create($this->url, $this->xmlOptions);
    }

    /**
     * @return array
     */
    public function authInfo()
    {
        return ['login' => $this->login, 'password' => $this->password];
    }

    /**
     * @param array $ips
     * @param array $macs
     * @return array
     */
    public function serverAddress($ips = [], $macs = [])
    {
        if (!is_array($ips) && $ips != '') {
            $ips = [$ips];
        }
        if (!is_array($macs) && $macs != '') {
            $macs = [$macs];
        }
        return [
            'ips' => $ips,
            'macs' => $macs
        ];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function terminateKey($key)
    {
        \StatisticClient::tick('Parallels', 'terminateKey');
        $this->response = $this->xml->__call('partner10.terminateKey', [$this->authInfo(), $key]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'terminateKey', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'terminateKey', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function resetKey($key)
    {
        \StatisticClient::tick('Parallels', 'resetKey');
        $this->response = $this->xml->__call('partner10.resetKey', [$this->authInfo(), $key]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'resetKey', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'resetKey', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function activateKey($key)
    {
        \StatisticClient::tick('Parallels', 'activateKey');
        $this->response = $this->xml->__call('partner10.activateKey', [$this->authInfo(), $key]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'activateKey', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'activateKey', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param $key
     * @param $note
     * @return mixed
     */
    public function addNoteToKey($key, $note)
    {
        \StatisticClient::tick('Parallels', 'addNoteToKey');
        $this->response = $this->xml->__call('partner10.addNoteToKey', [$this->authInfo(), $key, $note]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'addNoteToKey', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'addNoteToKey', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param      $key
     * @param bool $email
     * @return mixed
     */
    public function sendKeyByEmail($key, $email = false)
    {
        \StatisticClient::tick('Parallels', 'sendKeyByEmail');
        if ($email === false) {
            $this->response = $this->xml->__call('partner10.sendKeyByEmail', [$this->authInfo(), $key]);
        } else {
            $this->response = $this->xml->__call('partner10.sendKeyByEmail', [$this->authInfo(), $key, $email]);
        }
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'sendKeyByEmail', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'sendKeyByEmail', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param       $keyType
     * @param array $upgradePlans
     * @param array $ips
     * @param array $macs
     * @param bool  $licenseType
     * @param bool  $client
     * @return mixed
     */
    public function createKey($keyType, $upgradePlans = [], $ips = [], $macs = [], $licenseType = false, $client = false)
    {
        if (!is_array($ips) && $ips != '') {
            $ips = [$ips];
        }
        \StatisticClient::tick('Parallels', 'createKey');
        $this->response = $this->xml->__call(
            'partner10.createKey',
            [
                                                                      $this->authInfo(),
                                                                      $this->serverAddress($ips, $macs), $client === false ? $this->client : $client,
                                                                      $keyType,
                                                                      $upgradePlans, $licenseType === false ? $this->licenseType : $licenseType
        ]
        );
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'createKey', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'createKey', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
        /* Success:
        Array
        (
        [mainKeyNumber] => PLSK.00004266.0000
        [expirationDate] => stdClass Object
        (
        [scalar] => 20131209T00:00:00
        [xmlrpc_type] => datetime
        [timestamp] => 1386547200
        )

        [productKey] => DETAIN-2TVB02-ZT1R57-AY2442-6WN966
        [additionalKeysNumbers] => Array
        (
        )

        [resultCode] => 100
        [resultDesc] => PLSK.00004266.0000 has been successfully created.
        [updateDate] => stdClass Object
        (
        [scalar] => 20131129T00:00:00
        [xmlrpc_type] => datetime
        [timestamp] => 1385683200
        )

        )
        */
    }

    /**
     * @param $key
     * @return mixed
     */
    public function retrieveKey($key)
    {
        \StatisticClient::tick('Parallels', 'retrieveKey');
        $this->response = $this->xml->__call('partner10.retrieveKey', [$this->authInfo(), $key]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'retrieveKey', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'retrieveKey', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
        /* Success
        Array
        (
        [keyExtension] => xml
        [resultCode] => 100
        [resultDesc] => PLSK.00005819.0000 has been successfully retrieved
        [keyNumber] => PLSK.00005819.0000
        [key] => stdClass Object
        (
        [scalar] => <?xml version="1.0" encoding="UTF-8"?><plesk-windows:key xmlns:plesk-windows="http://parallels.com/schemas/keys/products/plesk/windows/multi" core:format="openfusion-3" xmlns:core="http://parallels.com/schemas/keys/core/3">
        <!--Unique product Key number-->
        <core:key-number core:type="string">PLSK.00005819</core:key-number>
        <!--Key version-->
        <core:key-version core:type="string">0000</core:key-version>
        <!--Key description-->
        <core:description>
        <core:keytype>Parallels Plesk Panel 10.x/11.x and Later for Windows</core:keytype>
        <core:feature>Unlimited Domains w/1 yr SUS</core:feature>
        <core:feature>Parallels Web Presence Builder - 100 Sites</core:feature>
        <core:feature>Parallels PowerPack for Plesk (Windows)</core:feature>
        </core:description>
        <!--Product which this license is intended to work on-->
        <core:product core:type="string">plesk-win</core:product>
        <!--Supported product version-->
        <core:versions>
        <core:from core:type="string">10.0</core:from>
        <core:to core:type="string">any</core:to>
        </core:versions>
        <!--Date after which this license becomes usable (inclusive)-->
        <core:start-date core:type="date">instant</core:start-date>
        <!--Date before which this license is usable (exclusive)-->
        <core:expiration-date core:type="date">2013-12-02</core:expiration-date>
        <!--URL of the service endpoint to use when performing an autoupdate-->
        <core:license-server-url core:type="string">https://ka.parallels.com:5224/xmlrpc</core:license-server-url>
        <!--Date when product will try to perform an autoupdate-->
        <core:update-date core:type="date">2013-11-22</core:update-date>
        <core:update-ticket core:hidden="true" core:type="string">k0uj75wmlfa1a5hwmk-k43gy2ji0p2y1</core:update-ticket>
        <!--Number of domains-->
        <plesk-windows:domains core:type="integer">unlimited</plesk-windows:domains>
        <!--Number of clients-->
        <plesk-windows:clients core:type="integer">unlimited</plesk-windows:clients>
        <!--Number of webusers-->
        <plesk-windows:webusers core:type="integer">unlimited</plesk-windows:webusers>
        <!--Number of mailnames-->
        <plesk-windows:mailnames core:type="integer">unlimited</plesk-windows:mailnames>
        <!--Number of additional language pack(s)-->
        <plesk-windows:language-packs core:type="integer">0</plesk-windows:language-packs>
        <plesk-windows:mpc-id core:hidden="true" core:type="integer">0</plesk-windows:mpc-id>
        <plesk-windows:mpc-disabled core:hidden="true" core:type="boolean">false</plesk-windows:mpc-disabled>
        <!--Google tools-->
        <plesk-windows:google-tools core:type="boolean">true</plesk-windows:google-tools>
        <plesk-windows:mpc-mng-disabled core:hidden="true" core:type="boolean">false</plesk-windows:mpc-mng-disabled>
        <!--Number of slaves-->
        <plesk-windows:slaves core:type="integer">0</plesk-windows:slaves>
        <!--EventManager-->
        <plesk-windows:event-manager core:type="boolean">true</plesk-windows:event-manager>
        <!--Domains backup-->
        <plesk-windows:domains-backup core:type="boolean">true</plesk-windows:domains-backup>
        <!--Tomcat support-->
        <plesk-windows:tomcat-support core:type="boolean">true</plesk-windows:tomcat-support>
        <!--Subdomains-->
        <plesk-windows:subdomains-support core:type="boolean">true</plesk-windows:subdomains-support>
        <!--Backward key compatibility restriction-->
        <plesk-windows:backward-restriction core:type="integer">0</plesk-windows:backward-restriction>
        <!--Work Inside Virtuozzo-->
        <plesk-windows:vps-only core:type="boolean">false</plesk-windows:vps-only>
        <!--Work Inside Hyper-V-->
        <plesk-windows:hyper-v core:type="boolean">false</plesk-windows:hyper-v>
        <!--Work Inside VMware-->
        <plesk-windows:vmware core:type="boolean">false</plesk-windows:vmware>
        <!--Work Inside Xen-->
        <plesk-windows:xen core:type="boolean">false</plesk-windows:xen>
        <!--Work Inside KVM-->
        <plesk-windows:kvm core:type="boolean">false</plesk-windows:kvm>
        <!--Work Inside Parallels Hypervisor-->
        <plesk-windows:hypervisor core:type="boolean">false</plesk-windows:hypervisor>
        <!--Global changes-->
        <plesk-windows:global-changes core:type="boolean">true</plesk-windows:global-changes>
        <!--Shell access-->
        <plesk-windows:shell-access core:type="boolean">true</plesk-windows:shell-access>
        <!--Detailed traffic-->
        <plesk-windows:detailed-traffic core:type="boolean">true</plesk-windows:detailed-traffic>
        <!--Notification manager-->
        <plesk-windows:notification-manager core:type="boolean">true</plesk-windows:notification-manager>
        <!--Action log manager-->
        <plesk-windows:action-manager core:type="boolean">true</plesk-windows:action-manager>
        <!--Clients and Domains Expirations management-->
        <plesk-windows:expirations-manager core:type="boolean">true</plesk-windows:expirations-manager>
        <!--Client templates-->
        <plesk-windows:client-templates core:type="boolean">true</plesk-windows:client-templates>
        <!--Ability to use Application Vault-->
        <plesk-windows:appvault-support core:type="boolean">true</plesk-windows:appvault-support>
        <!--Ability to use SpamAssassin-->
        <plesk-windows:spamassasin-support core:type="boolean">true</plesk-windows:spamassasin-support>
        <!--Ability to use Trouble Ticketing System-->
        <plesk-windows:tts-support core:type="boolean">true</plesk-windows:tts-support>
        <!--Ability to use ColdFusion-->
        <plesk-windows:coldfusion-support core:type="boolean">true</plesk-windows:coldfusion-support>
        <plesk-windows:ask-update core:hidden="true" core:type="boolean">false</plesk-windows:ask-update>
        <plesk-windows:autoinstaller-config core:hidden="true" core:type="string">true</plesk-windows:autoinstaller-config>
        <!--Ability to use DrWeb-->
        <plesk-windows:drweb-support core:type="boolean">true</plesk-windows:drweb-support>
        <plesk-windows:store-id core:hidden="true" core:type="integer">1</plesk-windows:store-id>
        <!--Ability to use Migration Manager-->
        <plesk-windows:migration-manager core:type="boolean">true</plesk-windows:migration-manager>
        <!--Ability to use MS SQL-->
        <plesk-windows:mssql core:type="boolean">true</plesk-windows:mssql>
        <!--Allowed locales-->
        <plesk-windows:allowed-locales core:type="string">any</plesk-windows:allowed-locales>
        <!--Allows feature upgrades for this version-->
        <plesk-windows:feature-upgrades core:type="boolean">true</plesk-windows:feature-upgrades>
        <!--Parallels Plesk Billing accounts count-->
        <plesk-windows:modernbill-accounts core:type="integer">0</plesk-windows:modernbill-accounts>
        <!--Number of sites-->
        <plesk-windows:sitebuilder-sites core:type="integer">100</plesk-windows:sitebuilder-sites>
        <!--Enable Parallels Plesk Mobile Server Manager-->
        <plesk-windows:mobile-server-manager-support core:type="boolean">true</plesk-windows:mobile-server-manager-support>
        <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        <ds:SignedInfo>
        <ds:CanonicalizationMethod Algorithm="http://parallels.com/schemas/keys/core/3#canonicalize"/>
        <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>
        <ds:Reference URI="">
        <ds:Transforms>
        <ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments"/>
        <ds:Transform Algorithm="http://parallels.com/schemas/keys/core/3#transform"/>
        </ds:Transforms>
        <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
        <ds:DigestValue>ENCODED HASH HERE</ds:DigestValue>
        </ds:Reference>
        </ds:SignedInfo>
        <ds:SignatureValue>
        ENCODED DATA HERE
        </ds:SignatureValue>
        <ds:KeyInfo>
        <ds:X509Data>
        <ds:X509Certificate>
        ENCODED DATA HERE
        </ds:X509Certificate>
        </ds:X509Data>
        </ds:KeyInfo>
        </ds:Signature>
        </plesk-windows:key>

        [xmlrpc_type] => base64
        )

        )

        */
    }

    /**
     * Returns an array with keys 'resultCode', 'resultDesc', and 'upgradePlans'.  the last one being an array of plan names, one time i wrote down the output it looked like:
     * 3_LANGUAGE_PACKS FOTOLIA_OFF 5_LANGUAGE_PACKS  NEWSFEED_OFF VIRTUOZZO_PROMO_OFF ADDITIONAL_LANGUAGE_PACK were some of the packqage types, there wer eothers
     *
     * @param $key
     * @return mixed
     */
    public function getAvailableUpgrades($key)
    {
        \StatisticClient::tick('Parallels', 'getAvailableUpgrades');
        $this->response = $this->xml->__call('partner10.getAvailableUpgrades', [$this->authInfo(), $key]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'getAvailableUpgrades', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'getAvailableUpgrades', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * Success
     * Array
     * (
     * [keyInfo] => Array
     * (
     * [expirationDate] => stdClass Object
     * (
     * [scalar] => 20131202T00:00:00
     * [xmlrpc_type] => datetime
     * [timestamp] => 1385942400
     * )
     * )
     * [features] => Array
     * (
     * [0] => Array
     * (
     * [apiName] => PLESK_7X_FOR_WIN_POWER_PACK
     * [name] => Parallels PowerPack for Plesk (Windows) (Monthly Lease)
     * )
     * [1] => Array
     * (
     * [apiName] => PLESK-100-SITES
     * [name] => Parallels Web Presence Builder - 100 Sites (Monthly Lease)
     * )
     * [2] => Array
     * (
     * [apiName] => UNLIMITED_DOMAINS
     * [name] => Unlimited Domains w/1 yr SUS (Lease)
     * )
     * )
     * [billingType] => LEASE
     * [productFamily] => plesk
     * [createDate] => stdClass Object
     * (
     * [scalar] => 20131023T18:02:11
     * [xmlrpc_type] => datetime
     * [timestamp] => 1382551331
     * )
     * [trial] =>
     * [lastReportingDate] => stdClass Object
     * (
     * [scalar] => 20131029T06:27:31
     * [xmlrpc_type] => datetime
     * [timestamp] => 1383028051
     * )
     * [additionalKeys] => Array
     * (
     * [0] => Array
     * (
     * [expirationDate] => stdClass Object
     * (
     * [scalar] => 20131202T00:00:00
     * [xmlrpc_type] => datetime
     * [timestamp] => 1385942400
     * )
     * [lastReportingIp] => 206.72.205.242, 206.72.205.243, 206.72.205.244, 206.72.205.245, 206.72.205.246
     * [apiKeyType] => N/A
     * [boundIPAddress] =>
     * [problem] =>
     * [keyNumber] => KAV.00005821.0001
     * [properties] => Array
     * (
     * )
     * [type] => ADDITIONAL
     * [updateDate] => stdClass Object
     * (
     * [scalar] => 20131122T00:00:00
     * [xmlrpc_type] => datetime
     * [timestamp] => 1385078400
     * )
     * [clientId] => 19282468
     * [parentKeyNumber] => PLSK.00005819.0000
     * [lastReportingVersion] => 11.5.3
     * [keyType] => Parallels Plesk Panel Antivirus Powered by Kaspersky, 5 Mailboxes (Parallels PowerPack for Plesk) (Windows) (Monthly Lease)
     * [terminated] =>
     * [susAndSupportInfo] => Array
     * (
     * )
     * [features] => Array
     * (
     * )
     * [billingType] => LEASE
     * [productFamily] => kav
     * [createDate] => stdClass Object
     * (
     * [scalar] => 20131023T18:02:12
     * [xmlrpc_type] => datetime
     * [timestamp] => 1382551332
     * )
     * [trial] =>
     * [lastReportingDate] => stdClass Object
     * (
     * [scalar] => 20131023T18:05:24
     * [xmlrpc_type] => datetime
     * [timestamp] => 1382551524
     * )
     * [additionalKeys] => Array
     * (
     * )
     * )
     * [1] => Array
     * (
     * [expirationDate] => stdClass Object
     * (
     * [scalar] => 20131202T00:00:00
     * [xmlrpc_type] => datetime
     * [timestamp] => 1385942400
     * )
     * [lastReportingIp] => 206.72.205.242, 206.72.205.243, 206.72.205.244, 206.72.205.245, 206.72.205.246
     * [apiKeyType] => N/A
     * [boundIPAddress] =>
     * [problem] =>
     * [keyNumber] => APS.00005820.0001
     * [properties] => Array
     * (
     * )
     * [type] => ADDITIONAL
     * [updateDate] => stdClass Object
     * (
     * [scalar] => 20131122T00:00:00
     * [xmlrpc_type] => datetime
     * [timestamp] => 1385078400
     * )
     * [clientId] => 19282468
     * [parentKeyNumber] => PLSK.00005819.0000
     * [lastReportingVersion] => 11.5.3
     * [keyType] => UNITY One, 2 Domains (Parallels PowerPack for Plesk) (Windows) (Monthly Lease)
     * [terminated] =>
     * [susAndSupportInfo] => Array
     * (
     * )
     * [features] => Array
     * (
     * )
     * [billingType] => LEASE
     * [productFamily] => unity-one
     * [createDate] => stdClass Object
     * (
     * [scalar] => 20131023T18:02:11
     * [xmlrpc_type] => datetime
     * [timestamp] => 1382551331
     * )
     * [trial] =>
     * [lastReportingDate] => stdClass Object
     * (
     * [scalar] => 20131023T18:05:26
     * [xmlrpc_type] => datetime
     * [timestamp] => 1382551526
     * )
     * [additionalKeys] => Array
     * (
     * )
     * )
     * )
     * )
     *[resultCode] => 100
     * [resultDesc] => Key info for PLSK.00005819.0000 key returned successfully
     * [keyNumber] => PLSK.00005819.0000
     * )
     *
     * @param $key
     * @return mixed
     */
    public function getKeyInfo($key)
    {
        \StatisticClient::tick('Parallels', 'getKeyInfo');
        $this->response = $this->xml->__call('partner10.getKeyInfo', [$this->authInfo(), $key]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'getKeyInfo', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'getKeyInfo', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param string $ipAddress the ip address
     * @return false|string false if no key , or a string w/ the key
     */
    public function getMainKeyFromIp($ipAddress)
    {
        $response = $this->getKeyNumbers($ipAddress);
        //$response = $this->getKeysInfoByIP($ipAddress);
        $return = false;
        if (isset($response['keyInfos'])) {
            $responseValues = array_values($response['keyInfos']);
            foreach ($responseValues as $data) {
                if ($return === false) {
                    $return = $data['keyNumber'];
                }
                if ($data['type'] == 'MAIN') {
                    $return = $data['keyNumber'];
                }
            }
            return $return;
        } else {
            return false;
        }
    }

    /**
     * @param $ipAddress
     * @return mixed
     */
    public function getKeysInfoByIP($ipAddress)
    {
        \StatisticClient::tick('Parallels', 'getKeysInfoByIP');
        $this->response = $this->xml->__call('partner10.getKeysInfoByIP', [$this->authInfo(), $ipAddress]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'getKeysInfoByIP', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'getKeysInfoByIP', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
    }

    /**
     * @param array|string $ips
     * @param array        $macs
     * @return mixed
     */
    public function getKeyNumbers($ips = [], $macs = [])
    {
        myadmin_log('licenses', 'info', json_encode($this->serverAddress($ips, $macs)), __LINE__, __FILE__);
        \StatisticClient::tick('Parallels', 'getKeyNumbers');
        $this->response = $this->xml->__call('partner10.getKeyNumbers', [$this->authInfo(), $this->serverAddress($ips, $macs)]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'getKeyNumbers', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'getKeyNumbers', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
        /* Success
        Array
        (
        [keyInfos] => Array
        (
        [0] => Array
        (
        [keyType] => Parallels Plesk Panel 10.x/11.x and Later for Windows for Virtual Machines (Lease)
        Lease)
        [lastReportingIp] => 206.72.205.242
        [terminated] => 1
        [keyNumber] => KAV.00004873.0002
        [billingType] => LEASE
        [type] => ADDITIONAL
        [createDate] => stdClass Object
        (
        [scalar] => 20131014T16:44:40
        [xmlrpc_type] => datetime
        [timestamp] => 1381769080
        )

        [lastReportingDate] => stdClass Object
        (
        [scalar] => 20131023T17:35:45
        [xmlrpc_type] => datetime
        [timestamp] => 1382549745
        )

        )

        [3] => Array
        (
        [keyType] => Parallels Plesk Panel 10.x/11.x and Later for Windows (Lease)
        [lastReportingIp] => 206.72.205.242, 206.72.205.243, 206.72.205.244, 206.72.205.245, 206.72.205.246
        [terminated] =>
        [keyNumber] => PLSK.00005819.0000
        [billingType] => LEASE
        [type] => MAIN
        [createDate] => stdClass Object
        (
        [scalar] => 20131023T18:02:11
        [xmlrpc_type] => datetime
        [timestamp] => 1382551331
        )

        [lastReportingDate] => stdClass Object
        (
        [scalar] => 20131029T06:27:31
        [xmlrpc_type] => datetime
        [timestamp] => 1383028051
        )

        )

        [4] => Array
        (
        [keyType] => UNITY One, 2 Domains (Parallels PowerPack for Plesk) (Windows) (Monthly Lease)
        [lastReportingIp] => 206.72.205.242, 206.72.205.243, 206.72.205.244, 206.72.205.245, 206.72.205.246
        [terminated] =>
        [keyNumber] => APS.00005820.0001
        [billingType] => LEASE
        [type] => ADDITIONAL
        [createDate] => stdClass Object
        (
        [scalar] => 20131023T18:02:11
        [xmlrpc_type] => datetime
        [timestamp] => 1382551331
        )

        [lastReportingDate] => stdClass Object
        (
        [scalar] => 20131023T18:05:26
        [xmlrpc_type] => datetime
        [timestamp] => 1382551526
        )

        )

        [5] => Array
        (
        [keyType] => Parallels Plesk Panel Antivirus Powered by Kaspersky, 5 Mailboxes (Parallels PowerPack for Plesk) (Windows) (Monthly Lease)
        [lastReportingIp] => 206.72.205.242, 206.72.205.243, 206.72.205.244, 206.72.205.245, 206.72.205.246
        [terminated] =>
        [keyNumber] => KAV.00005821.0001
        [billingType] => LEASE
        [type] => ADDITIONAL
        [createDate] => stdClass Object
        (
        [scalar] => 20131023T18:02:12
        [xmlrpc_type] => datetime
        [timestamp] => 1382551332
        )

        [lastReportingDate] => stdClass Object
        (
        [scalar] => 20131023T18:05:24
        [xmlrpc_type] => datetime
        [timestamp] => 1382551524
        )

        )

        )

        [resultCode] => 100
        [resultDesc] => Found: PLSK.02554871.0001, APS.02554872.0002, KAV.02554873.0002, PLSK.00005819.0000, APS.00005820.0001, KAV.00005821.0001
        [keyNumbers] => Array
        (
        [0] => PLSK.02554871.0001
        [1] => APS.02554872.0002
        [2] => KAV.02554873.0002
        [3] => PLSK.00005819.0000
        [4] => APS.00005820.0001
        [5] => KAV.00005821.0001
        )

        [detailResultCode] => 0
        )
        */
    }

    /**
     * @param bool $client
     * @return mixed
     */
    public function getAvailableKeyTypesAndFeatures($client = false)
    {
        \StatisticClient::tick('Parallels', 'getAvailableKeyTypesAndFeatures');
        $this->response = $this->xml->__call('partner10.getAvailableKeyTypesAndFeatures', [$this->authInfo(), $client === false ? $this->client : $client]);
        if ($this->response === false) {
            \StatisticClient::report('Parallels', 'getAvailableKeyTypesAndFeatures', false, 1, 'XML Call Error', STATISTICS_SERVER);
        } else {
            \StatisticClient::report('Parallels', 'getAvailableKeyTypesAndFeatures', true, 0, '', STATISTICS_SERVER);
        }
        return $this->response;
        /* My Output:
        Array
        (
        [resultCode] => 100
        [features] => Array
        (
        [0] => ADDON-CT-OAS-L-1Y
        [1] => ADDON-HMP-L-M
        [2] => SB10X-500
        [3] => ADDON-WPB-12500-M
        [4] => MEDIUM
        [5] => 30_DOMAINS_FOR_VZ
        [6] => PLESK-100-SITES
        [7] => SB10X-35000
        [8] => STORE_BUTTON_OFF
        [9] => 2CPU_90CT_PIM
        [10] => 4CPU_1CT_PIM
        [11] => SB10X-300
        [12] => SB10X-25000
        [13] => 3_LANGUAGE_PACKS_FOR_VMM
        [14] => UNLIMITED_DOMAINS_1000_BILLING_ACCOUNTS_100_SITES_FOR_VZ
        [15] => 8CPU_15HV_PVA
        [16] => ADDITIONAL_LANGUAGE_PACK
        [17] => SB10X-100
        [18] => 4CPU_UNLIMITEDVC_PVA
        [19] => PLESK_SWSOFT_SERVICES_OFF
        [20] => 8CPU_100CT_PVA
        [21] => PLESK_POWER_PACK_FOR_VMM
        [22] => 4CPU_20CT_PIM
        [23] => PLESK-UNLIMITED-PB-ACCOUNTS-FOR-VMM
        [24] => HSPHERE_7500_ACCOUNTS
        [25] => CLDF-PLUS-M
        [26] => 7500_SITES_MULTI_SERVER
        [27] => 4-LANGUAGE-PACKS-FOR-PPA
        [28] => MONTHLY_AMPS
        [29] => 300_SITES
        [30] => UO-UNL-L-1Y
        [31] => 2CPU_20CT_PIM
        [32] => 8CPU_6HV_PVA
        [33] => 4_LANGUAGE_PACKS
        [34] => 8CPU_40CT_PVA
        [35] => 8CPU_1CT_PVA
        [36] => 2CPU_4HV_PVA
        [37] => 8CPU_200CT_PVA
        [38] => 4CPU_60CT_PIM
        [39] => 100_EXT_WHITELABEL
        [40] => 8CPU_50CT_PVA
        [41] => PLESK-UNLIMITED-PB-ACCOUNTS
        [42] => SB10X-40000
        [43] => PLESK_HOSTING_SUITE_FOR_VZ
        [44] => HSPHERE_3750_ACCOUNTS
        [45] => 2CPU_7HV_PVA
        [46] => 4CPU_5CT_PIM
        [47] => UNLIMITED_USERS_FOR_VPS
        [48] => PLESK-100-SITES-FOR-VZ
        [49] => PLESK_RELOADED_FOR_VZ_POWER_PACK
        [50] => 8CPU_10HV_PVA
        [51] => ADDONVZ-CT-OAS-L-1Y
        [52] => PLESK_7X_FOR_WIN_FOR_VZ_POWER_PACK
        [53] => SB10X-2500
        [54] => 2CPU_3CT_PIM
        [55] => ADDITIONAL_LANGUAGE_PACK_FOR_VZ
        [56] => 8CPU_80CT_PVA
        [57] => 4CPU_30VC_PVA
        [58] => 1000_SITES_MULTI_SERVER
        [59] => 1_UNITY_MOBILE_SITE
        [60] => 2CPU_40CT_PIM
        [61] => EXTRAS_BUTTONS_OFF
        [62] => UNLIMITED_DOMAINS_FOR_VMM
        [63] => 8CPU_450CT_PVA
        [64] => HSPHERE_500_ACCOUNTS
        [65] => 4CPU_5VC_PVA
        [66] => HSPHERE_1750_ACCOUNTS
        [67] => FOTOLIA_OFF
        [68] => SB10X-50000
        [69] => HSPHERE_1000_ACCOUNTS
        [70] => 1000_EXT
        [71] => SB10X-7500
        [72] => 100_EXTENSIONS
        [73] => 2CPU_1CT_PIM
        [74] => 1000_EXT_WHITELABEL
        [75] => 8CPU_9HV_PVA
        [76] => UO-1-L-1Y
        [77] => 8CPU_5VC_PVA
        [78] => 8CPU_8HV_PVA
        [79] => 4_LANGUAGE_PACKS_FOR_VZ
        [80] => 4CPU_70CT_PIM
        [81] => 30_DOMAINS
        [82] => 2_LANGUAGE_PACKS_FOR_VMM
        [83] => ADDON-CT-OAS-L-M
        [84] => UO-1-W-M
        [85] => UNLIMITED-LANGUAGE-PACKS-FOR-PPA
        [86] => 100_DOMAINS_FOR_VZ
        [87] => ADDON-WPB-1000-M
        [88] => STH-WMP-BUSP-M
        [89] => UNLIMITED_DOMAINS
        [90] => 2CPU_100CT_PIM
        [91] => ADDONVZ-HMP-W-M
        [92] => DISABLE_SITEBUILDER
        [93] => 8CPU_1CT
        [94] => HSPHERE_2500_ACCOUNTS
        [95] => STH-WMP-BUS-M
        [96] => PLESK_POWER_PACK_FOR_WIN
        [97] => 8CPU_30VC_PVA
        [98] => 8CPU_150CT_PVA
        [99] => HSPHERE_5000_ACCOUNTS
        [100] => 4CPU_90CT_PIM
        [101] => PLESK-UNLIMITED-PB-ACCOUNTS-FOR-VZ
        [102] => ADDON-WPB-45000-M
        [103] => PLESK_POWER_PACK_FOR_VZ
        [104] => 8CPU_250CT_PVA
        [105] => 1-LANGUAGE-PACK-FOR-PPA
        [106] => UO-UNL-W-M
        [107] => 2CPU_5VC_PVA
        [108] => ADDON-WPB-15000-M
        [109] => PLESK_RELOADED_POWER_PACK
        [110] => ADDON-WPB-7500-M
        [111] => PLESK-1000-SITES-FOR-VZ
        [112] => 10_UNITY_MOBILE_SITES
        [148] => 3_LANGUAGE_PACKS_FOR_VZ
        [149] => 2CPU_30CT_PIM
        [150] => ENTERPRISE
        [151] => 2CPU_50CT_PIM
        [152] => ADDON-HMP-W-M
        [153] => 2_LANGUAGE_PACKS_FOR_VZ
        [154] => 4_LANGUAGE_PACKS_FOR_VMM
        [155] => UO-UNL-L-M
        [156] => SB10X-12500
        [157] => 4CPU_50CT_PIM
        [158] => ADDON-WPB-20000-M
        [159] => HSPHERE_10000_ACCOUNTS
        [160] => 4CPU_3CT_PIM
        [161] => 3_LANGUAGE_PACKS
        [162] => 8CPU_90CT_PVA
        [163] => 5000_SITES
        [164] => 100_SITES
        [165] => PLESK_POWER_PACK
        [166] => 8CPU_400CT_PVA
        [167] => 500_EXT
        [168] => 5000_SITES_MULTI_SERVER
        [169] => 5_LANGUAGE_PACKS_FOR_VMM
        [170] => UNLIMITED_BATTLEFIELD_SERVERS
        [171] => ADDON-WPB-35000-M
        [172] => PLESK-100-SITES-FOR-VMM
        [173] => ADDON-WPB-50000-M
        [174] => 8CPU_4HV_PVA
        [175] => 8CPU_5HV_PVA
        [176] => 2CPU_10HV_PVA
        [177] => ADDON-WPB-300-M
        [178] => 2CPU_2HV_PVA
        [179] => 300_SITES_MULTI_SERVER
        [180] => 10_DOMAINS_FOR_VZ
        [181] => 1_LANGUAGE_PACK
        [182] => 4CPU_10CT_PIM
        [183] => ADDON-WPB-700-M
        [184] => 2CPU_30VC_PVA
        [185] => 2CPU_1HV_PVA
        [186] => 5_USERS_FOR_VPS
        [187] => 2CPU_10CT_PIM
        [188] => 500_SITES_MULTI_SERVER
        [189] => 300_DOMAINS
        [190] => 10000_SITES
        [191] => 8CPU_2HV_PVA
        [192] => ADDON-WPB-10000-M
        [193] => PLESK_POWER_PACK_FOR_WIN_FOR_VMM
        [194] => PLESK-1000-SITES-FOR-VMM
        [195] => 5_LANGUAGE_PACKS
        [196] => 1_LANGUAGE_PACK_FOR_VZ
        [197] => 2CPU_200CT_PIM
        [198] => VIRTUOZZO_PROMO_OFF
        [199] => SB10X-5000
        [200] => 8CPU_7HV_PVA
        [201] => 10_DOMAINS
        [202] => PRO
        [203] => 500_SITES
        [204] => 2CPU_70CT_PIM
        [205] => 1_BATTLEFIELD_SERVER
        [206] => 2CPU_150CT_PIM
        [207] => 2CPU_80CT_PIM
        [208] => ADDONVMM-HMP-L-M
        [209] => UNLIMITED_USERS
        [210] => 4CPU_30CT_PIM
        [211] => PLESK_POWER_PACK_FOR_WIN_FOR_VZ
        [212] => 8CPU_300CT_PVA
        [213] => 5_USERS
        [214] => 4CPU_150CT_PIM
        [215] => 10_BATTLEFIELD_SERVERS
        [216] => ADDON-WPB-100-M
        [217] => 8CPU_3HV_PVA
        [218] => ADDON-WPB-2500-M
        [219] => 5_LANGUAGE_PACKS_FOR_VZ
        [220] => 5_BATTLEFIELD_SERVERS
        [221] => DISABLE_GOOGLE_TOOLS
        [222] => 500_EXT_WHITELABEL
        [223] => UNLIMITED_DOMAINS_FOR_VZ
        [224] => SB10X-15000
        [225] => UNLIMITED_MAILBOXES_FOR_VZ
        [226] => 2CPU_5HV_PVA
        [227] => PLESK_7X_FOR_WIN_POWER_PACK
        [228] => PLESK-1000-SITES
        [229] => DISABLE_FEATURE_UPGRADES
        [230] => SB10X-20000
        [231] => ENTRY
        [232] => 2CPU_6HV_PVA
        [233] => 8CPU_30CT_PVA
        [234] => UO-UNL-W-1Y
        [235] => 2_LANGUAGE_PACKS
        [236] => 8CPU_UNLIMITEDVC_PVA
        [237] => 100_DOMAINS
        [238] => 8CPU_20CT_PVA
        [239] => 2CPU_3HV_PVA
        [240] => ADD_1_MANAGED_MSSQL
        [241] => 100_EXT
        [242] => 100_EXTENSIONS_FOR_VZ
        [243] => ADDON-WPB-500-M
        [244] => 8CPU_3CT_PVA
        [245] => 2CPU_9HV_PVA
        [246] => 1_LANGUAGE_PACK_FOR_VMM
        [247] => 8CPU_10CT_PVA
        [248] => 2CPU_15HV_PVA
        [249] => 2CPU_8HV_PVA
        [250] => STARTER
        [251] => ADDONVMM-HMP-W-M
        [252] => 7500_SITES
        [253] => 4CPU_40CT_PIM
        [254] => SB10X-30000
        [255] => PROFESSIONAL
        [256] => 1000_SITES
        [257] => 8CPU_350CT_PVA
        [258] => 2CPU_60CT_PIM
        [259] => HSPHERE_200_ACCOUNTS
        [260] => 8CPU_500CT_PVA
        [261] => SB10X-10000
        [262] => STH-WMP-BSC-M
        [263] => ADDON-WPB-25000-M
        )

        [resultDesc] => Key types available: GLOBAL_MENTORING_LIVE_EXPERT_STANDARD_CARE, PSBM_45_SPE, PLESK-10-AND-LATER-FOR-VMM, PLESK_ANTIVIRUS_BY_KAV_FOR_WIN_FOR_VZ, PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-L-M, MYLITTLEADMIN_2000, MYLITTLEADMIN_2005, PLESK_ANTIVIRUS_BY_DRWEB_FOR_WIN, CRT-5-UNL-L, GLOBAL_MENTORING_TOTAL_CARE, LINUXMAGIC_MAGICSPAM, PLESK_ANTIVIRUS_BY_KAV_FOR_VZ, PINNACLE_CART_ECOMMERCE_SHOPPING_CART, SYMANTEC_NORTON_INTERNET_SECURITY_10SEATS_MONTHLY, ATI_PRO_FOR_WIN, STOPTHEHACKER-M, PARALLELS_PREMIUM_ANTIVIRUS_FOR_WIN_FOR_VZ, SB10X-PA, ATI_PRO, CRT-30-UNL-L, 4PSA_VOIPNOW_25_PROFESSIONAL, CLOUDLINUX-L-M, ATMAIL_WEBMAIL, PLESK_10_AND_LATER_FOR_WIN, CRT-5-100-L, PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-FOR-VZ-L-1Y, UNITY-ONE-W-M, PLESK-10-AND-LATER-FOR-WIN-FOR-VMM, GLOBAL_MENTORING_LIVE_EXPERT_BASIC, CRT-50-UNL-L, VIRTUOZZO_CONTAINERS_4, PARALLELS_PREMIUM_ANTIVIRUS_FOR_VZ, PLESK_10_AND_LATER_FOR_VZ, PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-FOR-VZ-L-M, CRT-50-100-L, UNITY-ONE-L-M, PLESK_ANTIVIRUS_BY_DRWEB, SYMANTEC_NORTON_INTERNET_SECURITY_MONTHLY, CRT-100-UNL-L, PARALLELS-CLOUD-SERVER, PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-L-1Y, CRT-100-100-L, PLESK_10_AND_LATER_FOR_WIN_FOR_VZ, PPA-L-M, PARALLELS_PREMIUM_ANTIVIRUS_FOR_WIN, PARALLELS-PREMIUM-ANTIVIRUS-FOR-VMM, PLESK_ANTIVIRUS_BY_KAV_FOR_WIN, PLESK_ANTIVIRUS_BY_DRWEB_FOR_VZ, SYMANTEC_NORTON_INTERNET_SECURITY_5SEATS_MONTHLY, UNITY_MOBILE, PLESK_ANTIVIRUS_BY_DRWEB_FOR_WIN_FOR_VZ, SB10X, UNITY_MOBILE_FOR_WIN, PARALLELS-CLOUD-STORAGE, CRT-30-100-L, CLOUDFLARE-M, VIRTUOZZO_CONTAINERS_4_FOR_WIN, PLESK_10_AND_LATER, PARALLELS_PREMIUM_ANTIVIRUS, SYMANTEC_NORTON_INTERNET_SECURITY_3SEATS_MONTHLY, PLESK_ANTIVIRUS_BY_KAV, KEEPIT_ONLINE_BACKUP. Features available: ADDON-CT-OAS-L-1Y, ADDON-HMP-L-M, SB10X-500, ADDON-WPB-12500-M, MEDIUM, 30_DOMAINS_FOR_VZ, PLESK-100-SITES, SB10X-35000, STORE_BUTTON_OFF, 2CPU_90CT_PIM, 4CPU_1CT_PIM, SB10X-300, SB10X-25000, 3_LANGUAGE_PACKS_FOR_VMM, UNLIMITED_DOMAINS_1000_BILLING_ACCOUNTS_100_SITES_FOR_VZ, 8CPU_15HV_PVA, ADDITIONAL_LANGUAGE_PACK, SB10X-100, 4CPU_UNLIMITEDVC_PVA, PLESK_SWSOFT_SERVICES_OFF, 8CPU_100CT_PVA, PLESK_POWER_PACK_FOR_VMM, 4CPU_20CT_PIM, PLESK-UNLIMITED-PB-ACCOUNTS-FOR-VMM, HSPHERE_7500_ACCOUNTS, CLDF-PLUS-M, 7500_SITES_MULTI_SERVER, 4-LANGUAGE-PACKS-FOR-PPA, MONTHLY_AMPS, 300_SITES, UO-UNL-L-1Y, 2CPU_20CT_PIM, 8CPU_6HV_PVA, 4_LANGUAGE_PACKS, 8CPU_40CT_PVA, 8CPU_1CT_PVA, 2CPU_4HV_PVA, 8CPU_200CT_PVA, 4CPU_60CT_PIM, 100_EXT_WHITELABEL, 8CPU_50CT_PVA, PLESK-UNLIMITED-PB-ACCOUNTS, SB10X-40000, PLESK_HOSTING_SUITE_FOR_VZ, HSPHERE_3750_ACCOUNTS, 2CPU_7HV_PVA, 4CPU_5CT_PIM, UNLIMITED_USERS_FOR_VPS, PLESK-100-SITES-FOR-VZ, PLESK_RELOADED_FOR_VZ_POWER_PACK, 8CPU_10HV_PVA, ADDONVZ-CT-OAS-L-1Y, PLESK_7X_FOR_WIN_FOR_VZ_POWER_PACK, SB10X-2500, 2CPU_3CT_PIM, ADDITIONAL_LANGUAGE_PACK_FOR_VZ, 8CPU_80CT_PVA, 4CPU_30VC_PVA, 1000_SITES_MULTI_SERVER, 1_UNITY_MOBILE_SITE, 2CPU_40CT_PIM, EXTRAS_BUTTONS_OFF, UNLIMITED_DOMAINS_FOR_VMM, 8CPU_450CT_PVA, HSPHERE_500_ACCOUNTS, 4CPU_5VC_PVA, HSPHERE_1750_ACCOUNTS, FOTOLIA_OFF, SB10X-50000, HSPHERE_1000_ACCOUNTS, 1000_EXT, SB10X-7500, 100_EXTENSIONS, 2CPU_1CT_PIM, 1000_EXT_WHITELABEL, 8CPU_9HV_PVA, UO-1-L-1Y, 8CPU_5VC_PVA, 8CPU_8HV_PVA, 4_LANGUAGE_PACKS_FOR_VZ, 4CPU_70CT_PIM, 30_DOMAINS, 2_LANGUAGE_PACKS_FOR_VMM, ADDON-CT-OAS-L-M, UO-1-W-M, UNLIMITED-LANGUAGE-PACKS-FOR-PPA, 100_DOMAINS_FOR_VZ, ADDON-WPB-1000-M, STH-WMP-BUSP-M, UNLIMITED_DOMAINS, 2CPU_100CT_PIM, ADDONVZ-HMP-W-M, DISABLE_SITEBUILDER, 8CPU_1CT, HSPHERE_2500_ACCOUNTS, STH-WMP-BUS-M, PLESK_POWER_PACK_FOR_WIN, 8CPU_30VC_PVA, 8CPU_150CT_PVA, HSPHERE_5000_ACCOUNTS, 4CPU_90CT_PIM, PLESK-UNLIMITED-PB-ACCOUNTS-FOR-VZ, ADDON-WPB-45000-M, PLESK_POWER_PACK_FOR_VZ, 8CPU_250CT_PVA, 1-LANGUAGE-PACK-FOR-PPA, UO-UNL-W-M, 2CPU_5VC_PVA, ADDON-WPB-15000-M, PLESK_RELOADED_POWER_PACK, ADDON-WPB-7500-M, PLESK-1000-SITES-FOR-VZ, 10_UNITY_MOBILE_SITES, 8CPU_70CT_PVA, UNLIMITED_MAILBOXES, STH-WMP-PRO-M, SB10X-45000, 8CPU_1HV_PVA, NEWSFEED_OFF, 4CPU_80CT_PIM, UO-1-W-1Y, 10000_SITES_MULTI_SERVER, 8CPU_UNLIMITEDHV_PVA, 2_BATTLEFIELD_SERVERS, CLNX-M, 8CPU_60CT_PVA, 100_SITES_MULTI_SERVER, 2CPU_UNLIMITEDVC_PVA, 2CPU_UNLIMITED_HV_PVA, 10_EXT_WHITELABEL, REINSTATE_SUS, ADDONVZ-HMP-L-M, UO-1-L-M, 8CPU_5CT_PVA, 4CPU_100CT_PIM, SB10X-1000, ADDON-WPB-5000-M, 2CPU_5CT_PIM, 10_EXT, 5_UNITY_MOBILE_SITES, 4CPU_200CT_PIM, ADDON-WPB-40000-M, ADDON-WPB-30000-M, 5-LANGUAGE-PACKS-FOR-PPA, 3-LANGUAGE-PACKS-FOR-PPA, PCS-RKU, ADDONVZ-CT-OAS-L-M, 2-LANGUAGE-PACKS-FOR-PPA, 3_LANGUAGE_PACKS_FOR_VZ, 2CPU_30CT_PIM, ENTERPRISE, 2CPU_50CT_PIM, ADDON-HMP-W-M, 2_LANGUAGE_PACKS_FOR_VZ, 4_LANGUAGE_PACKS_FOR_VMM, UO-UNL-L-M, SB10X-12500, 4CPU_50CT_PIM, ADDON-WPB-20000-M, HSPHERE_10000_ACCOUNTS, 4CPU_3CT_PIM, 3_LANGUAGE_PACKS, 8CPU_90CT_PVA, 5000_SITES, 100_SITES, PLESK_POWER_PACK, 8CPU_400CT_PVA, 500_EXT, 5000_SITES_MULTI_SERVER, 5_LANGUAGE_PACKS_FOR_VMM, UNLIMITED_BATTLEFIELD_SERVERS, ADDON-WPB-35000-M, PLESK-100-SITES-FOR-VMM, ADDON-WPB-50000-M, 8CPU_4HV_PVA, 8CPU_5HV_PVA, 2CPU_10HV_PVA, ADDON-WPB-300-M, 2CPU_2HV_PVA, 300_SITES_MULTI_SERVER, 10_DOMAINS_FOR_VZ, 1_LANGUAGE_PACK, 4CPU_10CT_PIM, ADDON-WPB-700-M, 2CPU_30VC_PVA, 2CPU_1HV_PVA, 5_USERS_FOR_VPS, 2CPU_10CT_PIM, 500_SITES_MULTI_SERVER, 300_DOMAINS, 10000_SITES, 8CPU_2HV_PVA, ADDON-WPB-10000-M, PLESK_POWER_PACK_FOR_WIN_FOR_VMM, PLESK-1000-SITES-FOR-VMM, 5_LANGUAGE_PACKS, 1_LANGUAGE_PACK_FOR_VZ, 2CPU_200CT_PIM, VIRTUOZZO_PROMO_OFF, SB10X-5000, 8CPU_7HV_PVA, 10_DOMAINS, PRO, 500_SITES, 2CPU_70CT_PIM, 1_BATTLEFIELD_SERVER, 2CPU_150CT_PIM, 2CPU_80CT_PIM, ADDONVMM-HMP-L-M, UNLIMITED_USERS, 4CPU_30CT_PIM, PLESK_POWER_PACK_FOR_WIN_FOR_VZ, 8CPU_300CT_PVA, 5_USERS, 4CPU_150CT_PIM, 10_BATTLEFIELD_SERVERS, ADDON-WPB-100-M, 8CPU_3HV_PVA, ADDON-WPB-2500-M, 5_LANGUAGE_PACKS_FOR_VZ, 5_BATTLEFIELD_SERVERS, DISABLE_GOOGLE_TOOLS, 500_EXT_WHITELABEL, UNLIMITED_DOMAINS_FOR_VZ, SB10X-15000, UNLIMITED_MAILBOXES_FOR_VZ, 2CPU_5HV_PVA, PLESK_7X_FOR_WIN_POWER_PACK, PLESK-1000-SITES, DISABLE_FEATURE_UPGRADES, SB10X-20000, ENTRY, 2CPU_6HV_PVA, 8CPU_30CT_PVA, UO-UNL-W-1Y, 2_LANGUAGE_PACKS, 8CPU_UNLIMITEDVC_PVA, 100_DOMAINS, 8CPU_20CT_PVA, 2CPU_3HV_PVA, ADD_1_MANAGED_MSSQL, 100_EXT, 100_EXTENSIONS_FOR_VZ, ADDON-WPB-500-M, 8CPU_3CT_PVA, 2CPU_9HV_PVA, 1_LANGUAGE_PACK_FOR_VMM, 8CPU_10CT_PVA, 2CPU_15HV_PVA, 2CPU_8HV_PVA, STARTER, ADDONVMM-HMP-W-M, 7500_SITES, 4CPU_40CT_PIM, SB10X-30000, PROFESSIONAL, 1000_SITES, 8CPU_350CT_PVA, 2CPU_60CT_PIM, HSPHERE_200_ACCOUNTS, 8CPU_500CT_PVA, SB10X-10000, STH-WMP-BSC-M, ADDON-WPB-25000-M.
        [keyTypes] => Array
        (
        [0] => GLOBAL_MENTORING_LIVE_EXPERT_STANDARD_CARE
        [1] => PSBM_45_SPE
        [2] => PLESK-10-AND-LATER-FOR-VMM
        [3] => PLESK_ANTIVIRUS_BY_KAV_FOR_WIN_FOR_VZ
        [4] => PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-L-M
        [5] => MYLITTLEADMIN_2000
        [6] => MYLITTLEADMIN_2005
        [7] => PLESK_ANTIVIRUS_BY_DRWEB_FOR_WIN
        [8] => CRT-5-UNL-L
        [9] => GLOBAL_MENTORING_TOTAL_CARE
        [10] => LINUXMAGIC_MAGICSPAM
        [11] => PLESK_ANTIVIRUS_BY_KAV_FOR_VZ
        [12] => PINNACLE_CART_ECOMMERCE_SHOPPING_CART
        [13] => SYMANTEC_NORTON_INTERNET_SECURITY_10SEATS_MONTHLY
        [14] => ATI_PRO_FOR_WIN
        [15] => STOPTHEHACKER-M
        [16] => PARALLELS_PREMIUM_ANTIVIRUS_FOR_WIN_FOR_VZ
        [17] => SB10X-PA
        [18] => ATI_PRO
        [19] => CRT-30-UNL-L
        [20] => 4PSA_VOIPNOW_25_PROFESSIONAL
        [21] => CLOUDLINUX-L-M
        [22] => ATMAIL_WEBMAIL
        [23] => PLESK_10_AND_LATER_FOR_WIN
        [24] => CRT-5-100-L
        [25] => PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-FOR-VZ-L-1Y
        [26] => UNITY-ONE-W-M
        [27] => PLESK-10-AND-LATER-FOR-WIN-FOR-VMM
        [28] => GLOBAL_MENTORING_LIVE_EXPERT_BASIC
        [29] => CRT-50-UNL-L
        [30] => VIRTUOZZO_CONTAINERS_4
        [31] => PARALLELS_PREMIUM_ANTIVIRUS_FOR_VZ
        [32] => PLESK_10_AND_LATER_FOR_VZ
        [33] => PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-FOR-VZ-L-M
        [34] => CRT-50-100-L
        [35] => UNITY-ONE-L-M
        [36] => PLESK_ANTIVIRUS_BY_DRWEB
        [37] => SYMANTEC_NORTON_INTERNET_SECURITY_MONTHLY
        [38] => CRT-100-UNL-L
        [39] => PARALLELS-CLOUD-SERVER
        [40] => PARALLELS-PREMIUM-OUTBOUND-ANTISPAM-L-1Y
        [41] => CRT-100-100-L
        [42] => PLESK_10_AND_LATER_FOR_WIN_FOR_VZ
        [43] => PPA-L-M
        [44] => PARALLELS_PREMIUM_ANTIVIRUS_FOR_WIN
        [45] => PARALLELS-PREMIUM-ANTIVIRUS-FOR-VMM
        [46] => PLESK_ANTIVIRUS_BY_KAV_FOR_WIN
        [47] => PLESK_ANTIVIRUS_BY_DRWEB_FOR_VZ
        [48] => SYMANTEC_NORTON_INTERNET_SECURITY_5SEATS_MONTHLY
        [49] => UNITY_MOBILE
        [50] => PLESK_ANTIVIRUS_BY_DRWEB_FOR_WIN_FOR_VZ
        [51] => SB10X
        [52] => UNITY_MOBILE_FOR_WIN
        [53] => PARALLELS-CLOUD-STORAGE
        [54] => CRT-30-100-L
        [55] => CLOUDFLARE-M
        [56] => VIRTUOZZO_CONTAINERS_4_FOR_WIN
        [57] => PLESK_10_AND_LATER
        [58] => PARALLELS_PREMIUM_ANTIVIRUS
        [59] => SYMANTEC_NORTON_INTERNET_SECURITY_3SEATS_MONTHLY
        [60] => PLESK_ANTIVIRUS_BY_KAV
        [61] => KEEPIT_ONLINE_BACKUP
        )

        )
        */
    }
}
