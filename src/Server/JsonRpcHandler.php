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
use Mcp\Exception\HandlerNotFoundException;
use Mcp\Exception\InvalidInputMessageException;
use Mcp\Exception\NotFoundExceptionInterface;
use Mcp\Message\Error;
use Mcp\Message\Factory;
use Mcp\Message\Notification;
use Mcp\Message\Request;
use Mcp\Message\Response;
use Psr\Log\LoggerInterface;

/**
 * @final
 *
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
class JsonRpcHandler
{
    /**
     * @var array<int, RequestHandlerInterface>
     */
    private readonly array $requestHandlers;

    /**
     * @var array<int, NotificationHandlerInterface>
     */
    private readonly array $notificationHandlers;

    /**
     * @param iterable<RequestHandlerInterface>      $requestHandlers
     * @param iterable<NotificationHandlerInterface> $notificationHandlers
     */
    public function __construct(
        private readonly Factory $messageFactory,
        iterable $requestHandlers,
        iterable $notificationHandlers,
        private readonly LoggerInterface $logger,
    ) {
        $this->requestHandlers = $requestHandlers instanceof \Traversable ? iterator_to_array($requestHandlers) : $requestHandlers;
        $this->notificationHandlers = $notificationHandlers instanceof \Traversable ? iterator_to_array($notificationHandlers) : $notificationHandlers;
    }

    /**
     * @return iterable<string|null>
     *
     * @throws ExceptionInterface When a handler throws an exception during message processing
     * @throws \JsonException     When JSON encoding of the response fails
     */
    public function process(string $input): iterable
    {
        $this->logger->info('Received message to process', ['message' => $input]);

        try {
            $messages = $this->messageFactory->create($input);
        } catch (\JsonException $e) {
            $this->logger->warning('Failed to decode json message', ['exception' => $e]);

            yield $this->encodeResponse(Error::parseError($e->getMessage()));

            return;
        }

        foreach ($messages as $message) {
            if ($message instanceof InvalidInputMessageException) {
                $this->logger->warning('Failed to create message', ['exception' => $message]);
                yield $this->encodeResponse(Error::invalidRequest(0, $message->getMessage()));
                continue;
            }

            $this->logger->info('Decoded incoming message', ['message' => $message]);

            try {
                yield $message instanceof Notification
                    ? $this->handleNotification($message)
                    : $this->encodeResponse($this->handleRequest($message));
            } catch (\DomainException) {
                yield null;
            } catch (NotFoundExceptionInterface $e) {
                $this->logger->warning(\sprintf('Failed to create response: %s', $e->getMessage()), ['exception' => $e]);

                yield $this->encodeResponse(Error::methodNotFound($message->id, $e->getMessage()));
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning(\sprintf('Invalid argument: %s', $e->getMessage()), ['exception' => $e]);

                yield $this->encodeResponse(Error::invalidParams($message->id, $e->getMessage()));
            } catch (\Throwable $e) {
                $this->logger->critical(\sprintf('Uncaught exception: %s', $e->getMessage()), ['exception' => $e]);

                yield $this->encodeResponse(Error::internalError($message->id, $e->getMessage()));
            }
        }
    }

    /**
     * @throws \JsonException When JSON encoding fails
     */
    private function encodeResponse(Response|Error|null $response): ?string
    {
        if (null === $response) {
            $this->logger->warning('Response is null');

            return null;
        }

        $this->logger->info('Encoding response', ['response' => $response]);

        if ($response instanceof Response && [] === $response->result) {
            return json_encode($response, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT);
        }

        return json_encode($response, \JSON_THROW_ON_ERROR);
    }

    /**
     * @return null
     *
     * @throws ExceptionInterface When a notification handler throws an exception
     */
    private function handleNotification(Notification $notification)
    {
        $handled = false;
        foreach ($this->notificationHandlers as $handler) {
            if ($handler->supports($notification)) {
                $handler->handle($notification);
                $handled = true;
            }
        }

        if (!$handled) {
            $this->logger->warning(\sprintf('No handler found for "%s".', $notification->method), ['notification' => $notification]);
        }

        return null;
    }

    /**
     * @throws NotFoundExceptionInterface When no handler is found for the request method
     * @throws ExceptionInterface         When a request handler throws an exception
     */
    private function handleRequest(Request $request): Response|Error
    {
        foreach ($this->requestHandlers as $handler) {
            if ($handler->supports($request)) {
                return $handler->createResponse($request);
            }
        }

        throw new HandlerNotFoundException(\sprintf('No handler found for method "%s".', $request->method));
    }
}
