<?php

declare(strict_types=1);

namespace Tests\Unit\GherkinSaver;

use Behat\Gherkin\Gherkin as GherkinParser;
use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Loader\GherkinFileLoader;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Parser;
use Illuminate\Filesystem\Filesystem;
use MilesChou\Behat\Extension\GherkinSaver\GherkinFile;
use Tests\TestCase;

class GherkinFileTest extends TestCase
{
    /**
     * @var GherkinParser
     */
    private $gherkin;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var GherkinFile
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new GherkinFile();

        $this->filesystem = new Filesystem();

        $i18n = include dirname(__DIR__, 3) . '/vendor/behat/gherkin/i18n.php';

        $this->gherkin = new GherkinParser();
        $this->gherkin->addLoader(new GherkinFileLoader(new Parser(new Lexer(new ArrayKeywords($i18n)))));
    }

    protected function tearDown(): void
    {
        $this->gherkin = null;
        $this->filesystem = null;
        $this->target = null;

        parent::tearDown();
    }

    protected function loadFeature(string $name): FeatureNode
    {
        return $this->gherkin->load($this->buildPath($name))[0];
    }

    protected function buildPath(string $name): string
    {
        return __DIR__ . "/Fixtures/$name";
    }

    public function basicCase(): iterable
    {
        yield 'test_empty_feature' => ['test_empty_feature.feature'];
        yield 'test_empty_scenario' => ['test_empty_scenario.feature'];
        yield 'test_tables' => ['test_tables.feature'];
        yield 'test_tags' => ['test_tags.feature'];
    }

    public function behatReferenceCase(): iterable
    {
        yield 'behat_quick_start' => ['behat_quick_start.feature'];
    }

    public function gherkinReferenceCase(): iterable
    {
        yield 'gherkin_reference_example' => ['gherkin_reference_example.feature'];
        yield 'gherkin_reference_feature' => ['gherkin_reference_feature.feature'];
        // Not support Rule
        // yield 'gherkin_reference_rule' => ['gherkin_reference_rule.feature'];
        yield 'gherkin_reference_background' => ['gherkin_reference_background.feature'];
        // Not support Rule
        // yield 'gherkin_reference_background_rule' => ['gherkin_reference_background_rule.feature'];
        yield 'gherkin_reference_scenario_outline' => ['gherkin_reference_scenario_outline.feature'];
    }

    /**
     * @test
     * @dataProvider basicCase
     * @dataProvider behatReferenceCase
     * @dataProvider gherkinReferenceCase
     */
    public function featureFileWillBeSameWithParsedString($file): void
    {
        $this->assertSame(
            $this->filesystem->get($this->buildPath($file)),
            $this->target->generate($this->loadFeature($file))
        );
    }
}
