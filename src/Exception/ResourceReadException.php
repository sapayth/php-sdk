<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Exception;

use Mcp\Capability\Resource\ResourceRead;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ResourceReadException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(
        public readonly ResourceRead $readRequest,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(\sprintf('Reading resource "%s" failed with error: %s', $readRequest->uri, $previous?->getMessage() ?? ''), previous: $previous);
    }
}
