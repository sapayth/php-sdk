<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Capability\Tool;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ToolCallResult
{
    public function __construct(
        public readonly string $result,
        /**
         * @var "text"|"image"|"audio"|"resource"|non-empty-string
         */
        public readonly string $type = 'text',
        public readonly string $mimeType = 'text/plan',
        public readonly bool $isError = false,
        public readonly ?string $uri = null,
    ) {
    }
}
