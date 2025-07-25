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

use Mcp\Capability\Resource\ResourceRead;
use Mcp\Capability\Resource\ResourceReaderInterface;
use Mcp\Exception\ExceptionInterface;
use Mcp\Exception\ResourceNotFoundException;
use Mcp\Message\Error;
use Mcp\Message\Request;
use Mcp\Message\Response;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ResourceReadHandler extends BaseRequestHandler
{
    public function __construct(
        private readonly ResourceReaderInterface $reader,
    ) {
    }

    public function createResponse(Request $message): Response|Error
    {
        $uri = $message->params['uri'];

        try {
            $result = $this->reader->read(new ResourceRead(uniqid('', true), $uri));
        } catch (ResourceNotFoundException $e) {
            return new Error($message->id, Error::RESOURCE_NOT_FOUND, $e->getMessage());
        } catch (ExceptionInterface) {
            return Error::internalError($message->id, 'Error while reading resource');
        }

        return new Response($message->id, [
            'contents' => [
                [
                    'uri' => $result->uri,
                    'mimeType' => $result->mimeType,
                    $result->type => $result->result,
                ],
            ],
        ]);
    }

    protected function supportedMethod(): string
    {
        return 'resources/read';
    }
}
