<?php

namespace Widicorp\KafkaBundle\Tests\Units\Factory;

use Widicorp\KafkaBundle\Tests\Units\BaseUnitTest;

/**
 * Class ConsumerFactory
 */
class ConsumerFactory extends BaseUnitTest
{
    public function testGet()
    {
        $consumerClass = 'RdKafka\KafkaConsumer';
        $consumerData  = [
            'configuration' => [
                'group.id' => 'myConsumerGroup'
            ],
            'topicConfiguration' => [],
            'timeout_consuming_queue' => 1,
            'topics' => [
                'test'
            ]
        ];

        $this
            ->given(
                $this->newTestedInstance(new \mock\RdKafka\Conf(), new \mock\RdKafka\TopicConf()),
                $producerManager = $this->testedInstance->get($consumerClass, $consumerData)
            )
            ->then
                ->object($producerManager)
                    ->isInstanceOf('Widicorp\KafkaBundle\Manager\ConsumerManager')
        ;
    }
}
