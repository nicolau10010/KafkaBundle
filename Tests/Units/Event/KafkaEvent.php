<?php
namespace Widicorp\KafkaBundle\Tests\Units\Event;

use atoum\test;
use Widicorp\KafkaBundle\Event\KafkaEvent as Base;

/**
 * Class EventLog
 * @package Widicorp\KafkaBundle\Tests\Units\Event
 *
 * A class to test the EventLog
 */
class KafkaEvent extends test
{
    /**
     * @return void
     */
    public function testShouldGetACorrectEventAfterConstruction()
    {
        $this
            ->if($event = new Base('consumer'))
            ->then
                ->string($event->getOrigin())
                    ->isEqualTo('consumer')
            ;
    }
}
