<?php
/**
 * 请求淘宝API接口方法，默认返回 json 数据
 * @global type $TAOBAO_TOP_SESSION     用户top_session
 * @global type $TAOBAO_APPKEY          应用APPKEY
 * @global type $TAOBAO_NICK            用户名
 * @global type $DB                     
 * @param type $method                  请求接口名
 * @param type $fields                  需要返回的字段
 * @param type $more_params             传递给接口的参数
 * @param type $format                  可选，指定响应格式。默认xml,目前支持格式为xml,json
 * @param type $auth                    是否是已经授权
 * @param type $retry                   失败后重试次数
 * @param type $max_retry               最大重试次数
 * @return boolean
 */

function query_taobao_api($method, $fields, $more_params = array(), $format = 'json', $auth = 1, $retry = 0, $max_retry = 20) {
        global $TAOBAO_TOP_SESSION, $TAOBAO_APPKEY, $TAOBAO_NICK; 
        // 参数数组
        $params = array();
        $params['app_key'] = $TAOBAO_APPKEY;
        $params['method'] = $method;
        if ($auth) {
                $params['session'] = $_SESSION['top_session'];
                if ($TAOBAO_TOP_SESSION AND !$_SESSION['top_session']) {
                        $params['session'] = $TAOBAO_TOP_SESSION;
                } 
        } 
        $params['format'] = $format;
        $params['v'] = '2.0';
        $params['sign_method'] = 'md5';
        $params['timestamp'] = date('Y-m-d H:i:s');
        if ($fields) {
                $params['fields'] = $fields;
        } 
        // 合并参数
        $params = $params + $more_params;
        $paramsArr = $params;
        if ($TAOBAO_NICK)$paramsArr['nick'] = $TAOBAO_NICK; 
        // 生成签名
        $sign = createSign($params); 
        // 组织参数
        $strParam = createStrParam($params);
        $strParam .= 'sign=' . $sign; 
        // 访问服务
        // 正式环境：http://gw.api.taobao.com/router/rest
        // 沙箱环境：http://gw.api.tbsandbox.com/router/rest
        if (TAOBAO_SANDBOX == 1 OR PROMOTION_TAOBAO_SANDBOX == 1) {
                $url = 'http://gw.api.tbsandbox.com/router/rest'; //沙箱环境调用地址
        } else {
                $url = 'http://gw.api.taobao.com/router/rest'; //正式环境调用地址     
        } 

        $curl = new CURL();
        $results = $curl->post($url, $params + array('sign' => $sign), 0); 
        // echo $results;
        $info = $curl->getinfo(); 
        // print_rr($info);
        // 网络异常
        if ($info['http_code'] != '200') {
                return false;
        } 
        // exit;
        if ($format == 'json') {
                // 将10位以上的整数转为字符串，避免整数长度过长而被转义
                $results = ereg_replace("\"([^\"]+)\":([0-9]{10,})", "\"\\1\":\"\\2\"", $results);
                $data = json_decode($results);
        } else {
                try {
                        $data = @new SimpleXMLElement($results);
                } 
                catch (Exception $e) {
                } 
        } 
        // 记录错误日志
        if ($data->error_response || $data->code) {
                // 自动重试
                if (ereg("This ban will last for 1 more seconds", trim($data->sub_msg)) || ereg("This ban will last for 1 more seconds", trim($data->error_response->sub_msg))) {
                        $retry++;
                        if ($retry > $max_retry) {
                                return $data;
                        } 
                        usleep(100000); // 0.1s
                        return query_taobao_api($method, $fields, $more_params, $format, $auth, $retry, $max_retry);
                } 
                
                if ($data->error_response->code == 27 || $data->code == 27) { // top session  无效
                        global $DB;
                        if ($TAOBAO_NICK) {
                                $DB->query("UPDATE LOW_PRIORITY user_service SET
                                                top_session_status=0
                                                WHERE nick='" . addslashes($TAOBAO_NICK) . "'
                                                ");
                        } else {
                                $DB->query("UPDATE LOW_PRIORITY user_service SET
                                                top_session_status=0                                               
                                                WHERE top_session='" . addslashes($params['session']) . "'
                                                ");
                        }       
                }
                if($data->error_response){
        		$error_sub_code = trim($data->error_response->code) ."/" . trim($data->error_response->sub_code);
        		$error_sub_msg = trim($data->error_response->msg) . "/" . trim($data->error_response->sub_msg); 
		}else{
			$error_sub_code = trim($data->code) . "/" . trim($data->sub_code);
        		$error_sub_msg = trim($data->msg) . "/" .trim($data->sub_msg);
		}
        	$error_msg = array();
        	$error_msg['code'] = trim($error_sub_code, "/");
        	$error_msg['msg'] = trim($error_sub_msg, "/");
		$data->error  = !empty($error_msg['msg'])?$error_msg['msg']:$error_msg['code'];
                $data->error_code = $error_msg['code'];
                log_taobao_error($method, $data, $paramsArr); 
        }
        if(preg_match("/taobao.trade/", $method)){//订单类TOP调用护城河日志
                if("taobao.trade.fullinfo.get" == trim($method) ){
                        $params = array('url'=> PROJECT_HOME_URL.$_SERVER['REQUEST_URI'],'tradeIds'=>$more_params['tid'],'operation'=>'获取订单详情');
                        Core::getInstance()->loadModel("common")->addOseTask("order",$params , true);
                }else{
                        $params = array('url'=>PROJECT_HOME_URL.$_SERVER['REQUEST_URI']);
                        Core::getInstance()->loadModel("common")->addOseTask("top",$params);
                }
        }
        return $data;

}