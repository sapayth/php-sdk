<?php

/*
 * This file is part of the official PHP MCP SDK.
 *
 * A collaboration between Symfony and the PHP Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcp\Example\DependenciesStdioExample\Service;

interface TaskRepositoryInterface
{
    public function addTask(string $userId, string $description): array;

    public function getTasksForUser(string $userId): array;

    public function getAllTasks(): array;

    public function completeTask(int $taskId): bool;
}
