<?php

namespace App\Factory;

use App\Entity\Leave;
use App\Repository\LeaveRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Leave>
 *
 * @method        Leave|Proxy                     create(array|callable $attributes = [])
 * @method static Leave|Proxy                     createOne(array $attributes = [])
 * @method static Leave|Proxy                     find(object|array|mixed $criteria)
 * @method static Leave|Proxy                     findOrCreate(array $attributes)
 * @method static Leave|Proxy                     first(string $sortedField = 'id')
 * @method static Leave|Proxy                     last(string $sortedField = 'id')
 * @method static Leave|Proxy                     random(array $attributes = [])
 * @method static Leave|Proxy                     randomOrCreate(array $attributes = [])
 * @method static LeaveRepository|RepositoryProxy repository()
 * @method static Leave[]|Proxy[]                 all()
 * @method static Leave[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Leave[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Leave[]|Proxy[]                 findBy(array $attributes)
 * @method static Leave[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Leave[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class LeaveFactory extends ModelFactory
{

    const TYPES = ['sick', 'parental', 'paid'];
    const STATUSES = ['pending', 'accepted', 'rejected'];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'endAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'startAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'status' => self::faker()->randomElement(self::STATUSES),
            'type' => self::faker()->randomElement(self::TYPES)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Leave $leave): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Leave::class;
    }
}
