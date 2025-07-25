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

use Mcp\Capability\Prompt\MetadataInterface;
use Mcp\Capability\Prompt\PromptGet;
use Mcp\Capability\Prompt\PromptGetResult;
use Mcp\Capability\Prompt\PromptGetResultMessages;
use Mcp\Capability\Prompt\PromptGetterInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ExamplePrompt implements MetadataInterface, PromptGetterInterface
{
    public function get(PromptGet $input): PromptGetResult
    {
        $firstName = $input->arguments['first name'] ?? null;

        return new PromptGetResult(
            $this->getDescription(),
            [new PromptGetResultMessages(
                'user',
                \sprintf('Hello %s', $firstName ?? 'World')
            )]
        );
    }

    public function getName(): string
    {
        return 'Greet';
    }

    public function getDescription(): ?string
    {
        return 'Greet a person with a nice message';
    }

    public function getArguments(): array
    {
        return [
            [
                'name' => 'first name',
                'description' => 'The name of the person to greet',
                'required' => false,
            ],
        ];
    }
}
