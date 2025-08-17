<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Example\HttpUserProfileExample;

use Mcp\Capability\Prompt\Completion\ProviderInterface;

class UserIdCompletionProvider implements ProviderInterface
{
    public function getCompletions(string $currentValue): array
    {
        $availableUserIds = ['101', '102', '103'];

        return array_filter($availableUserIds, fn (string $userId) => str_contains($userId, $currentValue));
    }
}
