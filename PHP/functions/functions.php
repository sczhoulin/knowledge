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