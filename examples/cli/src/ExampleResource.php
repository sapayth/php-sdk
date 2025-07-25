<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Mcp\Capability\Resource\MetadataInterface;
use Mcp\Capability\Resource\ResourceRead;
use Mcp\Capability\Resource\ResourceReaderInterface;
use Mcp\Capability\Resource\ResourceReadResult;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ExampleResource implements MetadataInterface, ResourceReaderInterface
{
    public function read(ResourceRead $input): ResourceReadResult
    {
        return new ResourceReadResult(
            'Content of '.$this->getName(),
            $this->getUri(),
        );
    }

    public function getUri(): string
    {
        return 'file:///project/src/main.rs';
    }

    public function getName(): string
    {
        return 'My resource';
    }

    public function getDescription(): ?string
    {
        return 'This is just an example';
    }

    public function getMimeType(): ?string
    {
        return null;
    }

    public function getSize(): ?int
    {
        return null;
    }
}
