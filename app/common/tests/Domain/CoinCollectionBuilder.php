<?php declare(strict_types=1);

namespace Tests\VendingMachine\Common\Domain;

use VendingMachine\Common\Domain\CoinCollection;

final class CoinCollectionBuilder
{
    private array $coins;

    public function __construct()
    {
        $this->coins = [];
    }

    public static function empty(): CoinCollection
    {
        return new CoinCollection();
    }

    public static function aCoinCollection(): self
    {
        return new self();
    }

    public function withAnyCoin(): self
    {
        $this->coins[] = CoinBuilder::aCoin()->build();

        return $this;
    }

    public function build(): CoinCollection
    {
        return new CoinCollection(...$this->coins);
    }
}
