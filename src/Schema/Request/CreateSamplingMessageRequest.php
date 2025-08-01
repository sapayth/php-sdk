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

use Mcp\Schema\Content\SamplingMessage;
use Mcp\Schema\JsonRpc\Request;
use Mcp\Schema\ModelPreferences;

/**
 * A request from the server to sample an LLM via the client. The client has full discretion over which model to select.
 * The client should also inform the user before beginning sampling, to allow them to inspect the request (human in the
 * loop) and decide whether to approve it.
 *
 * @author Kyrian Obikwelu <koshnawaza@gmail.com>
 */
class CreateSamplingMessageRequest extends Request
{
    /**
     * @param SamplingMessage[]     $messages       the messages to send to the model
     * @param int                   $maxTokens      The maximum number of tokens to sample, as requested by the server.
     *                                              The client MAY choose to sample fewer tokens than requested.
     * @param ModelPreferences|null $preferences    The server's preferences for which model to select. The client MAY
     *                                              ignore these preferences.
     * @param string|null           $systemPrompt   An optional system prompt the server wants to use for sampling. The
     *                                              client MAY modify or omit this prompt.
     * @param string|null           $includeContext A request to include context from one or more MCP servers (including
     *                                              the caller), to be attached to the prompt. The client MAY ignore this request.
     *
     * Allowed values: "none", "thisServer", "allServers"
     * @param float|null            $temperature   The temperature to use for sampling. The client MAY ignore this request.
     * @param string[]|null         $stopSequences A list of sequences to stop sampling at. The client MAY ignore this request.
     * @param ?array<string, mixed> $metadata      Optional metadata to pass through to the LLM provider. The format of
     *                                             this metadata is provider-specific.
     * @param ?array<string, mixed> $_meta         optional metadata to include in the request
     */
    public function __construct(
        string|int $id,
        public readonly array $messages,
        public readonly int $maxTokens,
        public readonly ?ModelPreferences $preferences = null,
        public readonly ?string $systemPrompt = null,
        public readonly ?string $includeContext = null,
        public readonly ?float $temperature = null,
        public readonly ?array $stopSequences = null,
        public readonly ?array $metadata = null,
        public readonly ?array $_meta = null,
    ) {
        $params = [
            'messages' => $this->messages,
            'maxTokens' => $this->maxTokens,
        ];

        if (null !== $this->preferences) {
            $params['preferences'] = $this->preferences;
        }

        if (null !== $this->systemPrompt) {
            $params['systemPrompt'] = $this->systemPrompt;
        }

        if (null !== $this->includeContext) {
            $params['includeContext'] = $this->includeContext;
        }

        if (null !== $this->temperature) {
            $params['temperature'] = $this->temperature;
        }

        if (null !== $this->stopSequences) {
            $params['stopSequences'] = $this->stopSequences;
        }

        if (null !== $this->metadata) {
            $params['metadata'] = $this->metadata;
        }

        if (null !== $this->_meta) {
            $params['_meta'] = $this->_meta;
        }

        parent::__construct($id, 'sampling/createMessage', $params);
    }
}
