<?php

namespace Widicorp\KafkaBundle\Tests\Units\Factory;

use Widicorp\KafkaBundle\Tests\Units\BaseUnitTest;

/**
 * Class ProducerFactory
 */
class ProducerFactory extends BaseUnitTest
{
    public function testGet()
    {
        $producerClass = 'RdKafka\Producer';
        $producerData  = [
            'configuration' => [
                'api.version.request' => true
            ],
            'brokers' => [
                '127.0.0.1'
            ],
            'log_level' => LOG_ALERT,
            'topics' => [
                'test' => [
                    'configuration' => [
                        'auto.commit.interval.ms' => '1000'
                    ],
                    'strategy_partition' => 2
                ]
            ],
            'events_poll_timeout' => -1
        ];

        $this
            ->given(
                $this->newTestedInstance(new \mock\RdKafka\Conf(), new \RdKafka\TopicConf()),
                $producerManager = $this->testedInstance->get($producerClass, $producerData)
            )
            ->then
                ->object($producerManager)
                    ->isInstanceOf('Widicorp\KafkaBundle\Manager\ProducerManager')
        ;
    }
}
