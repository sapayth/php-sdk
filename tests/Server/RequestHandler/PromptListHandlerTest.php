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
use Mcp\Message\Request;
use Mcp\Server\RequestHandler\PromptListHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(PromptListHandler::class)]
class PromptListHandlerTest extends TestCase
{
    public function testHandleEmpty(): void
    {
        $handler = new PromptListHandler(new PromptChain([]));
        $message = new Request(1, 'prompts/list', []);
        $response = $handler->createResponse($message);
        $this->assertEquals(1, $response->id);
        $this->assertEquals(['prompts' => []], $response->result);
    }

    public function testHandleReturnAll(): void
    {
        $item = self::createMetadataItem();
        $handler = new PromptListHandler(new PromptChain([$item]));
        $message = new Request(1, 'prompts/list', []);
        $response = $handler->createResponse($message);
        $this->assertCount(1, $response->result['prompts']);
        $this->assertArrayNotHasKey('nextCursor', $response->result);
    }

    public function testHandlePagination(): void
    {
        $item = self::createMetadataItem();
        $handler = new PromptListHandler(new PromptChain([$item, $item]), 2);
        $message = new Request(1, 'prompts/list', []);
        $response = $handler->createResponse($message);
        $this->assertCount(2, $response->result['prompts']);
        $this->assertArrayHasKey('nextCursor', $response->result);
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
