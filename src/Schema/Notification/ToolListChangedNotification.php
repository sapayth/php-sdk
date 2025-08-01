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
 * An optional notification from the server to the client, informing it that the list of tools it offers has changed. This may be issued by servers without any previous subscription from the client.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class ToolListChangedNotification extends Notification
{
    /**
     * @param array<string, mixed>|null $_meta
     */
    public function __construct(
        public readonly ?array $_meta = null,
    ) {
        $params = [];
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct('notifications/tools/list_changed', $params);
    }

    public static function fromNotification(Notification $notification): self
    {
        if ('notifications/tools/list_changed' !== $notification->method) {
            throw new InvalidArgumentException('Notification is not a notifications/tools/list_changed notification');
        }

        return new self($notification->params['_meta'] ?? null);
    }
}
