<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;

class Params
{
    /**
     * 複合式order, 合併order and sort
     * 轉成orderBy所需參數
     *
     * @param array $order 排序方式
     * @param array $sort 排序欄位
     *
     * example:
     * [
     *   'id' => 'desc',
     *   'name' => 'asc'
     * ]
     * @return array
     */
    public static function getMultiOrders($sort, $order)
    {
        $result = [];

        if (!empty($sort) && !empty($order)) {
            foreach ($sort as $key => $data) {
                if (isset($order[$key]) && ($order[$key] == 'asc' || $order[$key] == 'desc')) {
                    $result[$data] = $order[$key];
                }
            }
        }

        return $result;
    }

    /**
     * 檢查欄位是否存在
     *
     * @param class $tableClass
     * @param array $field
     *
     * @return array || string '*'
     */
    public static function checkField($tableClass, $field)
    {
        $result = [];

        $allField = $tableClass->allField();

        foreach ($field as $value) {
            if (in_array($value, $allField)) {
                $result[] = $value;
            }
        }

        if (empty($result)) {
            $result = '*';
        }

        return $result;
    }
}