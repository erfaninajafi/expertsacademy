<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    public function testRegistryMeetsTheCurrentInventoryFloor(): void
    {
        $all = ComponentRegistry::all();
        self::assertGreaterThanOrEqual(60, count($all), 'The premium library target of 60 types is incomplete.');
        $variants = 0;
        $seen = [];
        foreach ($all as $type => $definition) {
            self::assertNotEmpty($definition['variants'], $type . ' needs variants.');
            self::assertNotSame('', trim((string) $definition['description']), $type . ' needs a description.');
            foreach ($definition['variants'] as $variant) {
                $variants++;
                $key = $type . '/' . $variant['id'];
                self::assertArrayNotHasKey($key, $seen, 'Duplicate variant ' . $key);
                $seen[$key] = true;
            }
            self::assertSame(count($definition['variants']), count(array_unique(array_column($definition['variants'], 'id'))));
        }
        self::assertGreaterThanOrEqual(200, $variants, 'The premium library target of 200 variants is incomplete.');
    }

    public function testEveryComponentHasATemplate(): void
    {
        foreach (array_keys(ComponentRegistry::all()) as $type) {
            self::assertFileExists(
                dirname(__DIR__) . '/src/Render/templates/' . $type . '.php',
                'Missing template for ' . $type
            );
        }
    }

    public function testEveryVariantDefaultValidates(): void
    {
        $validator = new Validator();
        foreach (ComponentRegistry::all() as $type => $definition) {
            foreach ($definition['variants'] as $variant) {
                $document = Samples::single($type, $variant['id']);
                self::assertSame([], $validator->errors($document), $type . '/' . $variant['id']);
            }
        }
    }

    public function testUnknownTypeReturnsNull(): void
    {
        self::assertNull(ComponentRegistry::get('page_builder_legacy'));
        self::assertSame([], ComponentRegistry::variantIds('page_builder_legacy'));
    }

    public function testRegistryNeverReferencesLegacyTemplates(): void
    {
        $files = [
            dirname(__DIR__) . '/src/Registry/ComponentRegistry.php',
            dirname(__DIR__) . '/src/Render/Renderer.php',
        ];
        foreach (glob(dirname(__DIR__) . '/src/Render/templates/*.php') ?: [] as $template) {
            $files[] = $template;
        }
        foreach ($files as $file) {
            $contents = (string) file_get_contents($file);
            self::assertStringNotContainsString('components_site', $contents, $file);
            self::assertStringNotContainsString('component_registry.php', $contents, $file);
        }
    }

    public function testFingerprintIsStableAndOpaque(): void
    {
        $first = ComponentRegistry::fingerprint();
        self::assertSame($first, ComponentRegistry::fingerprint());
        self::assertMatchesRegularExpression('/\A[0-9a-f]{16,64}\z/', $first);
    }

    public function testCatalogIsExposedWithoutTemplatesOrPaths(): void
    {
        $catalog = ComponentRegistry::catalog();
        self::assertNotEmpty($catalog);
        $encoded = json_encode($catalog);
        self::assertIsString($encoded);
        self::assertStringNotContainsString('.php', $encoded);
    }
}
