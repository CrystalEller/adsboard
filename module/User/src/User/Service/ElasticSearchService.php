<?php

namespace User\Service;


use Application\Entity\Users;
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

    public function saveUser(Users $user)
    {
        $id = $user->getId();

        $doc = array(
            'id' => $id,
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'stat' => $user->getStat(),
        );

        $document = new Document($id, $doc, 'users', 'adsboard');

        $this->client->addDocuments([$document]);

        $this->client->getIndex('adsboard')->refresh();
    }

    public function updateUser(Users $user)
    {
        $id = $user->getId();
        $doc = array();

        if (!empty($user->getRole())) {
            $doc['role'] = $user->getRole();
        }

        if (!empty($user->getStat())) {
            $doc['stat'] = $user->getStat();
        }

        $this->client->updateDocument($id, $doc, 'adsboard', 'users');

        $this->client->getIndex('adsboard')->refresh();
    }

    public function deleteUser($userId)
    {
        $this->client->deleteIds([$userId], 'adsboard', 'users');

        $this->client->getIndex('adsboard')->refresh();
    }

    public function searchUser($queryData, $from = 0, $size = 0)
    {
        $adsIds = array();

        if (!empty($queryData)) {
            $data = $this->createSearchQuery($queryData, $from, $size);
            $response = $this->client->request('/adsboard/users/_search', Request::GET, $data);
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
        $should = array();


        foreach ($queryData as $key => $val) {
            if (!empty($val)) {
                switch ($key) {
                    case 'search':
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