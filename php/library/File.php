<?php
/**
 * 处理文件类
 */

class File
{
        function importFile () {
                $file = $_FILES['file'];
                if (trim($file['type']) != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && trim($file['type']) != 'application/vnd.ms-excel' && trim($file['type']) != 'application/octet-stream') {

                        $this->importFileError('导入文件格式不对！');
                }
                $ext = getFileExt($file['name']);
                if($ext != 'csv' && $ext != 'xls' && $ext != 'xlsx'){
                        $this->importFileError('只能上传CSV或excel文件！');
                }
                $filename = $file['tmp_name'];
                if (empty($filename)) {
                        $this->importFileError('请选择要上传的文件！');
                }
                if ($ext == 'csv') {
                        $result = $this->getImportCsvFile($filename);
                } else {
                        $result = $this->getImportXlsData ($file, $ext);
                }
        }
        
        /**
         * 获取CSV文件格式的数据
         * @param type $filename        文件名
         */
        function getImportCsvFile ($filename) {
                $handle = fopen($filename, 'r');
                $result = array ();
                if ($data = fgetcsv($handle, 1000)) {
                        foreach ($data AS $k=>$v) {
                                $data[$k] = iconv('gb2312', 'utf-8', $v);
                        }
                        $result[] = $data;
                }
                fclose($handle);
                return $result;
        }
        
        /**
         * 导入文件信息出错时弹出错误提示
         * @param type $msg     错误信息
         */
        function importFileError ($msg) {
                header("Content-Type:text/html;charset=UTF-8");
                echo "<script>parent.alert($msg)</script>";
                exit;
        }
        
        
}