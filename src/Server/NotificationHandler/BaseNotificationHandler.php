<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Server\NotificationHandler;

use Mcp\Message\Notification;
use Mcp\Server\NotificationHandlerInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
abstract class BaseNotificationHandler implements NotificationHandlerInterface
{
    public function supports(Notification $message): bool
    {
        return $message->method === \sprintf('notifications/%s', $this->supportedNotification());
    }

    abstract protected function supportedNotification(): string;
}
