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

use Mcp\Capability\Tool\ToolCall;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ToolExecutionException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(
        public readonly ToolCall $toolCall,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(\sprintf('Execution of tool "%s" failed with error: %s', $toolCall->name, $previous?->getMessage() ?? ''), previous: $previous);
    }
}
