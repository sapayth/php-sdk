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

use Mcp\Capability\Prompt\PromptGet;
use Mcp\Capability\Prompt\PromptGetterInterface;
use Mcp\Exception\ExceptionInterface;
use Mcp\Exception\InvalidArgumentException;
use Mcp\Message\Error;
use Mcp\Message\Request;
use Mcp\Message\Response;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class PromptGetHandler extends BaseRequestHandler
{
    public function __construct(
        private readonly PromptGetterInterface $getter,
    ) {
    }

    public function createResponse(Request $message): Response|Error
    {
        $name = $message->params['name'];
        $arguments = $message->params['arguments'] ?? [];

        try {
            $result = $this->getter->get(new PromptGet(uniqid('', true), $name, $arguments));
        } catch (ExceptionInterface) {
            return Error::internalError($message->id, 'Error while handling prompt');
        }

        $messages = [];
        foreach ($result->messages as $resultMessage) {
            $content = match ($resultMessage->type) {
                'text' => [
                    'type' => 'text',
                    'text' => $resultMessage->result,
                ],
                'image', 'audio' => [
                    'type' => $resultMessage->type,
                    'data' => $resultMessage->result,
                    'mimeType' => $resultMessage->mimeType,
                ],
                'resource' => [
                    'type' => 'resource',
                    'resource' => [
                        'uri' => $resultMessage->uri,
                        'mimeType' => $resultMessage->mimeType,
                        'text' => $resultMessage->result,
                    ],
                ],
                // TODO better exception
                default => throw new InvalidArgumentException('Unsupported PromptGet result type: '.$resultMessage->type),
            };

            $messages[] = [
                'role' => $resultMessage->role,
                'content' => $content,
            ];
        }

        return new Response($message->id, [
            'description' => $result->description,
            'messages' => $messages,
        ]);
    }

    protected function supportedMethod(): string
    {
        return 'prompts/get';
    }
}
