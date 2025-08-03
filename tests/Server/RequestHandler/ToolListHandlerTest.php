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
use Mcp\Schema\Request\ListToolsRequest;
use Mcp\Schema\Result\ListToolsResult;
use Mcp\Server\RequestHandler\ListToolsHandler;
use Nyholm\NSA;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ToolListHandlerTest extends TestCase
{
    public function testHandleEmpty()
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn([]);

        $handler = new ListToolsHandler($collection);
        $request = new ListToolsRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListToolsResult::class, $response->result);
        $this->assertSame(1, $response->id);
        $this->assertSame([], $response->result->tools);
    }

    /**
     * @param iterable<MetadataInterface> $metadataList
     */
    #[DataProvider('metadataProvider')]
    public function testHandleReturnAll(iterable $metadataList)
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn($metadataList);
        $handler = new ListToolsHandler($collection);
        $request = new ListToolsRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListToolsResult::class, $response->result);
        $this->assertCount(1, $response->result->tools);
        $this->assertNull($response->result->nextCursor);
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

    public function testHandlePagination()
    {
        $item = self::createMetadataItem();
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn([$item, $item]);
        $handler = new ListToolsHandler($collection, 2);
        $request = new ListToolsRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListToolsResult::class, $response->result);
        $this->assertCount(2, $response->result->tools);
        $this->assertNotNull($response->result->nextCursor);
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
