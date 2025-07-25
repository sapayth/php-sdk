<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Server\RequestHandler;

use Mcp\Capability\Tool\CollectionInterface;
use Mcp\Message\Request;
use Mcp\Message\Response;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ToolListHandler extends BaseRequestHandler
{
    public function __construct(
        private readonly CollectionInterface $collection,
        private readonly int $pageSize = 20,
    ) {
    }

    public function createResponse(Request $message): Response
    {
        $nextCursor = null;
        $tools = [];

        $metadataList = $this->collection->getMetadata(
            $this->pageSize,
            $message->params['cursor'] ?? null
        );

        foreach ($metadataList as $tool) {
            $nextCursor = $tool->getName();
            $inputSchema = $tool->getInputSchema();
            $tools[] = [
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'inputSchema' => [] === $inputSchema ? [
                    'type' => 'object',
                    '$schema' => 'http://json-schema.org/draft-07/schema#',
                ] : $inputSchema,
            ];
        }

        $result = [
            'tools' => $tools,
        ];

        if (null !== $nextCursor && \count($tools) === $this->pageSize) {
            $result['nextCursor'] = $nextCursor;
        }

        return new Response($message->id, $result);
    }

    protected function supportedMethod(): string
    {
        return 'tools/list';
    }
}
