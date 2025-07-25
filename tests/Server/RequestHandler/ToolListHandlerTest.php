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

use Mcp\Capability\Tool\CollectionInterface;
use Mcp\Capability\Tool\MetadataInterface;
use Mcp\Message\Request;
use Mcp\Server\RequestHandler\ToolListHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(ToolListHandler::class)]
class ToolListHandlerTest extends TestCase
{
    public function testHandleEmpty(): void
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn([]);

        $handler = new ToolListHandler($collection);
        $message = new Request(1, 'tools/list', []);
        $response = $handler->createResponse($message);
        $this->assertEquals(1, $response->id);
        $this->assertEquals(['tools' => []], $response->result);
    }

    /**
     * @param iterable<MetadataInterface> $metadataList
     */
    #[DataProvider('metadataProvider')]
    public function testHandleReturnAll(iterable $metadataList): void
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn($metadataList);
        $handler = new ToolListHandler($collection);
        $message = new Request(1, 'tools/list', []);
        $response = $handler->createResponse($message);
        $this->assertCount(1, $response->result['tools']);
        $this->assertArrayNotHasKey('nextCursor', $response->result);
    }

    /**
     * @return array<string, iterable<MetadataInterface>>
     */
    public static function metadataProvider(): array
    {
        $item = self::createMetadataItem();

        return [
            'array' => [[$item]],
            'generator' => [(function () use ($item) { yield $item; })()],
        ];
    }

    #[Test]
    public function handlePagination(): void
    {
        $item = self::createMetadataItem();
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn([$item, $item]);
        $handler = new ToolListHandler($collection, 2);
        $message = new Request(1, 'tools/list', []);
        $response = $handler->createResponse($message);
        $this->assertCount(2, $response->result['tools']);
        $this->assertArrayHasKey('nextCursor', $response->result);
    }

    private static function createMetadataItem(): MetadataInterface
    {
        return new class implements MetadataInterface {
            public function getName(): string
            {
                return 'test_tool';
            }

            public function getDescription(): string
            {
                return 'A test tool';
            }

            public function getInputSchema(): array
            {
                return ['type' => 'object'];
            }
        };
    }
}
