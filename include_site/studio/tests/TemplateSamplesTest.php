<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Contract\Validator;
use PHPUnit\Framework\TestCase;

final class TemplateSamplesTest extends TestCase
{
    public function testEveryStarterTemplateIsAValidDistinctDocument(): void
    {
        $templates = Samples::templates();
        self::assertGreaterThanOrEqual(6, count($templates), 'The template library needs a real starting set.');

        $validator = new Validator();
        $ids = [];
        $names = [];
        foreach ($templates as $template) {
            self::assertMatchesRegularExpression(
                '/\A[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/',
                $template['id']
            );
            self::assertArrayNotHasKey($template['id'], $ids, 'Template ids must be unique.');
            $ids[$template['id']] = true;
            self::assertArrayNotHasKey($template['name'], $names, 'Template names must be unique.');
            $names[$template['name']] = true;

            self::assertNotSame('', $template['description']);
            self::assertNotSame('', $template['category']);
            self::assertNotEmpty($template['document']['sections'], $template['name'] . ' needs sections.');

            $validator->assert($template['document']);
            $types = [];
            foreach ($template['document']['sections'] as $section) {
                foreach ($section['components'] as $component) {
                    $types[] = $component['type'];
                }
            }
            self::assertNotEmpty($types, $template['name'] . ' needs components.');
        }
    }
}
