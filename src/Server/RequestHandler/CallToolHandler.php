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

use Mcp\Capability\Tool\ToolExecutorInterface;
use Mcp\Exception\ExceptionInterface;
use Mcp\Schema\JsonRpc\Error;
use Mcp\Schema\JsonRpc\HasMethodInterface;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Schema\Request\CallToolRequest;
use Mcp\Server\MethodHandlerInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CallToolHandler implements MethodHandlerInterface
{
    public function __construct(
        private readonly ToolExecutorInterface $toolExecutor,
    ) {
    }

    public function supports(HasMethodInterface $message): bool
    {
        return $message instanceof CallToolRequest;
    }

    public function handle(CallToolRequest|HasMethodInterface $message): Response|Error
    {
        \assert($message instanceof CallToolRequest);

        try {
            $result = $this->toolExecutor->call($message);
        } catch (ExceptionInterface) {
            return Error::forInternalError('Error while executing tool', $message->getId());
        }

        return new Response($message->getId(), $result);
    }
}
