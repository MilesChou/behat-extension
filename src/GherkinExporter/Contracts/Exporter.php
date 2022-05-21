<?php

namespace MilesChou\Behat\Extension\GherkinExporter\Contracts;

use Behat\Gherkin\Node\FeatureNode;

interface Exporter
{
    /**
     * Save gherkin into file or remote storage
     *
     * @return void
     */
    public function save(FeatureNode $feature);
}
