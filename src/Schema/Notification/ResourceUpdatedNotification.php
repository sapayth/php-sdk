<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Schema\Notification;

use Mcp\Exception\InvalidArgumentException;
use Mcp\Schema\JsonRpc\Notification;

/**
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class ResourceUpdatedNotification extends Notification
{
    /**
     * @param array<string, mixed>|null $_meta
     */
    public function __construct(
        public readonly string $uri,
        public readonly ?array $_meta = null,
    ) {
        $params = ['uri' => $uri];
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct('notifications/resources/updated', $params);
    }

    public static function fromNotification(Notification $notification): self
    {
        if ('notifications/resources/updated' !== $notification->method) {
            throw new InvalidArgumentException('Notification is not a notifications/resources/updated notification');
        }

        $params = $notification->params;

        if (!isset($params['uri']) || !\is_string($params['uri'])) {
            throw new InvalidArgumentException('Missing or invalid uri parameter for notifications/resources/updated notification');
        }

        return new self($params['uri'], $params['_meta'] ?? null);
    }
}
