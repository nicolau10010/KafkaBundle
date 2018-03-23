<?php

namespace Widicorp\KafkaBundle\Handler;

use RdKafka\Message;

interface MessageHandlerInterface
{
    /**
     * Process message from kafka
     *
     * @param Message $message
     * @return mixed
     */
    public function process(Message $message);

    /**
     * @return mixed
     */
    public function endOfPartitionReached();

}