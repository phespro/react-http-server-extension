<?php

namespace Phespro\ReactHttpServerExtension;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class StartServerCommand extends Command
{
    public function __construct(
        protected readonly Config $config,
        protected readonly ReactServer $server,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('react:start-server');
        $this->addOption(
            'host',
            's',
            InputOption::VALUE_REQUIRED,
            'The host under which the services is exposed',
            $this->config->host,
        );
        $this->addOption(
            'workerAmount',
            'w',
            InputOption::VALUE_REQUIRED,
            'The amount of worker processes',
            $this->config->workerAmount,
        );

        $this->addOption(
            'isChild',
            'c',
            InputOption::VALUE_NONE,
            '[Internal] used to start child worker processes',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        $worker = $input->getOption('workerAmount');
        $isChild = !!$input->getOption('isChild');

        $config = new Config($host, $worker);
        $this->server->run($config, new ConsoleLogger($output), $isChild);

        return self::SUCCESS;
    }
}
