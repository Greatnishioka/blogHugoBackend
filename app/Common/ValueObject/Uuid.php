<?php
namespace App\Domain\Common\ValueObject;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class Uuid
{
    private string $value;

    private function __construct(string $value)
    {
        if (!self::isValidUuid($value)) {
            throw new InvalidArgumentException("Invalid uuid: {$value}");
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self((string) Str::uuid());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private static function isValidUuid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/', $value);
    }
}