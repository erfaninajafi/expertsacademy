<?php
/** Read-only multiset comparison with a separately restored pre-Phase-2 database. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
$_SERVER['HTTP_HOST']='localhost';
require dirname(__DIR__,2).'/include_site/config.php';
if($dbname!=='expertsacademy_dev')throw new RuntimeException('Development database required.');
$baseline=$argv[1]??'';
if(!preg_match('/^expertsacademy_studio_test_phase2_preservation_[0-9]+$/D',$baseline))throw new RuntimeException('Provide a dedicated restored preservation schema.');
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$db=new mysqli($dbserver,$dbusername,$dbpassword,$dbname);$db->set_charset('utf8mb4');
$db->query('SET TRANSACTION READ ONLY');$db->begin_transaction();$results=[];
function rows(mysqli $db,string $schema,string $table):array {
    if(!preg_match('/^[a-zA-Z0-9_]+$/D',$schema.$table))throw new RuntimeException('Invalid identifier.');
    $hashes=[];foreach($db->query("SELECT * FROM `$schema`.`$table`")->fetch_all(MYSQLI_ASSOC) as $row)$hashes[]=hash('sha256',serialize($row));
    sort($hashes);return $hashes;
}
foreach($db->query("SHOW TABLES FROM `$baseline`")->fetch_all(MYSQLI_NUM) as [$table]){
    $before=rows($db,$baseline,$table);$after=rows($db,$dbname,$table);$oldCounts=array_count_values($before);$newCounts=array_count_values($after);$missing=0;
    foreach($oldCounts as $hash=>$count)$missing+=max(0,$count-($newCounts[$hash]??0));
    $legacy=!str_starts_with($table,'tbl_studio_');
    $results[$table]=['before_rows'=>count($before),'after_rows'=>count($after),'missing_original_rows'=>$missing,'unchanged'=>$before===$after,'legacy'=>$legacy,'before_sha256'=>hash('sha256',implode("\n",$before)),'after_sha256'=>hash('sha256',implode("\n",$after))];
}
$db->rollback();$failures=array_keys(array_filter($results,fn($r)=>$r['missing_original_rows']>0||($r['legacy']&&!$r['unchanged'])));
echo json_encode(['checked_at'=>gmdate('c'),'baseline_tables'=>count($results),'legacy_tables'=>count(array_filter($results,fn($r)=>$r['legacy'])),'failures'=>$failures,'tables'=>$results],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR),"\n";exit($failures?1:0);
