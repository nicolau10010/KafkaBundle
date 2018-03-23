<?php

namespace Widicorp\KafkaBundle\Helper;

use Widicorp\KafkaBundle\Exceptions\KafkaException;

/**
 * Class PartitionAssignment
 * @package Widicorp\KafkaBundle
 *
 * A class to handle partition assignment
 */
class PartitionAssignment
{
    /**
     * @return Callable
     */
    public static function handlePartitionsAssignment(): Callable
    {
        return function (\RdKafka\KafkaConsumer $consumer, $error, array $partitions = null) {
            switch ($error) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    $consumer->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $consumer->assign(null);
                    break;

                default:
                    throw new KafkaException($error);
            }
        };
    }
}
