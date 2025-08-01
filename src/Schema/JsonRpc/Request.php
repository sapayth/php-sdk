<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Schema\JsonRpc;

use Mcp\Exception\InvalidArgumentException;
use Mcp\Schema\Constants;

/**
 * @phpstan-type RequestData array{
 *     jsonrpc: string,
 *     id: string|int,
 *     method: string,
 *     params?: array<string, mixed>,
 * }
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class Request implements MessageInterface
{
    /**
     * @param string|int                $id     a unique identifier for the request
     * @param string                    $method the name of the method to be invoked
     * @param array<string, mixed>|null $params parameters for the method
     */
    public function __construct(
        public readonly string|int $id,
        public readonly string $method,
        public readonly ?array $params = null,
    ) {
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    /**
     * @param RequestData $data
     */
    public static function fromArray(array $data): self
    {
        if (($data['jsonrpc'] ?? null) !== Constants::JSONRPC_VERSION) {
            throw new InvalidArgumentException('Invalid or missing "jsonrpc" version for Request.');
        }
        if (!isset($data['id']) || !\is_string($data['id']) && !\is_int($data['id'])) {
            throw new InvalidArgumentException('Invalid or missing "id" for Request.');
        }
        if (!isset($data['method']) || !\is_string($data['method'])) {
            throw new InvalidArgumentException('Invalid or missing "method" for Request.');
        }
        $params = $data['params'] ?? null;
        if ($params instanceof \stdClass) {
            $params = (array) $params;
        }
        if (null !== $params && !\is_array($params)) {
            throw new InvalidArgumentException('"params" for Request must be an array/object or null.');
        }

        return new self($data['id'], $data['method'], $params);
    }

    /**
     * @return RequestData
     */
    public function jsonSerialize(): array
    {
        $array = [
            'jsonrpc' => Constants::JSONRPC_VERSION,
            'id' => $this->id,
            'method' => $this->method,
        ];
        if (null !== $this->params) {
            $array['params'] = $this->params;
        }

        return $array;
    }
}
