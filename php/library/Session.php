<?php
/**
 * 会话类
 * 
 * @author H.R.M <heizes@21cn.com> 
 */
// define('SESSION_LIFE',1440);
// define('SESSION_LIFE',1800); // 半小时
define('SESSION_LIFE', 1440); // 1小时

class Session {
        var $session_date = array();
        var $DB;

        function session($DB) {
                $this->DB = $DB;
                session_set_save_handler(
                        array(&$this, 'sess_open'),
                        array(&$this, 'sess_close'),
                        array(&$this, 'sess_read'),
                        array(&$this, 'sess_write'),
                        array(&$this, 'sess_destroy'),
                        array(&$this, 'sess_gc')
                        );
        }

        function sess_open($save_path, $session_name) {
                $this->sess_gc(0);
                return true;
        }

        function sess_close() {
                return true;
        }

        function sess_read($id) {
                $this->session_data = $this->DB->query_first("SELECT * FROM dzsofts_promotion_session.session WHERE id='$id'");
                if (!empty($this->session_data) AND $this->session_data['expiry'] > time()) {
                        return $this->session_data['value'];
                } else {
                        // $onlineuser++;
                        return "";
                }
        }

        function sess_write($id, $val) {
                $expiry = time() + SESSION_LIFE;
                $value = addslashes($val);

                $ipaddress = getip();
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                $REQUEST_URI = $_SERVER['REQUEST_URI'];

                $now = time();                                           

                $query = $this->DB->query("INSERT INTO dzsofts_promotion_session.session SET
                                                 id='$id',
                                                 expiry='$expiry',
                                                 value='$value',
                                                 ipaddress='" . addslashes($ipaddress) . "',
                                                 useragent='" . addslashes($useragent) . "',                                                 
                                                 lastactivity='$now'                                                   
                                                         ON DUPLICATE KEY UPDATE 
                                                                 id='$id',        
                                                                 expiry='$expiry',                                                        
                                                                 value='$value',
                                                                 ipaddress='" . addslashes($ipaddress) . "',
                                                                 useragent='" . addslashes($useragent) . "',                                                         
                                                                 lastactivity='$now'
                                                                ");
                //return $query;
        } 

        function sess_destroy($id) {
                return $this->DB->query("DELETE LOW_PRIORITY FROM dzsofts_promotion_session.session WHERE id='$id'");
        } 

        function sess_gc($maxlifetime) {
                //$query = $this->DB->query("DELETE LOW_PRIORITY FROM dzsofts_promotion_session.session WHERE expiry<" . time() . "");
                //return $this->DB->affected_rows();
        } 

	function clear_session() {
                $query = $this->DB->query("DELETE LOW_PRIORITY FROM dzsofts_promotion_session.session WHERE expiry<" . time() . "");	
	}
} 

?>