<?php

declare(strict_types=1);

namespace MilesChou\Behat\Extension\GherkinExporter;

use Behat\Gherkin\Node\FeatureNode;
use MilesChou\Behat\Extension\GherkinExporter\Contracts\Exporter;

class GherkinFile implements Exporter
{
    public function save(FeatureNode $feature): void
    {
    }
}
