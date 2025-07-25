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
final class Request implements \JsonSerializable, \Stringable
{
    /**
     * @param array<string, mixed>|null $params
     */
    public function __construct(
        public readonly int|string $id,
        public readonly string $method,
        public readonly ?array $params = null,
    ) {
    }

    /**
     * @param array{id: string|int, method: string, params?: array<string, mixed>} $data
     */
    public static function from(array $data): self
    {
        return new self(
            $data['id'],
            $data['method'],
            $data['params'] ?? null,
        );
    }

    /**
     * @return array{jsonrpc: string, id: string|int, method: string, params: array<string, mixed>|null}
     */
    public function jsonSerialize(): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $this->id,
            'method' => $this->method,
            'params' => $this->params,
        ];
    }

    public function __toString(): string
    {
        return \sprintf('%s: %s', $this->id, $this->method);
    }
}
