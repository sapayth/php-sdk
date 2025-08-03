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

use Mcp\Capability\Tool\CollectionInterface;
use Mcp\Schema\JsonRpc\HasMethodInterface;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Schema\Request\ListToolsRequest;
use Mcp\Schema\Result\ListToolsResult;
use Mcp\Schema\Tool;
use Mcp\Server\MethodHandlerInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ListToolsHandler implements MethodHandlerInterface
{
    public function __construct(
        private readonly CollectionInterface $collection,
        private readonly int $pageSize = 20,
    ) {
    }

    public function supports(HasMethodInterface $message): bool
    {
        return $message instanceof ListToolsRequest;
    }

    public function handle(ListToolsRequest|HasMethodInterface $message): Response
    {
        \assert($message instanceof ListToolsRequest);

        $cursor = null;
        $tools = [];

        $metadataList = $this->collection->getMetadata($this->pageSize, $message->cursor);

        foreach ($metadataList as $tool) {
            $cursor = $tool->getName();
            $inputSchema = $tool->getInputSchema();
            $tools[] = new Tool(
                $tool->getName(),
                [] === $inputSchema ? [
                    'type' => 'object',
                    '$schema' => 'http://json-schema.org/draft-07/schema#',
                ] : $inputSchema,
                $tool->getDescription(),
                null,
            );
        }

        $nextCursor = (null !== $cursor && \count($tools) === $this->pageSize) ? $cursor : null;

        return new Response(
            $message->getId(),
            new ListToolsResult($tools, $nextCursor),
        );
    }
}
