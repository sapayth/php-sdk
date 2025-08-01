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
 * This notification can be sent by either side to indicate that it is cancelling a previously-issued request.
 *
 * The request SHOULD still be in-flight, but due to communication latency, it is always possible that this notification MAY arrive after the request has already finished.
 *
 * This notification indicates that the result will be unused, so any associated processing SHOULD cease.
 *
 * A client MUST NOT attempt to cancel its `initialize` request.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class CancelledNotification extends Notification
{
    /**
     * @param string                $requestId The ID of the request that is being cancelled. This MUST correspond to the ID of a request previously issued in the same direction.
     * @param ?string               $reason    An optional string describing the reason for the cancellation. This MAY be logged or presented to the user.
     * @param ?array<string, mixed> $_meta     additional metadata about the notification
     */
    public function __construct(
        public readonly string $requestId,
        public readonly ?string $reason = null,
        public readonly ?array $_meta = null,
    ) {
        $params = [
            'requestId' => $this->requestId,
        ];
        if (null !== $this->reason) {
            $params['reason'] = $this->reason;
        }
        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct('notifications/cancelled', $params);
    }

    public static function fromNotification(Notification $notification): self
    {
        if ('notifications/cancelled' !== $notification->method) {
            throw new InvalidArgumentException('Notification is not a notifications/cancelled notification');
        }

        $params = $notification->params;

        if (!isset($params['requestId']) || !\is_string($params['requestId'])) {
            throw new InvalidArgumentException('Missing or invalid requestId parameter for notifications/cancelled notification');
        }

        return new self($params['requestId'], $params['reason'] ?? null, $params['_meta'] ?? null);
    }
}
