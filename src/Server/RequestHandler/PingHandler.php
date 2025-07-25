<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Server\RequestHandler;

use Mcp\Message\Request;
use Mcp\Message\Response;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class PingHandler extends BaseRequestHandler
{
    public function createResponse(Request $message): Response
    {
        return new Response($message->id, []);
    }

    protected function supportedMethod(): string
    {
        return 'ping';
    }
}
