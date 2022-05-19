<?php

declare(strict_types=1);

namespace MilesChou\Behat\Extension\GherkinSaver;

use Behat\Gherkin\Node\FeatureNode;
use MilesChou\Behat\Extension\GherkinSaver\Contracts\GherkinSaver;
use MilesChou\Behat\Extension\GherkinSaver\Utils\Generator;

class GherkinFile implements GherkinSaver
{
    public function save(FeatureNode $feature): void
    {
    }

    public static function generate(FeatureNode $feature): string
    {
        return Generator::generate($feature);
    }
}
