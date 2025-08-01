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
 * @phpstan-type NotificationData array{
 *     jsonrpc: string,
 *     method: string,
 *     params?: array<string, mixed>|null
 * }
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class Notification implements MessageInterface
{
    /**
     * @param string                $method the name of the method to be invoked
     * @param ?array<string, mixed> $params parameters for the method
     */
    public function __construct(
        public readonly string $method,
        public readonly ?array $params = null,
    ) {
    }

    /**
     * @param NotificationData $data
     */
    public static function fromArray(array $data): self
    {
        if (isset($data['id'])) {
            throw new InvalidArgumentException('Notification MUST NOT contain an "id" field.');
        }
        if (!isset($data['method']) || !\is_string($data['method'])) {
            throw new InvalidArgumentException('Invalid or missing "method" for Notification.');
        }
        $params = $data['params'] ?? null;
        if (null !== $params && !\is_array($params)) {
            throw new InvalidArgumentException('"params" for Notification must be an array/object or null.');
        }

        return new self($data['method'], $params);
    }

    /**
     * @return null
     */
    public function getId()
    {
        return null;
    }

    /**
     * @return NotificationData
     */
    public function jsonSerialize(): array
    {
        $array = [
            'jsonrpc' => Constants::JSONRPC_VERSION,
            'method' => $this->method,
        ];
        if (null !== $this->params) {
            $array['params'] = $this->params;
        }

        return $array;
    }
}
