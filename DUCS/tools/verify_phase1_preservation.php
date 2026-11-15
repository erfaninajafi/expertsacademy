<?php
/** Read-only comparison against the private pre-Phase-1 database manifest. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
$_SERVER['HTTP_HOST']='localhost';
require dirname(__DIR__,2).'/include_site/config.php';
if($dbname!=='expertsacademy_dev')throw new RuntimeException('Development database required.');
$baseline=json_decode((string)file_get_contents($argv[1]??''),true,512,JSON_THROW_ON_ERROR)['tables_manifest'];
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$db=new mysqli($dbserver,$dbusername,$dbpassword,$dbname);$db->set_charset('utf8mb4');
$db->query('SET TRANSACTION READ ONLY');$db->begin_transaction();$results=[];
foreach($baseline as $table=>$expected){
    if(!preg_match('/^[a-zA-Z0-9_]+$/D',$table))throw new RuntimeException('Invalid table name.');
    $hashes=[];
    foreach($db->query("SELECT * FROM `$table`")->fetch_all(MYSQLI_ASSOC) as $row)$hashes[]=hash('sha256',serialize($row));
    sort($hashes);$observed=['rows'=>count($hashes),'content_sha256'=>hash('sha256',implode("\n",$hashes))];
    $results[$table]=['unchanged'=>$observed===$expected]+$observed;
}
$new=[];foreach($db->query("SHOW TABLES LIKE 'tbl_studio_%'")->fetch_all(MYSQLI_NUM) as [$table])$new[$table]=(int)$db->query("SELECT COUNT(*) FROM `$table`")->fetch_row()[0];
$db->rollback();$changed=array_keys(array_filter($results,fn($r)=>!$r['unchanged']));
$result=['checked_at'=>gmdate('c'),'legacy_tables_checked'=>count($results),'changed_legacy_tables'=>$changed,'legacy_tables'=>$results,'studio_table_rows'=>$new];
echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR),"\n";exit($changed?1:0);
