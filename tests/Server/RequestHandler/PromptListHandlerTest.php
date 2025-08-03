<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Tests\Server\RequestHandler;

use Mcp\Capability\Prompt\MetadataInterface;
use Mcp\Capability\PromptChain;
use Mcp\Schema\Request\ListPromptsRequest;
use Mcp\Schema\Result\ListPromptsResult;
use Mcp\Server\RequestHandler\ListPromptsHandler;
use Nyholm\NSA;
use PHPUnit\Framework\TestCase;

class PromptListHandlerTest extends TestCase
{
    public function testHandleEmpty()
    {
        $handler = new ListPromptsHandler(new PromptChain([]));
        $request = new ListPromptsRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListPromptsResult::class, $response->result);
        $this->assertEquals(1, $response->getId());
        $this->assertEquals([], $response->result->prompts);
    }

    public function testHandleReturnAll()
    {
        $item = self::createMetadataItem();
        $handler = new ListPromptsHandler(new PromptChain([$item]));
        $request = new ListPromptsRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListPromptsResult::class, $response->result);
        $this->assertCount(1, $response->result->prompts);
        $this->assertNull($response->result->nextCursor);
    }

    public function testHandlePagination()
    {
        $item = self::createMetadataItem();
        $handler = new ListPromptsHandler(new PromptChain([$item, $item]), 2);
        $request = new ListPromptsRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListPromptsResult::class, $response->result);
        $this->assertCount(2, $response->result->prompts);
        $this->assertNotNull($response->result->nextCursor);
    }

    private static function createMetadataItem(): MetadataInterface
    {
        return new class implements MetadataInterface {
            public function getName(): string
            {
                return 'greet';
            }

            public function getDescription(): string
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
        };
    }
}
