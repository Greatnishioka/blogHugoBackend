<?php
namespace App\Domain\Articles\ValueObject\ArticleDetail;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class ArticleNote
{
    private string $value;
    private const MAX_NOTE_LENGTH = 1000;

    private function __construct(string $value)
    {
        if (!self::isValidNote($value)) {
            throw new InvalidArgumentException("Invalid note: {$value}");
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

    private static function isValidNote(string $value): bool
    {
        $len = strlen($value);
        return !empty($value) && $len <= self::MAX_NOTE_LENGTH;
    }
}
