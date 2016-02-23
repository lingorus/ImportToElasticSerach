<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 21.02.16
 * Time: 18:25
 */

namespace AppBundle\ElasticSerach;


class ElasticSearch
{
    protected static $elasticSearch;

    public function __construct()
    {
        if(!self::$elasticSearch){
            self::$elasticSearch = \Elasticsearch\ClientBuilder::create()->build();
        }
    }

    public function getClient()
    {
        return self::$elasticSearch;
    }


    public function index($params)
    {
        return self::$elasticSearch->index($params);
    }

    public function get($params)
    {
        return self::$elasticSearch->index($params);
    }


}