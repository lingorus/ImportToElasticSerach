<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 21.02.16
 * Time: 15:55
 */

namespace AppBundle\Producer;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class TaskProducer extends Producer
{
    public function __construct()
    {
        $this->setContentType('application/json');
    }
    
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {

        parent::publish($msgBody);
    }

}


