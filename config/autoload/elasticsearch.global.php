<?php

return array(
    'elastica' => array(
        'servers' => array(
            array('host' => '127.0.0.1', 'port' => 9200),
        ),
        'index' => array(
            'name' => 'adsboard',
            'settings' => [
                "analysis" => [
                    "filter" => [
                        "trigrams_filter" => [
                            "type" => "ngram",
                            "min_gram" => 3,
                            "max_gram" => 6
                        ],
                        "users_trigrams_filter" => [
                            "type" => "ngram",
                            "min_gram" => 1,
                            "max_gram" => 6

                        ]
                    ],
                    "analyzer" => [
                        "trigrams" => [
                            "type" => "custom",
                            "tokenizer" => "standard",
                            "filter" => [
                                "lowercase",
                                "trigrams_filter"
                            ]
                        ],
                        "users_trigram" => [
                            "type" => "custom",
                            "tokenizer" => "standard",
                            "filter" => [
                                "lowercase",
                                "users_trigrams_filter"
                            ]
                        ]
                    ]
                ]
            ]
        ),
        'mappings' => [
            "ads" => [
                "_all" => [
                    "index_analyzer" => "trigrams"
                ],
                "properties" => [
                    "id" => [
                        "type" => "long"
                    ],
                    "category" => [
                        "type" => "long"
                    ],
                    "region" => [
                        "type" => "long"
                    ],
                    "city" => [
                        "type" => "long"
                    ],
                    "title" => [
                        "type" => "string",
                        "analyzer" => "trigrams"
                    ],
                    "description" => [
                        "type" => "string",
                        "analyzer" => "trigrams"
                    ],
                    "price" => [
                        "type" => "integer"
                    ],
                    "props" => [
                        "type" => "object",
                        "properties" => [
                            "attr" => [
                                "type" => "string",
                                "analyzer" => "trigrams"
                            ],
                            "values" => [
                                "type" => "string",
                                "analyzer" => "trigrams"
                            ]
                        ]
                    ]
                ]
            ],
            "users" => [
                "_all" => [
                    "index_analyzer" => "users_trigram"
                ],
                "properties" => [
                    "id" => [
                        "type" => "long",
                        "analyzer" => "users_trigram"
                    ],
                    "email" => [
                        "type" => "string",
                        "analyzer" => "users_trigram"
                    ],
                    "role" => [
                        "type" => "string",
                        "analyzer" => "users_trigram"
                    ],
                    "stat" => [
                        "type" => "string",
                        "analyzer" => "users_trigram"
                    ]
                ]
            ]
        ]
    )
);