<?php
/** Export reviewed metadata only; no database or user content is read. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
require dirname(__DIR__,2).'/include_site/studio/bootstrap.php';
use ExpertsAcademy\Studio\{ComponentCatalog,DesignTokens,Registry};
$root=dirname(__DIR__);$definitions=array_values(array_filter((new Registry())->all(),fn($d)=>isset($d['category'])));
file_put_contents($root.'/PHASE2_COMPONENT_REGISTRY.json',json_encode(['categories'=>ComponentCatalog::categories(),'coreTypes'=>60,'coreVariants'=>240,'definitions'=>$definitions],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)."\n");
file_put_contents($root.'/PHASE2_DESIGN_TOKENS.json',json_encode(DesignTokens::defaults(),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)."\n");
$text="# Reviewed Phase 2 component library\n\nGenerated from code-owned metadata. 60 core types / 240 core variants across 20 categories; hero-editorial v2 is an additional reviewed version, not another quota type. Full schemas/defaults/presets are in `PHASE2_COMPONENT_REGISTRY.json` and are inspectable in Component Studio.\n\n";
foreach($definitions as $d){
    $text.='## '.$d['label'].' — `'.$d['type'].'` v'.$d['version']."\n\nCategory: `".$d['category']."`. Status: ".$d['status'].".\n\n".$d['description']."\n\n| Variant | Composition |\n| --- | --- |\n";
    foreach($d['variants'] as $v)$text.='| `'.$v['id'].'` | '.$v['composition']." |\n";
    $schema=(array)$d['schema'];$properties=(array)$schema['properties'];
    $text.="\nProperties: ".implode(', ',array_map(fn($k)=>'`'.$k.'`',array_keys($properties))).". Unknown properties are rejected.\n\nPresets: ".implode(', ',array_column($d['presets'],'label')).".\n\n";
    foreach((array)$d['documentation'] as $key=>$value)$text.='- **'.ucfirst($key).'**: '.$value."\n";
    $text.="\n";
}
file_put_contents($root.'/PHASE2_COMPONENTS.md',$text);echo count($definitions)." reviewed versioned definitions exported.\n";
