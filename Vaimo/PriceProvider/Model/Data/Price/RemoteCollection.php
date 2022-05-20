<?php

namespace Vaimo\PriceProvider\Model\Data\Price;

use GuzzleHttp\Client;
use Magento\Framework\Json\DecoderInterface;
use Vaimo\PriceProvider\Api\Data\Price\RemoteCollectionInterface;

class RemoteCollection implements RemoteCollectionInterface
{

    private const PRICE_API = 'http://api.test/api/v1/price';

    private $client = null;
    private $jsonDecoder = null;

    public function __construct(Decoderinterface $jsonDecoder)
    {
        $this->client = new Client();
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @param array $productIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPriceDataByIds(array $productIds)
    {
        $res = $this->client->request('GET', self::PRICE_API, [
            'query' => ['skus' => $productIds]
        ]);
        $result = '';
        if ( $res->getStatusCode() == 200 ) {
            $result = $res->getBody();
        }
        return $this->jsonDecoder->decode($result);
    }
}
