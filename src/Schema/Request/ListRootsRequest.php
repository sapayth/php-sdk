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

use Mcp\Schema\JsonRpc\Request;

/**
 * Sent from the server to request a list of root URIs from the client. Roots allow
 * servers to ask for specific directories or files to operate on. A common example
 * for roots is providing a set of repositories or directories a server should operate
 * on.
 *
 * This request is typically used when the server needs to understand the file system
 * structure or access specific locations that the client has permission to read from.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class ListRootsRequest extends Request
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

        parent::__construct($id, 'roots/list', $params);
    }
}
