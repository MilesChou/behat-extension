<?php

namespace MilesChou\Behat\Extension\GherkinSaver\Utils;

use Behat\Gherkin\Node\ArgumentInterface;
use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;

class FileGenerator
{
    public static $indent = 2;

    /**
     * @param string $source
     * @param int $indent
     * @return string
     */
    private static function indent(string $source, int $indent): string
    {
        return str_repeat(' ', $indent) . trim($source);
    }

    /**
     * @param array $source
     * @param int $indent
     * @return array
     */
    private static function indentArray(array $source, int $indent): array
    {
        return array_map(static function ($line) use ($indent) {
            return rtrim(self::indent($line, $indent));
        }, $source);
    }

    /**
     * @param array $tags
     * @param int $indent
     * @return string
     */
    private static function buildTags(array $tags, int $indent): string
    {
        return str_repeat(' ', $indent) . implode(' ', array_map(function ($tag) use ($indent) {
                return '@' . $tag;
        }, $tags));
    }

    public static function generate(FeatureNode $feature): string
    {
        $content = [];

        $content = array_merge($content, self::buildFeature($feature));

        if ($feature->hasBackground()) {
            $content[] = '';
            $content = array_merge($content, self::buildBackground($feature->getBackground(), 2));
        }

        if ($feature->hasScenarios()) {
            $content[] = '';
            $content = array_merge($content, self::buildScenarios($feature->getScenarios(), 2));
        }

        return implode("\n", $content) . "\n";
    }

    /**
     * @param FeatureNode $feature
     * @return string[]
     */
    public static function buildFeature(FeatureNode $feature): array
    {
        $part = [];

        if ($feature->hasTags()) {
            $part[] = self::buildTags($feature->getTags(), 0);
        }

        $part[] = "{$feature->getKeyword()}: {$feature->getTitle()}";

        if ($feature->hasDescription()) {
            $part = array_merge(
                $part,
                self::indentArray(explode("\n", $feature->getDescription()), 2)
            );
        }

        return $part;
    }

    public static function buildBackground(BackgroundNode $background, int $indent): array
    {
        $part = [
            self::indent("{$background->getKeyword()}: {$background->getTitle()}", $indent),
        ];

        if ($background->hasSteps()) {
            $part = array_merge($part, self::buildSteps($background->getSteps(), $indent + 2));
        }

        return $part;
    }

    /**
     * @param ScenarioInterface[] $scenarios
     * @param int $indent
     * @return array
     */
    public static function buildScenarios(array $scenarios, int $indent): array
    {
        $reduce = array_reduce($scenarios, function (array $carry, ScenarioInterface $scenario) use ($indent) {
            if ($scenario->hasTags()) {
                $carry[] = self::buildTags($scenario->getTags(), $indent);
            }

            foreach (self::buildScenario($scenario, $indent) as $item) {
                $carry[] = $item;
            }

            $carry[] = '';

            return $carry;
        }, []);

        array_pop($reduce);

        return $reduce;
    }

    public static function buildScenario(ScenarioInterface $scenario, int $indent): array
    {
        $part = [
            self::indent("{$scenario->getKeyword()}: {$scenario->getTitle()}", $indent),
        ];

        if ($scenario->hasSteps()) {
            $part = array_merge($part, self::buildSteps($scenario->getSteps(), $indent + 2));
        }

        if ($scenario instanceof OutlineNode && $scenario->hasExamples()) {
            $part = array_merge($part, self::buildExampleTables($scenario->getExampleTables(), $indent + 2));
        }

        return $part;
    }

    /**
     * @param StepNode[] $steps
     * @param int $indent
     * @return array
     */
    public static function buildSteps(array $steps, int $indent): array
    {
        return array_reduce($steps, function (array $carry, StepNode $step) use ($indent) {
            return array_merge($carry, self::buildStep($step, $indent));
        }, []);
    }

    /**
     * @param StepNode $step
     * @param int $indent
     * @return array
     */
    public static function buildStep(StepNode $step, int $indent): array
    {
        $part = [
            self::indent("{$step->getKeyword()} {$step->getText()}", $indent),
        ];

        if ($step->hasArguments()) {
            $part = array_merge($part, self::buildArguments($step->getArguments(), $indent + 2));
        }

        return $part;
    }

    /**
     * @param ExampleTableNode[] $exampleTables
     * @param int $indent
     * @return array
     */
    private static function buildExampleTables(array $exampleTables, int $indent): array
    {
        $part = [];

        foreach ($exampleTables as $exampleTable) {
            $part[] = '';
            $part = array_merge($part, self::buildExampleTable($exampleTable, $indent));
        }

        return $part;
    }

    private static function buildExampleTable(ExampleTableNode $exampleTable, int $indent): array
    {
        $part = [];

        if ($tags = $exampleTable->getTags()) {
            $part[] = self::buildTags($tags, $indent);
        }

        $part[] = self::indent(
            "{$exampleTable->getKeyword()}:",
            $indent
        );

        return array_merge($part, self::buildTable($exampleTable, $indent + self::$indent));
    }

    /**
     * @param ArgumentInterface[] $arguments
     * @param int $indent
     * @return array
     */
    public static function buildArguments(array $arguments, int $indent): array
    {
        return array_reduce($arguments, function (array $carry, ArgumentInterface $argument) use ($indent) {
            return array_merge($carry, self::buildArgument($argument, $indent));
        }, []);
    }

    /**
     * @param ArgumentInterface $argument
     * @param int $indent
     * @return array
     */
    public static function buildArgument(ArgumentInterface $argument, int $indent): array
    {
        if ($argument instanceof PyStringNode) {
            return self::buildPyString($argument, $indent);
        } elseif ($argument instanceof TableNode) {
            return self::buildTable($argument, $indent);
        } else {
            throw new \OutOfRangeException('Out of range argument type: ' . $argument->getNodeType());
        }
    }

    /**
     * @param PyStringNode $node
     * @param int $indent
     * @return array
     */
    public static function buildPyString(PyStringNode $node, int $indent): array
    {
        $part = array_map(function (string $str) use ($indent) {
            return self::indent($str, $indent);
        }, $node->getStrings());

        array_unshift($part, self::indent('"""', $indent));
        $part[] = self::indent('"""', $indent);

        return $part;
    }

    private static function buildTable(TableNode $table, int $indent): array
    {
        $part = [];

        foreach ($table->getRows() as $key => $_) {
            $part[] = self::indent($table->getRowAsString($key), $indent);
        }

        return $part;
    }
}
