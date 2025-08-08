<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Capability;

use Mcp\Capability\Registry\RegisteredElement;
use Mcp\Capability\Registry\RegisteredPrompt;
use Mcp\Capability\Registry\RegisteredResource;
use Mcp\Capability\Registry\RegisteredResourceTemplate;
use Mcp\Capability\Registry\RegisteredTool;
use Mcp\Event\PromptListChangedEvent;
use Mcp\Event\ResourceListChangedEvent;
use Mcp\Event\ResourceTemplateListChangedEvent;
use Mcp\Event\ToolListChangedEvent;
use Mcp\Schema\Prompt;
use Mcp\Schema\Resource;
use Mcp\Schema\ResourceTemplate;
use Mcp\Schema\Tool;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @phpstan-import-type CallableArray from RegisteredElement
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class Registry
{
    /**
     * @var array<string, RegisteredTool>
     */
    private array $tools = [];

    /**
     * @var array<string, RegisteredResource>
     */
    private array $resources = [];

    /**
     * @var array<string, RegisteredPrompt>
     */
    private array $prompts = [];

    /**
     * @var array<string, RegisteredResourceTemplate>
     */
    private array $resourceTemplates = [];

    public function __construct(
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /**
     * @param callable|CallableArray|string $handler
     */
    public function registerTool(Tool $tool, callable|array|string $handler, bool $isManual = false): void
    {
        $toolName = $tool->name;
        $existing = $this->tools[$toolName] ?? null;

        if ($existing && !$isManual && $existing->isManual) {
            $this->logger->debug("Ignoring discovered tool '{$toolName}' as it conflicts with a manually registered one.");

            return;
        }

        $this->tools[$toolName] = new RegisteredTool($tool, $handler, $isManual);

        $this->eventDispatcher?->dispatch(new ToolListChangedEvent());
    }

    /**
     * @param callable|CallableArray|string $handler
     */
    public function registerResource(Resource $resource, callable|array|string $handler, bool $isManual = false): void
    {
        $uri = $resource->uri;
        $existing = $this->resources[$uri] ?? null;

        if ($existing && !$isManual && $existing->isManual) {
            $this->logger->debug("Ignoring discovered resource '{$uri}' as it conflicts with a manually registered one.");

            return;
        }

        $this->resources[$uri] = new RegisteredResource($resource, $handler, $isManual);

        $this->eventDispatcher?->dispatch(new ResourceListChangedEvent());
    }

    /**
     * @param callable|CallableArray|string      $handler
     * @param array<string, class-string|object> $completionProviders
     */
    public function registerResourceTemplate(
        ResourceTemplate $template,
        callable|array|string $handler,
        array $completionProviders = [],
        bool $isManual = false,
    ): void {
        $uriTemplate = $template->uriTemplate;
        $existing = $this->resourceTemplates[$uriTemplate] ?? null;

        if ($existing && !$isManual && $existing->isManual) {
            $this->logger->debug("Ignoring discovered template '{$uriTemplate}' as it conflicts with a manually registered one.");

            return;
        }

        $this->resourceTemplates[$uriTemplate] = new RegisteredResourceTemplate($template, $handler, $isManual, $completionProviders);

        $this->eventDispatcher?->dispatch(new ResourceTemplateListChangedEvent());
    }

    /**
     * @param callable|CallableArray|string      $handler
     * @param array<string, class-string|object> $completionProviders
     */
    public function registerPrompt(
        Prompt $prompt,
        callable|array|string $handler,
        array $completionProviders = [],
        bool $isManual = false,
    ): void {
        $promptName = $prompt->name;
        $existing = $this->prompts[$promptName] ?? null;

        if ($existing && !$isManual && $existing->isManual) {
            $this->logger->debug("Ignoring discovered prompt '{$promptName}' as it conflicts with a manually registered one.");

            return;
        }

        $this->prompts[$promptName] = new RegisteredPrompt($prompt, $handler, $isManual, $completionProviders);

        $this->eventDispatcher?->dispatch(new PromptListChangedEvent());
    }

    /** Checks if any elements (manual or discovered) are currently registered. */
    public function hasElements(): bool
    {
        return !empty($this->tools)
            || !empty($this->resources)
            || !empty($this->prompts)
            || !empty($this->resourceTemplates);
    }

    /**
     * Clear discovered elements from registry.
     */
    public function clear(): void
    {
        $clearCount = 0;

        foreach ($this->tools as $name => $tool) {
            if (!$tool->isManual) {
                unset($this->tools[$name]);
                ++$clearCount;
            }
        }
        foreach ($this->resources as $uri => $resource) {
            if (!$resource->isManual) {
                unset($this->resources[$uri]);
                ++$clearCount;
            }
        }
        foreach ($this->prompts as $name => $prompt) {
            if (!$prompt->isManual) {
                unset($this->prompts[$name]);
                ++$clearCount;
            }
        }
        foreach ($this->resourceTemplates as $uriTemplate => $template) {
            if (!$template->isManual) {
                unset($this->resourceTemplates[$uriTemplate]);
                ++$clearCount;
            }
        }

        if ($clearCount > 0) {
            $this->logger->debug(\sprintf('Removed %d discovered elements from internal registry.', $clearCount));
        }
    }

    public function getTool(string $name): ?RegisteredTool
    {
        return $this->tools[$name] ?? null;
    }

    public function getResource(string $uri, bool $includeTemplates = true): RegisteredResource|RegisteredResourceTemplate|null
    {
        $registration = $this->resources[$uri] ?? null;
        if ($registration) {
            return $registration;
        }

        if (!$includeTemplates) {
            return null;
        }

        foreach ($this->resourceTemplates as $template) {
            if ($template->matches($uri)) {
                return $template;
            }
        }

        $this->logger->debug('No resource matched URI.', ['uri' => $uri]);

        return null;
    }

    public function getResourceTemplate(string $uriTemplate): ?RegisteredResourceTemplate
    {
        return $this->resourceTemplates[$uriTemplate] ?? null;
    }

    public function getPrompt(string $name): ?RegisteredPrompt
    {
        return $this->prompts[$name] ?? null;
    }

    /** @return array<string, Tool> */
    public function getTools(): array
    {
        return array_map(fn ($tool) => $tool->tool, $this->tools);
    }

    /** @return array<string, resource> */
    public function getResources(): array
    {
        return array_map(fn ($resource) => $resource->schema, $this->resources);
    }

    /** @return array<string, Prompt> */
    public function getPrompts(): array
    {
        return array_map(fn ($prompt) => $prompt->prompt, $this->prompts);
    }

    /** @return array<string, ResourceTemplate> */
    public function getResourceTemplates(): array
    {
        return array_map(fn ($template) => $template->resourceTemplate, $this->resourceTemplates);
    }
}
