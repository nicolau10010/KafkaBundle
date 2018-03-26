<?php

declare(ticks=1);

namespace Widicorp\KafkaBundle\Command;

use Psr\Log\LoggerInterface;
use Widicorp\KafkaBundle\Manager\ConsumerManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeTopicCommand extends ContainerAwareCommand
{
    protected $shutdown;
    protected $logger;

    protected function configure()
    {
        $this
            ->setName('widicorp:kafka:consume')
            ->setDescription('Consume command to process kafka topics')
            ->addArgument('consumer', InputArgument::REQUIRED, 'Consumer name')
            ->addOption('auto-commit', null, InputOption::VALUE_NONE, 'Auto commit enabled?')
            ->addOption('memory-max', null, InputOption::VALUE_REQUIRED, 'Memory max in bytes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $prefixName = $container->getParameter('widicorp_kafka.services_name_prefix');

        $consumer = $input->getArgument('consumer');
        $autoCommit = $input->getOption('auto-commit');
        $memoryMax = $input->getOption('memory-max');

        /**
         * @var ConsumerManager $topicConsumer
         */
        $topicConsumer = $container->get(sprintf('%s.consumer.%s', $prefixName, $consumer));
        if (!$topicConsumer) {
            throw new \Exception(sprintf("TopicConsumer with name '%s' is not defined", $consumer));
        }

        $output->writeln(
            '<comment>Waiting for partition assignment... (make take some time when quickly re-joining the group after 
leaving it.)' . PHP_EOL . '</comment>'
        );

        $this->registerSigHandlers();

        while (true) {
            $message = $topicConsumer->consume($autoCommit);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    try {
                        $topicConsumer->getMessageHandler()->process($message);
                    } catch (\Throwable $e) {
                        if ($this->logger) {
                            $this->logError($message, $e->getMessage());
                        }
                        $output->writeln('<question>Error processing</question>');
                    }
                    if ($autoCommit) {
                        $topicConsumer->commit();
                    }
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $output->writeln('<question>No more messages; will wait for more</question>');
                    $topicConsumer->getMessageHandler()->endOfPartitionReached();
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $output->writeln('<question>Timed out</question>');
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }

            if ($memoryMax !== null && memory_get_peak_usage(true) >= $memoryMax) {
                $output->writeln('<question>Memory limit exceeded!</question>');
                $this->shutdownFn();
            }

            //TODO check shutdown si el tiempo autocomit mayor que el de cola
            if ($this->shutdown) {
                $output->writeln('<question>Shuting down...</question>');
                if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
                    $topicConsumer->commit();
                }

                break;
            }
        }

        $output->writeln('<info>End consuming topic successfully</info>');
    }

    public function shutdownFn()
    {
        $this->shutdown = true;
    }

    private function registerSigHandlers()
    {
        if (!\function_exists('pcntl_signal')) {
            return;
        }

        pcntl_signal(SIGTERM, [$this, 'shutdownFn']);
        pcntl_signal(SIGINT, [$this, 'shutdownFn']);
        pcntl_signal(SIGQUIT, [$this, 'shutdownFn']);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function logError($message, string $exception)
    {
        $this->logger->{$this->getContainer()->getParameter('widicorp_kafka.logger.level')}(
            json_encode([
                'message' => json_encode($message),
                'exception' => json_encode($exception),
            ])
        );
    }
}
