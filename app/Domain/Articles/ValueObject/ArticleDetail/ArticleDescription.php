<?php
namespace App\Domain\Articles\ValueObject\ArticleDetail;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class ArticleDescription
{
    private string $value;
    private const MAX_DESCRIPTION_LENGTH = 1000;

    private function __construct(string $value)
    {
        if (!self::isValidDescription($value)) {
            throw new InvalidArgumentException("Invalid description: {$value}");
        }
        $this->value = $value;
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

    private static function isValidDescription(string $value): bool
    {
        $len = strlen($value);
        return !empty($value) && $len <= self::MAX_DESCRIPTION_LENGTH;
    }
}
