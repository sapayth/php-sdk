<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp;

use Mcp\Server\JsonRpcHandler;
use Mcp\Server\TransportInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class Server
{
    public function __construct(
        private readonly JsonRpcHandler $jsonRpcHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function connect(TransportInterface $transport): void
    {
        $transport->initialize();
        $this->logger->info('Transport initialized');

        while ($transport->isConnected()) {
            foreach ($transport->receive() as $message) {
                if (null === $message) {
                    continue;
                }

                try {
                    foreach ($this->jsonRpcHandler->process($message) as $response) {
                        if (null === $response) {
                            continue;
                        }

                        $transport->send($response);
                    }
                } catch (\JsonException $e) {
                    $this->logger->error('Failed to encode response to JSON', [
                        'message' => $message,
                        'exception' => $e,
                    ]);
                    continue;
                }
            }

            usleep(1000);
        }

        $transport->close();
        $this->logger->info('Transport closed');
    }
}
