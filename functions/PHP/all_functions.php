<?php
/**
 * 实现中文字符串反转，包括汉字
 * @param type $str
 * @return type
 */
function reverse($str)
{
    $ret = "";	
    $len = mb_strwidth($str,"UTF-8");
    for($i=0;$i< $len;$i++)
    {
            $arr[]=mb_substr($str, $i, 1, "UTF-8");
    }
    return implode("", array_reverse($arr));
}

/**
 * 验证邮箱
 * @param type $email
 * @return type
 */
function checkEmail($email) 
{ 
    $pregEmail = "/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i"; 
    return preg_match($pregEmail,$email); 
} 
/**
 * 从url里面取出文件的扩展名
 * @param type $url
 * @return type
 */
function getExt($url)
{
    $arr = parse_url($url);
    $file = basename($arr['path']);
    $ext = explode(".", $file);
    return $ext[1];
}
/**
 * 两个文件的相对路径
 * @param type $a
 * @param type $b
 * @return type
 */
function getRelativPath($a, $b)
{
    $returnPath = array(dirname($b));
    $arrA = explode('/', $a);
    $arrB = explode("/", $returnPath[0]);
    for ($n = 1, $len = count($arrB); $n< $len; $n++) {
        if($arrA[$n] != $arrB[$n]){
            break;
        }
    }
    if($len - $n > 0){
        $returnPath = array_merge($returnPath, array_fill(1, $len - $n, '..'));
    }
    $returnPath = array_merge($returnPath, array_slice($arrA, $n));
    return implode('/', $returnPath);
}
/**
 * 遍历一个文件夹下的所有文件和子文件夹
 * @param type $dir
 * @return type
 */
function my_scandir($dir)
{
    $files = array();
    if($handle = opendir($dir)){
        while(($file = readdir($handle)) != false){
            if($file != '..' && $file != '.'){
                if(is_dir($dir. "/". $file)){
                    $files[$file] = scandir($dir. "/" .$file);
                }else{
                    $files[] = $file;
                }
            }
        }
        closedir($handle);
        return $files;
    }
}


