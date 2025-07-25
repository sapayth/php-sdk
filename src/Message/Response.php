<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Message;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class Response implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $result
     */
    public function __construct(
        public readonly string|int $id,
        public readonly array $result = [],
    ) {
    }

    /**
     * @return array{jsonrpc: string, id: string|int, result: array<string, mixed>}
     */
    public function jsonSerialize(): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $this->id,
            'result' => $this->result,
        ];
    }
}
