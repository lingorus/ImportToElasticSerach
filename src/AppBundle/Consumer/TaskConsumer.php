<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 21.02.16
 * Time: 15:55
 */

namespace AppBundle\Consumer;


use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use AppBundle\ElasticSearch\ElasticSearch;


class TaskConsumer implements ConsumerInterface
{
    protected $elasticSearch;


    public function __construct(ElasticSearch $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function execute(AMQPMessage $msg){
        $message = unserialize($msg->getBody());
        $body = $message['data'];
        $hotel = json_decode($body);
        $res = false;
        if($message['id']){
            $params = [
                'index' => 'hotels',
                'type' => 'hotel',
                'id' => $message['id'],
                'body' => $body
            ];
            $response = $this->elasticSearch->index($params);
            $res = (bool)$response['_shards']['successful'];
        }
        return $res;
    }
}


