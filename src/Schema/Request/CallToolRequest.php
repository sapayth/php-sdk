<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Schema\Request;

use Mcp\Exception\InvalidArgumentException;
use Mcp\Schema\JsonRpc\Request;

/**
 * Used by the client to invoke a tool provided by the server.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class CallToolRequest extends Request
{
    /**
     * @param string                $name      the name of the tool to invoke
     * @param array<string, mixed>  $arguments the arguments to pass to the tool
     * @param ?array<string, mixed> $_meta     optional metadata to include in the request
     */
    public function __construct(
        string|int $id,
        public readonly string $name,
        public readonly array $arguments,
        public readonly ?array $_meta = null,
    ) {
        $params = [
            'name' => $name,
            'arguments' => (object) $arguments,
        ];

        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct($id, 'tools/call', $params);
    }

    public static function fromRequest(Request $request): self
    {
        if ('tools/call' !== $request->method) {
            throw new InvalidArgumentException('Request is not a call tool request');
        }

        $params = $request->params ?? [];

        if (!isset($params['name']) || !\is_string($params['name'])) {
            throw new InvalidArgumentException('Missing or invalid "name" parameter for tools/call.');
        }

        $arguments = $params['arguments'] ?? [];

        if ($arguments instanceof \stdClass) {
            $arguments = (array) $arguments;
        }

        if (!\is_array($arguments)) {
            throw new InvalidArgumentException('Parameter "arguments" must be an array.');
        }

        return new self(
            $request->id,
            $params['name'],
            $arguments,
            $params['_meta'] ?? null
        );
    }
}
