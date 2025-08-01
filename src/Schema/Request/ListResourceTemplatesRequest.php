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
 * Sent from the client to request a list of resource templates the server has.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class ListResourceTemplatesRequest extends Request
{
    /**
     * @param string|null $cursor An opaque token representing the current pagination position.
     *
     * If provided, the server should return results starting after this cursor.
     * @param ?array<string, mixed> $_meta optional metadata to include in the request
     */
    public function __construct(
        string|int $id,
        public readonly ?string $cursor = null,
        public readonly ?array $_meta = null,
    ) {
        $params = [];
        if (null !== $cursor) {
            $params['cursor'] = $cursor;
        }
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct($id, 'resources/templates/list', $params);
    }

    public static function fromRequest(Request $request): self
    {
        if ('resources/templates/list' !== $request->method) {
            throw new InvalidArgumentException('Request is not a list resource templates request');
        }

        return new self($request->id, $request->params['cursor'] ?? null, $request->params['_meta'] ?? null);
    }
}
