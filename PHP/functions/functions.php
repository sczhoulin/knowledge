<?php
/**
 * 预防数据库攻击
 */
function checkInput ($value) {
        if (get_magic_quotes_gpc) {
                $value = stripslashes($value);
        }
        if (!is_numeric($value)) { //如果不是数字，则加引号
                $value = "'".mysql_real_escape_string($value)."'";
        }
        return $value;
}

/**
 * 获取文件后缀名
 * @param type $filename        文件名字
 * @return string       返回文件后缀名
 */
function getFileExt ($filename) {
        $strs = explode('.', $filename);
        $len = count($strs);
        $extension = $strs[$len-1];
        return strtolower($extension);
}
