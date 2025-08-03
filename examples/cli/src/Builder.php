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

use Mcp\Capability\PromptChain;
use Mcp\Capability\ResourceChain;
use Mcp\Capability\ToolChain;
use Mcp\Server\MethodHandlerInterface;
use Mcp\Server\NotificationHandler;
use Mcp\Server\RequestHandler;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Builder
{
    /**
     * @return list<MethodHandlerInterface>
     */
    public static function buildMethodHandlers(): array
    {
        $promptManager = new PromptChain([
            new ExamplePrompt(),
        ]);

        $resourceManager = new ResourceChain([
            new ExampleResource(),
        ]);

        $toolManager = new ToolChain([
            new ExampleTool(),
        ]);

        return [
            new NotificationHandler\InitializedHandler(),
            new RequestHandler\InitializeHandler(),
            new RequestHandler\PingHandler(),
            new RequestHandler\ListPromptsHandler($promptManager),
            new RequestHandler\GetPromptHandler($promptManager),
            new RequestHandler\ListResourcesHandler($resourceManager),
            new RequestHandler\ReadResourceHandler($resourceManager),
            new RequestHandler\CallToolHandler($toolManager),
            new RequestHandler\ListToolsHandler($toolManager),
        ];
    }
}
