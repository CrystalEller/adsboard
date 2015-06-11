<?php

namespace Ads\Service;


use Application\Entity\Ads;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Elastica\Client;
use Elastica\Document;
use Doctrine\ORM\Query\Expr;
use Elastica\Request;

class ElasticSearchService
{
    private $client;
    private $em;

    public function __construct(Client $client, EntityManager $em)
    {
        $this->client = $client;
        $this->em = $em;
    }

    public function saveAds(Ads $data, array $categories, array $props)
    {
        $id = $data->getId();

        $qb = $this->em->createQueryBuilder();
        $propsAttr = array_keys($props);

        $attr = $qb
            ->select('ca.name')
            ->from('Application\Entity\CategoryAttributes', 'ca')
            ->add('where', new Expr\Andx(
                    array(
                        $qb->expr()->in(
                            'ca.id',
                            array_map('intval', $propsAttr)
                        )
                    )
                )
            )
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $values = $qb
            ->select('val.value')
            ->from('Application\Entity\AdsValues', 'av')
            ->innerjoin('av.valueid', 'val', 'WITH', 'av.adsid=?1')
            ->setParameter(1, $id)
            ->distinct()
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $doc = array(
            'id' => $id,
            'title' => $data->getTitle(),
            'description' => $data->getDescription(),
            'category' => $categories,
            'region' => $data->getRegionid()->getId(),
            'city' => $data->getCityid()->getId(),
            'price' => $data->getPrice() ?: null,
            'props' => array(
                'attr' => array_map('current', $attr),
                'values' => array_map('current', $values)
            )
        );

        $document = new Document($id, $doc, 'ads', 'adsboard');

        $this->client->addDocuments([$document]);

        $this->client->getIndex('adsboard')->refresh();
    }

    public function updateAds(Ads $data)
    {
        $id = $data->getId();

        $doc = array(
            'title' => $data->getTitle(),
            'description' => $data->getDescription(),
            'price' => $data->getPrice()
        );

        $this->client->updateDocument($id, $doc, 'adsboard', 'ads');

        $this->client->getIndex('adsboard')->refresh();
    }

    public function deleteAds($adsId)
    {
        $this->client->deleteIds([$adsId], 'adsboard', 'ads');

        $this->client->getIndex('adsboard')->refresh();
    }

    public function searchAds($queryData, $from = 0, $size = 0)
    {
        $adsIds = array();

        if (!empty($queryData)) {
            $data = $this->createSearchQuery($queryData, $from, $size);
            $response = $this->client->request('/adsboard/ads/_search', Request::GET, $data);
            if ($response->isOk()) {
                $resData = $response->getData()['hits']['hits'];
                foreach ($resData as $key => $val) {
                    $adsIds[] = $val['_source']['id'];
                }
            }
        }

        return $adsIds;
    }

    private function createSearchQuery($queryData, $from, $size)
    {
        $must = array();
        $should = array();


        foreach ($queryData as $key => $val) {
            if (!empty($val)) {
                switch ($key) {
                    case 'regionId':
                        $must[] = [
                            "match" => [
                                "region" => [
                                    "query" => $val
                                ]
                            ]
                        ];
                        break;
                    case 'cityId':
                        $must[] = [
                            "match" => [
                                "city" => [
                                    "query" => $val
                                ]
                            ]
                        ];
                        break;
                    case 'categoryId':
                        $must[] = [
                            "match" => [
                                "category" => [
                                    "query" => $val
                                ]
                            ]
                        ];
                        break;
                    case 'query':
                        $should = [
                            "match" => [
                                "_all" => [
                                    "query" => $val
                                ]
                            ]
                        ];
                        break;
                }
            }
        }

        $data = [
            "from" => $from ?: 0,
            "query" => [
                "bool" => [
                    "must" => $must,
                    "should" => $should
                ]
            ]
        ];

        if (!empty($size)) {
            $data['size'] = $size;
        }

        return $data;
    }
}