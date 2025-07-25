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
use Mcp\Server\RequestHandlerInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
abstract class BaseRequestHandler implements RequestHandlerInterface
{
    public function supports(Request $message): bool
    {
        return $message->method === $this->supportedMethod();
    }

    abstract protected function supportedMethod(): string;
}
