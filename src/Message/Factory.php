<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Message;

use Mcp\Exception\InvalidInputMessageException;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class Factory
{
    /**
     * @return iterable<Notification|Request|InvalidInputMessageException>
     *
     * @throws \JsonException When the input string is not valid JSON
     */
    public function create(string $input): iterable
    {
        $data = json_decode($input, true, flags: \JSON_THROW_ON_ERROR);

        if ('{' === $input[0]) {
            $data = [$data];
        }

        foreach ($data as $message) {
            if (!isset($message['method'])) {
                yield new InvalidInputMessageException('Invalid JSON-RPC request, missing "method".');
            } elseif (str_starts_with((string) $message['method'], 'notifications/')) {
                yield Notification::from($message);
            } else {
                yield Request::from($message);
            }
        }
    }
}
