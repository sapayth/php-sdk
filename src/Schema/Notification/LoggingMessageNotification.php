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
use Mcp\Schema\Enum\LoggingLevel;
use Mcp\Schema\JsonRpc\Notification;

/**
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class LoggingMessageNotification extends Notification
{
    /**
     * @param LoggingLevel          $level  the severity of this log message
     * @param mixed                 $data   The data to be logged, such as a string message or an object. Any JSON serializable type is allowed here.
     * @param string                $logger an optional name of the logger issuing this message
     * @param ?array<string, mixed> $_meta  optional metadata to include in the notification
     */
    public function __construct(
        public readonly LoggingLevel $level,
        public readonly mixed $data,
        public readonly ?string $logger = null,
        public readonly ?array $_meta = null,
    ) {
        $params = [
            'level' => $level->value,
            'data' => \is_string($data) ? $data : json_encode($data),
        ];

        if (null !== $logger) {
            $params['logger'] = $logger;
        }

        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct('notifications/message', $params);
    }

    public static function fromNotification(Notification $notification): self
    {
        if ('notifications/message' !== $notification->method) {
            throw new InvalidArgumentException('Notification is not a notifications/message notification');
        }

        $params = $notification->params;

        if (!isset($params['level']) || !\is_string($params['level'])) {
            throw new InvalidArgumentException('Missing or invalid level parameter for notifications/message notification');
        }

        if (!isset($params['data'])) {
            throw new InvalidArgumentException('Missing data parameter for notifications/message notification');
        }

        $level = LoggingLevel::from($params['level']);

        return new self($level, $params['data'], $params['logger'] ?? null, $params['_meta'] ?? null);
    }
}
