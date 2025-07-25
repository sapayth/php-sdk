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

use Mcp\Capability\Tool\ToolCall;
use Mcp\Capability\Tool\ToolExecutorInterface;
use Mcp\Exception\ExceptionInterface;
use Mcp\Exception\InvalidArgumentException;
use Mcp\Message\Error;
use Mcp\Message\Request;
use Mcp\Message\Response;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ToolCallHandler extends BaseRequestHandler
{
    public function __construct(
        private readonly ToolExecutorInterface $toolExecutor,
    ) {
    }

    public function createResponse(Request $message): Response|Error
    {
        $name = $message->params['name'];
        $arguments = $message->params['arguments'] ?? [];

        try {
            $result = $this->toolExecutor->call(new ToolCall(uniqid('', true), $name, $arguments));
        } catch (ExceptionInterface) {
            return Error::internalError($message->id, 'Error while executing tool');
        }

        $content = match ($result->type) {
            'text' => [
                'type' => 'text',
                'text' => $result->result,
            ],
            'image', 'audio' => [
                'type' => $result->type,
                'data' => $result->result,
                'mimeType' => $result->mimeType,
            ],
            'resource' => [
                'type' => 'resource',
                'resource' => [
                    'uri' => $result->uri,
                    'mimeType' => $result->mimeType,
                    'text' => $result->result,
                ],
            ],
            // TODO better exception
            default => throw new InvalidArgumentException('Unsupported tool result type: '.$result->type),
        };

        return new Response($message->id, [
            'content' => [$content], // TODO: allow multiple `ToolCallResult`s in the future
            'isError' => $result->isError,
        ]);
    }

    protected function supportedMethod(): string
    {
        return 'tools/call';
    }
}
