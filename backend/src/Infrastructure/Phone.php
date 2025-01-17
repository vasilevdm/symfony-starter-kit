<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Телефон
 */
#[ORM\Embeddable]
final class Phone implements ValueObject
{
    private const PHONE_NUMBER_REGEX = '/^[0-9]{11}+$/';

    #[ORM\Column]
    private readonly string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::regex($value, self::PHONE_NUMBER_REGEX, 'Укажите телефон в правильном формате');

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
