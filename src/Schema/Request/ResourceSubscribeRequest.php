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
 * Sent from the client to request resources/updated notifications from the server whenever a particular resource
 * changes.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class ResourceSubscribeRequest extends Request
{
    /**
     * @param string                $uri   the URI of the resource to subscribe to
     * @param ?array<string, mixed> $_meta optional metadata to include in the request
     */
    public function __construct(
        string|int $id,
        public readonly string $uri,
        public readonly ?array $_meta = null,
    ) {
        $params = ['uri' => $uri];
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct($id, 'resources/subscribe', $params);
    }

    public static function fromRequest(Request $request): self
    {
        if ('resources/subscribe' !== $request->method) {
            throw new InvalidArgumentException('Request is not a resource subscribe request');
        }

        $params = $request->params;

        if (!isset($params['uri']) || !\is_string($params['uri']) || empty($params['uri'])) {
            throw new InvalidArgumentException('Missing or invalid "uri" parameter for resources/subscribe.');
        }

        return new self($request->id, $params['uri'], $params['_meta'] ?? null);
    }
}
