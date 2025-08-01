<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Schema\Request;

use Mcp\Exception\InvalidArgumentException;
use Mcp\Schema\Enum\LoggingLevel;
use Mcp\Schema\JsonRpc\Request;

/**
 * A request from the client to the server, to enable or adjust logging.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class SetLogLevelRequest extends Request
{
    /**
     * @param LoggingLevel          $level The level of logging that the client wants to receive from the server. The server
     *                                     should send all logs at this level and higher (i.e., more severe) to the client as
     *                                     notifications/message.
     * @param ?array<string, mixed> $_meta optional metadata to include in the request
     */
    public function __construct(
        string|int $id,
        public readonly LoggingLevel $level,
        public readonly ?array $_meta = null,
    ) {
        $params = [
            'level' => $level->value,
        ];

        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        parent::__construct($id, 'logging/setLevel', $params);
    }

    public static function fromRequest(Request $request): self
    {
        if ('logging/setLevel' !== $request->method) {
            throw new InvalidArgumentException('Request is not a logging/setLevel request');
        }

        $params = $request->params;

        if (!isset($params['level']) || !\is_string($params['level']) || empty($params['level'])) {
            throw new InvalidArgumentException('Missing or invalid "level" parameter for logging/setLevel.');
        }

        return new self($request->id, LoggingLevel::from($params['level']), $params['_meta'] ?? null);
    }
}
