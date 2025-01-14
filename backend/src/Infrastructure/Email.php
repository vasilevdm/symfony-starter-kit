<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Email
 */
#[ORM\Embeddable]
final class Email implements ValueObject
{
    #[ORM\Column]
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::email($value);

        $this->value = $value;
    }

    /**
     * @param object $other
     */
    public function equalTo(mixed $other): bool
    {
        return $other::class === self::class && $this->value === $other->value;
    }
}
