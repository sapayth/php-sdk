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

use Mcp\Message\Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(Error::class)]
final class ErrorTest extends TestCase
{
    public function testWithIntegerId(): void
    {
        $error = new Error(1, -32602, 'Another error occurred');
        $expected = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'error' => [
                'code' => -32602,
                'message' => 'Another error occurred',
            ],
        ];

        $this->assertSame($expected, $error->jsonSerialize());
    }

    public function testWithStringId(): void
    {
        $error = new Error('abc', -32602, 'Another error occurred');
        $expected = [
            'jsonrpc' => '2.0',
            'id' => 'abc',
            'error' => [
                'code' => -32602,
                'message' => 'Another error occurred',
            ],
        ];

        $this->assertSame($expected, $error->jsonSerialize());
    }
}
