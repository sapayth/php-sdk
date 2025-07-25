<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Tests;

use Mcp\Server;
use Mcp\Server\JsonRpcHandler;
use Mcp\Tests\Fixtures\InMemoryTransport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\Stub\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

#[Small]
#[CoversClass(Server::class)]
class ServerTest extends TestCase
{
    public function testJsonExceptions(): void
    {
        $logger = $this->getMockBuilder(NullLogger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['error'])
            ->getMock();
        $logger->expects($this->once())->method('error');

        $handler = $this->getMockBuilder(JsonRpcHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['process'])
            ->getMock();
        $handler->expects($this->exactly(2))->method('process')->willReturnOnConsecutiveCalls(new Exception(new \JsonException('foobar')), ['success']);

        $transport = $this->getMockBuilder(InMemoryTransport::class)
            ->setConstructorArgs([['foo', 'bar']])
            ->onlyMethods(['send'])
            ->getMock();
        $transport->expects($this->once())->method('send')->with('success');

        $server = new Server($handler, $logger);
        $server->connect($transport);
    }
}
