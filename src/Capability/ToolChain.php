<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Capability;

use Mcp\Capability\Tool\CollectionInterface;
use Mcp\Capability\Tool\IdentifierInterface;
use Mcp\Capability\Tool\MetadataInterface;
use Mcp\Capability\Tool\ToolCall;
use Mcp\Capability\Tool\ToolCallResult;
use Mcp\Capability\Tool\ToolExecutorInterface;
use Mcp\Exception\InvalidCursorException;
use Mcp\Exception\ToolExecutionException;
use Mcp\Exception\ToolNotFoundException;

/**
 * A collection of tools. All tools need to implement IdentifierInterface.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ToolChain implements ToolExecutorInterface, CollectionInterface
{
    public function __construct(
        /**
         * @var IdentifierInterface[] $items
         */
        private readonly array $items,
    ) {
    }

    public function getMetadata(int $count, ?string $lastIdentifier = null): iterable
    {
        $found = null === $lastIdentifier;
        foreach ($this->items as $item) {
            if (!$item instanceof MetadataInterface) {
                continue;
            }

            if (false === $found) {
                $found = $item->getName() === $lastIdentifier;
                continue;
            }

            yield $item;
            if (--$count <= 0) {
                break;
            }
        }

        if (!$found) {
            throw new InvalidCursorException($lastIdentifier);
        }
    }

    public function call(ToolCall $input): ToolCallResult
    {
        foreach ($this->items as $item) {
            if ($item instanceof ToolExecutorInterface && $input->name === $item->getName()) {
                try {
                    return $item->call($input);
                } catch (\Throwable $e) {
                    throw new ToolExecutionException($input, $e);
                }
            }
        }

        throw new ToolNotFoundException($input);
    }
}
