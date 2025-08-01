<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Schema\JsonRpc;

use Mcp\Exception\InvalidArgumentException;

/**
 * A JSON-RPC batch response, as described in https://www.jsonrpc.org/specification#batch.
 *
 * @phpstan-import-type ResponseData from Response
 * @phpstan-import-type ErrorData from Error
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class BatchResponse implements MessageInterface, \Countable
{
    /**
     * @param array<Response|Error> $items the individual responses/errors in this batch
     */
    public function __construct(
        public array $items,
    ) {
        foreach ($items as $item) {
            if (!($item instanceof Response || $item instanceof Error)) {
                throw new InvalidArgumentException('All items in BatchResponse must be instances of Response or Error.');
            }
        }
    }

    /**
     * @param array<ErrorData|ResponseData> $data
     */
    public static function fromArray(array $data): self
    {
        if (empty($data)) {
            throw new InvalidArgumentException('BatchResponse data array must not be empty.');
        }

        $items = [];
        foreach ($data as $itemData) {
            if (!\is_array($itemData)) {
                throw new InvalidArgumentException('BatchResponse item data must be an array.');
            }
            if (isset($itemData['id'])) {
                $items[] = Response::fromArray($itemData);
            } elseif (isset($itemData['error'])) {
                $items[] = Error::fromArray($itemData);
            } else {
                throw new InvalidArgumentException('Invalid item in BatchResponse data: missing "id" or "error".');
            }
        }

        return new self($items);
    }

    public function getId(): string|int
    {
        foreach ($this->items as $item) {
            if ($item instanceof Response) {
                return $item->getId();
            }
        }

        throw new InvalidArgumentException('BatchResponse does not contain any Response items with an "id".');
    }

    public function hasResponses(): bool
    {
        $hasResponses = false;
        foreach ($this->items as $item) {
            if ($item instanceof Response) {
                $hasResponses = true;
                break;
            }
        }

        return $hasResponses;
    }

    public function hasErrors(): bool
    {
        $hasErrors = false;
        foreach ($this->items as $item) {
            if ($item instanceof Error) {
                $hasErrors = true;
                break;
            }
        }

        return $hasErrors;
    }

    /**
     * @return Response[]
     */
    public function getResponses(): array
    {
        return array_filter($this->items, fn ($item) => $item instanceof Response);
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return array_filter($this->items, fn ($item) => $item instanceof Error);
    }

    /**
     * @return Error[]|Response[]
     */
    public function getAll(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @return Error[]|Response[]
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }
}
