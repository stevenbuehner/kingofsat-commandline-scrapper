<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 02.02.16
 * Time: 00:05
 */


namespace StevenBuehner\Service;

use Goutte\Client;
use Guzzle\Common\Exception\InvalidArgumentException;

class KingOfSatScrapper {

    protected $baseUrl = 'http://de.kingofsat.net';
    protected $client;

    public function __construct() {
        $this->client = new Client();
//         $this->client->getClient()->setDefaultOption('config/curl/' . CURLOPT_TIMEOUT, 60);
    }

    public function getTransponderData($subUrl) {

        $url = $this->baseUrl . '/' . $subUrl;
        $crawler = $this->client->request('GET', $url);

        $tables = $crawler->filter('table.frq tr');

        $data = $tables->each(function ($node, $i) {
            /* @var $node \Symfony\Component\DomCrawler\Crawler */
            $tds = $node->children();

            $transponder = [ ];

            $transponder['Satelitenposition'] = $tds->eq(0)->text();
            $transponder['Satelit'] = $tds->eq(1)->text();
            $transponder['Frequenz'] = $tds->eq(2)->text();
            $transponder['Polarisation'] = $tds->eq(3)->text();
            $transponder['Transponder'] = $tds->eq(4)->text();
            $transponder['Beam'] = $tds->eq(5)->text();
            $transponder['Sendenorm'] = $tds->eq(6)->text();
            $transponder['Modulation'] = $tds->eq(7)->text();
            $transponder['Symbolate'] = $tds->eq(8)->text();

            $provider = $tds->eq(9)->filter('b');
            $transponder['Provider'] = (count($provider) > 0) ? $provider->text() : '';

            $bitrate = trim($tds->eq(9)->filterXPath('td/text()')->text());
            if (strlen($bitrate) > 0) {
                if (preg_match('~(?<mpbs>[0-9]+\.[0-9]+ Mbps)~', $bitrate, $match) === 1) {
                    $transponder['bitrate'] = $match['mpbs'];
                } else {
                    $transponder['bitrate'] = '';
                }
            }

            $transponder['Network ID'] = $tds->eq(10)->text();
            $transponder['Transponder ID'] = $tds->eq(11)->text();

            return $transponder;
        });

        return $data;
    }

}