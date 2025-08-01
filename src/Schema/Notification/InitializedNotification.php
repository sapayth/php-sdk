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
 * This notification is sent from the client to the server after initialization has finished.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class InitializedNotification extends Notification
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

        parent::__construct('notifications/initialized', $params);
    }

    public static function fromNotification(Notification $notification): self
    {
        if ('notifications/initialized' !== $notification->method) {
            throw new InvalidArgumentException('Notification is not a notifications/initialized notification');
        }

        return new self($notification->params['_meta'] ?? null);
    }
}
