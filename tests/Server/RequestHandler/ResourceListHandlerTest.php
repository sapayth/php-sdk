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

use Mcp\Capability\Resource\CollectionInterface;
use Mcp\Capability\Resource\MetadataInterface;
use Mcp\Schema\Request\ListResourcesRequest;
use Mcp\Schema\Result\ListResourcesResult;
use Mcp\Server\RequestHandler\ListResourcesHandler;
use Nyholm\NSA;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ResourceListHandlerTest extends TestCase
{
    public function testHandleEmpty()
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $collection->expects($this->once())->method('getMetadata')->willReturn([]);

        $handler = new ListResourcesHandler($collection);
        $request = new ListResourcesRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListResourcesResult::class, $response->result);
        $this->assertEquals(1, $response->id);
        $this->assertEquals([], $response->result->resources);
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

        $handler = new ListResourcesHandler($collection);
        $request = new ListResourcesRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListResourcesResult::class, $response->result);
        $this->assertCount(1, $response->result->resources);
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

        $handler = new ListResourcesHandler($collection, 2);
        $request = new ListResourcesRequest();
        NSA::setProperty($request, 'id', 1);
        $response = $handler->handle($request);

        $this->assertInstanceOf(ListResourcesResult::class, $response->result);
        $this->assertCount(2, $response->result->resources);
        $this->assertNotNull($response->result->nextCursor);
    }

    private static function createMetadataItem(): MetadataInterface
    {
        return new class implements MetadataInterface {
            public function getUri(): string
            {
                return 'file:///src/SomeFile.php';
            }

            public function getName(): string
            {
                return 'SomeFile';
            }

            public function getDescription(): string
            {
                return 'File src/SomeFile.php';
            }

            public function getMimeType(): string
            {
                return 'text/plain';
            }

            public function getSize(): int
            {
                return 1024;
            }
        };
    }
}
