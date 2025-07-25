<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Capability\Prompt;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class PromptGetResultMessages
{
    public function __construct(
        public readonly string $role,
        public readonly string $result,
        /**
         * @var "text"|"image"|"audio"|"resource"|non-empty-string
         */
        public readonly string $type = 'text',
        public readonly string $mimeType = 'text/plan',
        public readonly ?string $uri = null,
    ) {
    }
}
