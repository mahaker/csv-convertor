<?php

Class CsvRelation
{
    private $relation_definitions;
    private $map_ids = array();
    private $objs;
    function __construct($relation_definitions, $objs)
    {
        $this->objs = $objs;
        $this->relation_definition = $relation_definitions;
        foreach ($relation_definitions as $relation) {
            $key = $relation["parent"] . "." . $relation["child"];
            $value = [
                "p"=> $relation["parent_colnames"]
            ];
            $this->map_ids[$key] = $value;
        }
    }
    private function row($parent_row, $path)
    {
        $path_array = explode('.', $path);
        $parent = $path_array[0];
        $num = count($path_array);
        $name = $path_array[$num - 1];
        $last_inx = $num - 2;
        for ($i = 1; $i < $num - 1; $i++) {
            $parent = $path_array[$i - 1];
            $child = $path_array[$i];
            $key = $parent . "." . $child;
            $columns = $this->objs[$parent]->get_colinx_array($this->map_ids[$key]["p"]);
            $parent_value = $this->objs[$parent]->get_pkeys($parent_row, $columns);
            $child_row = $this->objs[$child]->get_value_by_pkey_with_cache($parent_value);
            if ($i == $last_inx) {
                return $child_row;
            }
        }
        return null;
    }

    public function value($parent_row, $path)
    {
        $tmp = explode('.', $path);
        $target = $tmp[count($tmp) - 2];
        $name = $tmp[count($tmp) - 1];
        $row = $this->row($parent_row, $path);
        if ($row) {
            $value = $this->objs[$target]->value($row, $this->objs[$target]->colinx($name));
            #printf("%s.%s=%s\n", $path, $name, $value);
            return $value;
        }
        return NULL;
    }
    public function set_value($parent_row, $path, $value)
    {
        $tmp = explode('.', $path);
        $target = $tmp[count($tmp) - 2];
        $name = $tmp[count($tmp) - 1];
        $row = $this->row($parent_row, $path);
        if ($row) {
            $this->objs[$target]->set_value($row, $this->objs[$target]->colinx($name), $value);
            #printf("%s.%s=%s\n", $path, $name, $value);
            return $value;
        }
        return NULL;
    }

}

?>