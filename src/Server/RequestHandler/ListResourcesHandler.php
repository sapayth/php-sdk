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

use Mcp\Capability\Resource\CollectionInterface;
use Mcp\Schema\JsonRpc\HasMethodInterface;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Schema\Request\ListResourcesRequest;
use Mcp\Schema\Resource;
use Mcp\Schema\Result\ListResourcesResult;
use Mcp\Server\MethodHandlerInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ListResourcesHandler implements MethodHandlerInterface
{
    public function __construct(
        private readonly CollectionInterface $collection,
        private readonly int $pageSize = 20,
    ) {
    }

    public function supports(HasMethodInterface $message): bool
    {
        return $message instanceof ListResourcesRequest;
    }

    public function handle(ListResourcesRequest|HasMethodInterface $message): Response
    {
        \assert($message instanceof ListResourcesRequest);

        $cursor = null;
        $resources = [];

        $metadataList = $this->collection->getMetadata($this->pageSize, $message->cursor);

        foreach ($metadataList as $metadata) {
            $cursor = $metadata->getUri();
            $resources[] = new Resource(
                $metadata->getUri(),
                $metadata->getName(),
                $metadata->getDescription(),
                $metadata->getMimeType(),
                null,
                $metadata->getSize(),
            );
        }

        $nextCursor = (null !== $cursor && \count($resources) === $this->pageSize) ? $cursor : null;

        return new Response(
            $message->getId(),
            new ListResourcesResult($resources, $nextCursor),
        );
    }
}
