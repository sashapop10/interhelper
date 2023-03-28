<?php
    // include 'connection.php'; 
    // include 'func.php';
    // global $connection;
    // $sql = "SELECT columns, owner_id FROM crm";
    // $rows = attach_sql($connection, $sql, 'query');
    // foreach($rows as $row){
    //     $replaced_names = array();
    //     $info = json_decode($row['columns'], JSON_UNESCAPED_UNICODE);
    //     $owner_id = $row['owner_id'];
    //     foreach($info as $table_name => $table){
    //         foreach($table["table_columns"] as $column_name => $column){
    //             if(strpos($column_name, 'helper_') !== false) continue;
    //             $new_name = uniqid();
    //             if(!array_key_exists($column_name, $replaced_names)) $replaced_names[$column_name] = $new_name;
    //             else $new_name = $replaced_names[$column_name];
    //             $info[$table_name]["table_columns"][$new_name] = $column;
    //             $info[$table_name]["table_columns"][$new_name]["helper_column_name"] = $column_name;
    //             unset($info[$table_name]["table_columns"][$column_name]);
    //         }
    //     }
    //     $info = json_encode($info, JSON_UNESCAPED_UNICODE);
    //     $sql = "SELECT info, id FROM crm_items WHERE owner_id = '$owner_id'";
    //     $items = attach_sql($connection, $sql, 'query');
    //     foreach($items as $item){
    //         $id = $item['id'];
    //         $item_info = json_decode($item['info'], JSON_UNESCAPED_UNICODE);
    //         foreach($replaced_names as $key => $value){
    //             if(array_key_exists($key, $item_info)){
    //                 $item_info[$value] = $item_info[$key];
    //                 unset($item_info[$key]);
    //             }
    //         }
    //         $item_info = json_encode($item_info, JSON_UNESCAPED_UNICODE);
    //         $sql = "UPDATE crm_items SET info = '$item_info' WHERE id = '$id'";
    //         $connection->query($sql);
    //     }
    //     $sql = "UPDATE crm SET columns = '$info' WHERE owner_id = '$owner_id'";
    //     $connection->query($sql);
    // }
    // mysqli_close($connection);
?>