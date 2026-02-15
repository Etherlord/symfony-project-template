<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use CuyZ\Valinor\Mapper\Object\Constructor;
use Ramsey\Uuid\UuidInterface;

final readonly class Uuid implements \JsonSerializable, \Stringable
{
    /**
     * @param non-empty-string $string
     */
    private function __construct(
        private string $string,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    #[Constructor]
    public static function fromString(string $uuid): self
    {
        if (isUuid($uuid)) {
            return new self($uuid);
        }

        throw new \InvalidArgumentException(\sprintf('Expected a valid UUID, got %s.', $uuid));
    }

    public static function fromRamsey(UuidInterface $uuid): self
    {
        return new self($uuid->toString());
    }

    /**
     * @return non-empty-string
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->string;
    }

    /**
     * @return non-empty-string
     */
    #[\Override]
    public function jsonSerialize(): string
    {
        return $this->string;
    }

    /**
     * @return array{non-empty-string}
     */
    public function __serialize(): array
    {
        return [$this->string];
    }

    /**
     * @param array{non-empty-string} $data
     */
    public function __unserialize(array $data): void
    {
        $this->string = $data[0];
    }
}
