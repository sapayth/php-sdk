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

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class InitializedHandler extends BaseNotificationHandler
{
    protected function supportedNotification(): string
    {
        return 'initialized';
    }

    public function handle(Notification $notification): void
    {
    }
}
