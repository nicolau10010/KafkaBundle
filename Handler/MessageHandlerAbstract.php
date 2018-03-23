<?php

namespace Widicorp\KafkaBundle\Handler;

abstract class MessageHandlerAbstract implements MessageHandlerInterface
{

    public function endOfPartitionReached()
    {
        return null;
    }

}
