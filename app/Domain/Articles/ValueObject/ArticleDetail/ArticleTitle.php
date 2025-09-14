<?php
namespace App\Domain\Articles\ValueObject\ArticleDetail;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class ArticleTitle
{
    private string $value;
    private const MAX_TITLE_LENGTH = 100;

    private function __construct(string $value)
    {
        if (!self::isValidTitle($value)) {
            throw new InvalidArgumentException("Invalid title: {$value}");
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

    private static function isValidTitle(string $value): bool
    {
        $len = strlen($value);
        return !empty($value) && $len <= self::MAX_TITLE_LENGTH;
    }
}
