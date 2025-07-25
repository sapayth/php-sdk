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

use Mcp\Capability\Prompt\PromptGet;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class PromptGetException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(
        public readonly PromptGet $promptGet,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(\sprintf('Handling prompt "%s" failed with error: %s', $promptGet->name, $previous->getMessage()), previous: $previous);
    }
}
