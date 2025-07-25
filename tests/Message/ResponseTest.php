<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Tests\Message;

use Mcp\Message\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    public function testWithIntegerId(): void
    {
        $response = new Response(1, ['foo' => 'bar']);
        $expected = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'result' => ['foo' => 'bar'],
        ];

        $this->assertSame($expected, $response->jsonSerialize());
    }

    public function testWithStringId(): void
    {
        $response = new Response('abc', ['foo' => 'bar']);
        $expected = [
            'jsonrpc' => '2.0',
            'id' => 'abc',
            'result' => ['foo' => 'bar'],
        ];

        $this->assertSame($expected, $response->jsonSerialize());
    }
}
