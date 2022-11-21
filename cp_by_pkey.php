<?php

require('utils/json_loader.php');
require('utils/CsvFileIo.php');


if (($argc != 4) && ($argc != 5)) {
    print("USAGE: " . $argv[0] . " <map-json> <parent-csv> <child-csv> [dump-path]\n");
    return 1;
}

$pkey_json=$argv[1];
$parent_csv=$argv[2];
$child_csv=$argv[3];
$dump_path = NULL;
if ($argc == 5) {
    $dump_path = $argv[4];
}

$json_array = load_json($pkey_json);

$parent_csv_obj = new CsvFileIo($parent_csv);
$child_csv_obj = new CsvFileIo($child_csv);

print("INFO: SRC LINENUM=" . $parent_csv_obj->linenum() . "\n");
print("INFO: SRC COLNUM=" . $parent_csv_obj->colnum() . "\n");
print("INFO: DST LINENUM=" . $child_csv_obj->linenum() . "\n");
print("INFO: DST COLNUM=" . $child_csv_obj->colnum() . "\n");

$start_line = $json_array["start_line"];
$parent_pkey_col = $json_array["parent_pkey_col"];
$child_fkey_col = $json_array["child_fkey_col"];
$child_dst_col = $json_array["child_dst_col"];

for ($i = $start_line; $i < $child_csv_obj->linenum(); $i++) {
    $keyword = $child_csv_obj->value($i, $child_fkey_col);
    $row_id = $parent_csv_obj->get_row($start_line, $keyword, $parent_pkey_col);
    $src_value = $parent_csv_obj->value($row_id, $json_array["parent_src_col"]);
    $child_csv_obj->set_value($i, $child_dst_col, $src_value);
}

if (is_null($dump_path)) {
    $child_csv_obj->dump();
}
else {
    $child_csv_obj->dump($dump_path);
}


?>