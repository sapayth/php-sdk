<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Server;

use Mcp\Exception\ExceptionInterface;
use Mcp\Message\Notification;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
interface NotificationHandlerInterface
{
    public function supports(Notification $message): bool;

    /**
     * @throws ExceptionInterface When the handler encounters an error processing the notification
     */
    public function handle(Notification $notification): void;
}
