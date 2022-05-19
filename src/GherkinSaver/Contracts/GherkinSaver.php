<?php

namespace MilesChou\Behat\Extension\GherkinSaver\Contracts;

use Behat\Gherkin\Node\FeatureNode;

interface GherkinSaver
{
    /**
     * Save gherkin into file or remote storage
     *
     * @return void
     */
    public function save(FeatureNode $feature);
}
