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
use Mcp\Server\NotificationHandler\InitializedHandler;
use Mcp\Server\NotificationHandlerInterface;
use Mcp\Server\RequestHandler\InitializeHandler;
use Mcp\Server\RequestHandler\PingHandler;
use Mcp\Server\RequestHandler\PromptGetHandler;
use Mcp\Server\RequestHandler\PromptListHandler;
use Mcp\Server\RequestHandler\ResourceListHandler;
use Mcp\Server\RequestHandler\ResourceReadHandler;
use Mcp\Server\RequestHandler\ToolCallHandler;
use Mcp\Server\RequestHandler\ToolListHandler;
use Mcp\Server\RequestHandlerInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Builder
{
    /**
     * @return list<RequestHandlerInterface>
     */
    public static function buildRequestHandlers(): array
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
            new InitializeHandler(),
            new PingHandler(),
            new PromptListHandler($promptManager),
            new PromptGetHandler($promptManager),
            new ResourceListHandler($resourceManager),
            new ResourceReadHandler($resourceManager),
            new ToolCallHandler($toolManager),
            new ToolListHandler($toolManager),
        ];
    }

    /**
     * @return list<NotificationHandlerInterface>
     */
    public static function buildNotificationHandlers(): array
    {
        return [
            new InitializedHandler(),
        ];
    }
}
