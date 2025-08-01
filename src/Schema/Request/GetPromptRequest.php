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
use Mcp\Schema\JsonRpc\Request;

/**
 * Used by the client to get a prompt provided by the server.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class GetPromptRequest extends Request
{
    /**
     * @param string|int                $id        the ID of the request to cancel
     * @param string                    $name      the name of the prompt to get
     * @param array<string, mixed>|null $arguments the arguments to pass to the prompt
     * @param ?array<string, mixed>     $_meta     optional metadata to include in the request
     */
    public function __construct(
        string|int $id,
        public readonly string $name,
        public readonly ?array $arguments = null,
        public readonly ?array $_meta = null,
    ) {
        $params = ['name' => $name];

        if (null !== $_meta) {
            $params['_meta'] = $_meta;
        }

        if (null !== $arguments) {
            $params['arguments'] = (object) $arguments;
        }

        parent::__construct($id, 'prompts/get', $params);
    }

    public static function fromRequest(Request $request): self
    {
        if ('prompts/get' !== $request->method) {
            throw new InvalidArgumentException('Request is not a prompts/get request');
        }

        $params = $request->params;

        if (!isset($params['name']) || !\is_string($params['name']) || empty($params['name'])) {
            throw new InvalidArgumentException('Missing or invalid "name" parameter for prompts/get.');
        }

        $arguments = $params['arguments'] ?? new \stdClass();
        if (!\is_array($arguments) && !$arguments instanceof \stdClass) {
            throw new InvalidArgumentException('Parameter "arguments" must be an object/array for prompts/get.');
        }

        return new self($request->id, $params['name'], $arguments, $params['_meta'] ?? null);
    }
}
