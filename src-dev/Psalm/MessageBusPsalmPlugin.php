<?php

declare(strict_types=1);

namespace App\Psalm;

use App\Infrastructure\MessageBus\Message;
use Psalm\CodeLocation;
use Psalm\Internal\Type\Comparator\UnionTypeComparator;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterFunctionLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\Storage\FunctionLikeStorage;
use Psalm\Storage\MethodStorage;
use Psalm\Type\Atomic\TMixed;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class MessageBusPsalmPlugin implements PluginEntryPointInterface, AfterFunctionLikeAnalysisInterface
{
    #[\Override]
    public static function afterStatementAnalysis(AfterFunctionLikeAnalysisEvent $event): ?bool
    {
        $storage = $event->getFunctionlikeStorage();

        if (!$storage instanceof MethodStorage) {
            return null;
        }

        if (!self::isHandler($storage)) {
            return null;
        }

        $handlerReturnType = $storage->return_type ?? new Union([new TMixed()]);

        foreach (self::handlerMessageClasses($storage) as $messageClass) {
            $messageClassStorage = $event->getCodebase()->classlike_storage_provider->get($messageClass);

            if (!isset($messageClassStorage->template_extended_params[Message::class]['TResult'])) {
                continue;
            }

            if ($storage->is_static) {
                continue;
            }

            $contractReturnType = $messageClassStorage->template_extended_params[Message::class]['TResult'];

            if (UnionTypeComparator::isContainedBy($event->getCodebase(), $handlerReturnType, $contractReturnType)) {
                continue;
            }

            IssueBuffer::accepts(
                new MessageContractNotSatisfied(
                    sprintf(
                        'Handler %s::%s return type %s does not satisfy contract %s of %s',
                        $storage->defining_fqcln ?? '',
                        $storage->cased_name ?? '',
                        $handlerReturnType,
                        $contractReturnType,
                        $messageClass,
                    ),
                    $storage->return_type_location ?? new CodeLocation($event->getStatementsSource(), $event->getStmt()),
                ),
                $event->getStatementsSource()->getSuppressedIssues(),
            );
        }

        return null;
    }

    private static function isHandler(MethodStorage $storage): bool
    {
        foreach ($storage->attributes as $attribute) {
            if ($attribute->fq_class_name === AsMessageHandler::class) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Generator<string>
     */
    private static function handlerMessageClasses(FunctionLikeStorage $storage): \Generator
    {
        if (!isset($storage->params[0])) {
            return;
        }

        foreach ($storage->params[0]->type?->getAtomicTypes() ?? [] as $type) {
            if ($type instanceof TNamedObject) {
                yield $type->value;
            }
        }
    }

    #[\Override]
    public function __invoke(RegistrationInterface $registration, ?\SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }
}
