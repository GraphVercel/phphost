<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'file_name',
    'dateadded',
    'filetype',
    ];

$sIndexColumn = 'orderid';
$sTable       = db_prefix() . 'orders_image';
$where        = [];
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $sWhere);

$output   = $result['output'];
$rResult  = $result['rResult'];
$is_admin = is_admin();
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'id') {
            $_data = '<a href="' . admin_url('orders/view/' . $aRow['id']) . '">'.$aRow['id'].'</a>';
        }
        if ($aColumns[$i] == 'file_name') {
            $_data = '<img width="100" src="' . base_url('uploads/orders/' . $aRow['file_name']) . '" class="img-responsive" alt="' . html_escape($aRow['file_name']) . '">';
        }
        if ($aColumns[$i] == 'filetype') {
            $_data = ' <a href="' . admin_url('orders/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            $_data .= ' | <a href="' . admin_url('orders/view/' . $aRow['id']) . '">' . _l('view') . '</a>';
        }
        if ($aColumns[$i] == 'dateadded') {
            $_data = _d($_data);
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}