<?php

declare(strict_types=1);

namespace MilesChou\Behat\Extension\GherkinSaver;

use Behat\Gherkin\Node\FeatureNode;
use MilesChou\Behat\Extension\GherkinSaver\Contracts\GherkinSaver;
use MilesChou\Behat\Extension\GherkinSaver\Utils\FileGenerator;

class GherkinFile implements GherkinSaver
{
    public function save(FeatureNode $feature): void
    {
    }
}
