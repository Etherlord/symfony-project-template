<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Symfony;

use App\Infrastructure\Http\ErrorMetadata\ErrorMetadataFactory;
use App\Infrastructure\Http\ErrorMetadata\ReflectionErrorMetadataFactory;
use App\Infrastructure\Http\Status;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ConvertToJsonResponseControllerResultListener implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ErrorMetadataFactory $errorMetadataFactory = new ReflectionErrorMetadataFactory(),
    ) {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $status = Status::OK;
        $controllerResult = $event->getControllerResult();

        if (\is_object($controllerResult)) {
            $errorMetadata = $this->errorMetadataFactory->get($controllerResult::class);

            if ($errorMetadata !== null) {
                $controllerResult = new ErrorResponse(
                    $errorMetadata->errorId,
                    $errorMetadata->title,
                );
                $status = $errorMetadata->status;
            }
        }

        $event->setResponse(
            new Response(
                $this->serializer->serialize($controllerResult, 'json'),
                $status->value,
                ['Content-type' => 'application/json'],
            ),
        );
    }
}
