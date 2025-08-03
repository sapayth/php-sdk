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

use Mcp\Capability\Prompt\CollectionInterface;
use Mcp\Schema\JsonRpc\HasMethodInterface;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Schema\Prompt;
use Mcp\Schema\PromptArgument;
use Mcp\Schema\Request\ListPromptsRequest;
use Mcp\Schema\Result\ListPromptsResult;
use Mcp\Server\MethodHandlerInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ListPromptsHandler implements MethodHandlerInterface
{
    public function __construct(
        private readonly CollectionInterface $collection,
        private readonly int $pageSize = 20,
    ) {
    }

    public function supports(HasMethodInterface $message): bool
    {
        return $message instanceof ListPromptsRequest;
    }

    public function handle(ListPromptsRequest|HasMethodInterface $message): Response
    {
        \assert($message instanceof ListPromptsRequest);

        $cursor = null;
        $prompts = [];

        $metadataList = $this->collection->getMetadata($this->pageSize, $message->cursor);

        foreach ($metadataList as $metadata) {
            $cursor = $metadata->getName();
            $prompts[] = new Prompt(
                $metadata->getName(),
                $metadata->getDescription(),
                array_map(fn (array $data) => PromptArgument::fromArray($data), $metadata->getArguments()),
            );
        }

        $nextCursor = (null !== $cursor && \count($prompts) === $this->pageSize) ? $cursor : null;

        return new Response(
            $message->getId(),
            new ListPromptsResult($prompts, $nextCursor),
        );
    }
}
