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
 * An out-of-band notification used to inform the receiver of a progress update for a long-running request.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class ProgressNotification extends Notification
{
    /**
     * @param string|int            $progressToken the progress token which was given in the initial request, used to
     *                                             associate this notification with the request that is proceeding
     * @param float                 $progress      The progress thus far. This should increase every time progress is
     *                                             made, even if the total is unknown.
     * @param ?float                $total         total number of items to process (or total progress required), if known
     * @param ?string               $message       an optional message describing the current progress
     * @param ?array<string, mixed> $_meta         optional metadata to include in the notification
     */
    public function __construct(
        public readonly string|int $progressToken,
        public readonly float $progress,
        public readonly ?float $total = null,
        public readonly ?string $message = null,
        public readonly ?array $_meta = null,
    ) {
        $params = [];
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct('notifications/progress', $params);
    }

    public static function fromNotification(Notification $notification): self
    {
        if ('notifications/progress' !== $notification->method) {
            throw new InvalidArgumentException('Notification is not a notifications/progress notification');
        }

        $params = $notification->params;

        if (!isset($params['progressToken']) || !\is_string($params['progressToken'])) {
            throw new InvalidArgumentException('Missing or invalid progressToken parameter for notifications/progress notification');
        }

        if (!isset($params['progress']) || !\is_float($params['progress'])) {
            throw new InvalidArgumentException('Missing or invalid progress parameter for notifications/progress notification');
        }

        return new self($params['progressToken'], $params['progress'], $params['total'] ?? null, $params['message'] ?? null, $params['_meta'] ?? null);
    }
}
