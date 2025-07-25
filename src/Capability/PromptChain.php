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

use Mcp\Capability\Prompt\CollectionInterface;
use Mcp\Capability\Prompt\IdentifierInterface;
use Mcp\Capability\Prompt\MetadataInterface;
use Mcp\Capability\Prompt\PromptGet;
use Mcp\Capability\Prompt\PromptGetResult;
use Mcp\Capability\Prompt\PromptGetterInterface;
use Mcp\Exception\InvalidCursorException;
use Mcp\Exception\PromptGetException;
use Mcp\Exception\PromptNotFoundException;

/**
 * A collection of prompts. All prompts need to implement IdentifierInterface.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class PromptChain implements PromptGetterInterface, CollectionInterface
{
    public function __construct(
        /**
         * @var IdentifierInterface[]
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

    public function get(PromptGet $input): PromptGetResult
    {
        foreach ($this->items as $item) {
            if ($item instanceof PromptGetterInterface && $input->name === $item->getName()) {
                try {
                    return $item->get($input);
                } catch (\Throwable $e) {
                    throw new PromptGetException($input, $e);
                }
            }
        }

        throw new PromptNotFoundException($input);
    }
}
