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
 * A ping, issued by either the server or the client, to check that the other party is still alive. The receiver must
 * promptly respond, or else may be disconnected.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class PingRequest extends Request
{
    /**
     * @param ?array<string, mixed> $_meta
     */
    public function __construct(
        string|int $id,
        public readonly ?array $_meta = null,
    ) {
        $params = [];
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct($id, 'ping', $params);
    }

    public static function fromRequest(Request $request): self
    {
        if ('ping' !== $request->method) {
            throw new InvalidArgumentException('Request is not a ping request');
        }

        return new self($request->id, $request->params['_meta'] ?? null);
    }
}
