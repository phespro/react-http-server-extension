<?php

namespace Phespro\ReactHttpServerExtension;

use Phespro\Phespro\Kernel;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\ChildProcess\Process;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;

class ReactServer implements ReactServerInterface
{
    /** @var array<int, Process> */
    protected array $childProcesses = [];
    protected bool $keepAlive = true;

    public function __construct(protected Kernel $kernel)
    {
    }

    public function run(Config $config, LoggerInterface $logger, bool $isChild): void
    {
        if (!extension_loaded('pcntl')) {
            throw new \Exception('Extension "pcntl" not installed');
        }

        if ($config->workerAmount === 1 || $isChild) {
            $this->listen($logger, $config);
            return;
        }

        for($x = 0; $x < $config->workerAmount; $x++) {
            $this->createChild($x, $logger);
        }

        Loop::addSignal(SIGINT, function() {
            $this->keepAlive = false;
            foreach($this->childProcesses as $childProcess) {
                $childProcess->terminate(SIGINT);
            }
            Loop::stop();
        });

        $logger->info("Started {$config->workerAmount} web server processes on {$config->host}");

        Loop::run();
    }

    protected function createChild(int $id, LoggerInterface $logger): void
    {
        if (!$this->keepAlive) {
            return;
        }

        $cmd = 'exec '; // required for not spawning process in shell context
        $cmd .= implode(
            ' ',
            array_map('escapeshellarg', $_SERVER['argv']),
        );
        $cmd .= ' --isChild'; // indicate, that the child process shall not start further process itself and instead start processing

        $existingChildProcess = $this->childProcesses[$id] ?? null;
        if ($existingChildProcess?->isRunning()) {
            throw new \Exception("Trying to start child process $id, but this process already exists and is running");
        }

        $child = new Process($cmd);
        $this->childProcesses[$id] = $child;
        $child->start();

        $child->stdout->on('data', function($chunk) use ($id, $logger) {
            echo ($chunk);
        });

        $child->stderr->on('data', function($chunk) use ($id, $logger) {
            echo($chunk);
        });

        $child->stdout->on('error', fn(\Throwable $err) => $logger->error(
            "Worker #$id errored",
            [
                'message' => $err->getMessage(),
                'file' => "{$err->getFile()}:{$err->getLine()}",
            ],
        ));

        $child->stderr->on('error', fn(\Throwable $err) => $logger->error(
            "Worker #$id errored",
            [
                'message' => $err->getMessage(),
                'file' => "{$err->getFile()}:{$err->getLine()}",
            ],
        ));
    }

    protected function listen(LoggerInterface $logger, Config $config)
    {
        $httpServer = new HttpServer(function(ServerRequestInterface $request) use ($logger) {
            $logger->info('Incoming Web Request from');
            return $this->kernel->handleWebRequest(false, $request);
        });

        $socketServerConfiguration = array_merge(
            $config->socketServerConfiguration,
            [
                'tcp' => [
                    'so_reuseport' => $config->workerAmount > 1,
                ],
            ],
        );

        $socketServer = new SocketServer($config->host, $socketServerConfiguration);

        $httpServer->listen($socketServer);

        $logger->info("Server started on host http://{$config->host}");

        Loop::addSignal(2, function() use ($logger) {
            $logger->info('Server shutting down by command.');
            Loop::stop();
        });

        Loop::run();
    }
}