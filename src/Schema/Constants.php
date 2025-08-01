<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Schema;

/**
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
interface Constants
{
    public const LATEST_PROTOCOL_VERSION = '2025-03-26';
    public const JSONRPC_VERSION = '2.0';

    public const PARSE_ERROR = -32700;
    public const INVALID_REQUEST = -32600;
    public const METHOD_NOT_FOUND = -32601;
    public const INVALID_PARAMS = -32602;
    public const INTERNAL_ERROR = -32603;

    public const SERVER_ERROR = -32000;
}
