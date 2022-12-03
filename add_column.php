<?php
require('utils/json_loader.php');
require('utils/CsvFileIo.php');

if (($argc != 3) && ($argc != 4)) {
    print("USAGE: " . $argv[0] . " <csv> <colname> [dump-path]\n");
    return 1;
}

$src_csv=$argv[1];
$colname=$argv[2];
$dump_path = NULL;
if ($argc == 4) {
    $dump_path = $argv[3];
}
$src_csv_obj = new CsvFileIo($src_csv);
$src_csv_obj->add_col($colname);
if (is_null($dump_path)) {
    $src_csv_obj->dump();
}
else {
    $src_csv_obj->dump($dump_path);
}

?>