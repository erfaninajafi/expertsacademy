<?php
declare(strict_types=1);

/**
 * Renderer benchmark. Usage: php bin/benchmark.php [iterations]
 *
 * Measures registry loading and PHP rendering for the sample page. The output is
 * a JSON report with average and p95 timings; it is a measurement, not a test.
 */

ini_set('serialize_precision', '-1');

require dirname(__DIR__) . '/bootstrap.php';

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Render\Renderer;

$iterations = max(5, min(500, (int) ($argv[1] ?? 50)));
$renderer = new Renderer();
$document = Samples::page('en');
$catalog = ComponentRegistry::catalog();
$variants = array_sum(array_map(static fn (array $item): int => count($item['variants']), $catalog));

/** @return array{average:float,p95:float,min:float,max:float,runs:int} */
function measure(callable $work, int $iterations): array
{
    for ($warmup = 0; $warmup < 3; $warmup++) {
        $work();
    }
    $samples = [];
    for ($run = 0; $run < $iterations; $run++) {
        $start = hrtime(true);
        $work();
        $samples[] = (hrtime(true) - $start) / 1_000_000;
    }
    sort($samples);
    $p95 = $samples[(int) floor((count($samples) - 1) * 0.95)];
    return [
        'average' => round(array_sum($samples) / count($samples), 2),
        'p95' => round($p95, 2),
        'min' => round($samples[0], 2),
        'max' => round($samples[count($samples) - 1], 2),
        'runs' => count($samples),
    ];
}

$fragment = measure(static fn (): string => $renderer->fragment($document), $iterations);
$full = measure(
    static fn (): string => $renderer->document($document, '/asset_site/css/cms-pages.css', '/asset_site/js/cms-runtime.js'),
    $iterations
);
$catalogLoad = measure(static fn (): array => ComponentRegistry::catalog(), max(5, (int) ($iterations / 5)));

echo json_encode([
    'components' => count($catalog),
    'variants' => $variants,
    'sections' => count($document['sections']),
    'php' => PHP_VERSION,
    'fragment_ms' => $fragment,
    'document_ms' => $full,
    'registry_ms' => $catalogLoad,
    'memory_peak_mb' => round(memory_get_peak_usage(true) / 1048576, 1),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
