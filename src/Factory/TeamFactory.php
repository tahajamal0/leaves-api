<?php

namespace App\Factory;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Team>
 *
 * @method        Team|Proxy                     create(array|callable $attributes = [])
 * @method static Team|Proxy                     createOne(array $attributes = [])
 * @method static Team|Proxy                     find(object|array|mixed $criteria)
 * @method static Team|Proxy                     findOrCreate(array $attributes)
 * @method static Team|Proxy                     first(string $sortedField = 'id')
 * @method static Team|Proxy                     last(string $sortedField = 'id')
 * @method static Team|Proxy                     random(array $attributes = [])
 * @method static Team|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TeamRepository|RepositoryProxy repository()
 * @method static Team[]|Proxy[]                 all()
 * @method static Team[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Team[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Team[]|Proxy[]                 findBy(array $attributes)
 * @method static Team[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Team[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TeamFactory extends ModelFactory
{

    const TEAM_NAMES = ['IT SUPPORT', 'DEV', 'DATA', 'SECURITY', 'RH'];

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
            'description' => self::faker()->text(255),
            'name' => self::faker()->randomElement(self::TEAM_NAMES)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Team $team): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Team::class;
    }
}
