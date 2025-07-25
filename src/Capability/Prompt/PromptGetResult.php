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
final class PromptGetResult
{
    /**
     * @param list<PromptGetResultMessages> $messages
     */
    public function __construct(
        public readonly string $description,
        public readonly array $messages = [],
    ) {
    }
}
