<?php

/**
 * 后台应答类
 * ============================================================================
 * api说明：
 * getKey()/setKey(),获取/设置密钥
 * getContent() / setContent(), 获取/设置原始内容
 * getParameter()/setParameter(),获取/设置参数值
 * getAllParameters(),获取所有参数
 * isTenpaySign(),是否平台签名,true:是 false:否
 * getDebugInfo(),获取debug信息
 * 
 * ============================================================================
 *
 */

class ClientResponseHandler  {
	
	/** MD5密钥 */
	var $key;

	/* 平台RSA公钥 */
	var $public_rsa_key;

	var $signtype;
	
	/** 应答的参数 */
	var $parameters;
	
	/** debug信息 */
	var $debugInfo;
	
	//原始内容
	var $content;
	
	function __construct() {
		$this->ClientResponseHandler();
	}
	
	function ClientResponseHandler() {
		$this->key = "";
		$this->public_rsa_key = "";
		$this->signtype = "";
		$this->parameters = array();
		$this->debugInfo = "";
		$this->content = "";
	}
		
	/**
	*获取密钥
	*/
	function getKey() {
		return $this->key;
	}
	
	/**
	*设置密钥
	*/	
	function setKey($key) {
		$this->key = $key;
	}

	/*设置平台公钥*/
	function setRSAKey($key) {
		$this->public_rsa_key = $key;
	}

	function setSignType($type) {
		$this->signtype = $type;
	}
	
	//设置原始内容
	function setContent($content) {
		$this->content = $content;
		
		libxml_disable_entity_loader(true);
		$xml = simplexml_load_string($this->content);
		$encode = $this->getXmlEncode($this->content);
		
		if($xml && $xml->children()) {
			foreach ($xml->children() as $node){
				//有子节点
				if($node->children()) {
					$k = $node->getName();
					$nodeXml = $node->asXML();
					$v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);
					
				} else {
					$k = $node->getName();
					$v = (string)$node;
				}
				
				if($encode!="" && $encode != "UTF-8") {
					$k = iconv("UTF-8", $encode, $k);
					$v = iconv("UTF-8", $encode, $v);
				}
				
				$this->setParameter($k, $v);			
			}
		}
	}
	
	//获取原始内容
	function getContent() {
		return $this->content;
	}
	
	/**
	*获取参数值
	*/	
	function getParameter($parameter) {
		return isset($this->parameters[$parameter])?$this->parameters[$parameter] : '';
	}
	
	/**
	*设置参数值
	*/	
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	*获取所有请求的参数
	*@return array
	*/
	function getAllParameters() {
		return $this->parameters;
	}	
	
	/**
	*是否平台签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
	*true:是
	*false:否
	*/	
	function isTenpaySign() {
		$swiftpassSign = strtolower($this->getParameter("sign"));
		if ($this->getParameter('sign_type') == 'MD5') {
			return $this->getMD5Sign() == $swiftpassSign;
		} else if ($this->getParameter('sign_type') == 'RSA_1_1' || $this->getParameter('sign_type') == 'RSA_1_256') {
			return $this->verifyRSASign();
		}
	}

	function getMD5Sign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		
		return strtolower(md5($signPars));
	}

	function verifyRSASign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}

		$signPars = substr($signPars, 0, strlen($signPars) - 1);
		$res = openssl_get_publickey($this->public_rsa_key);
		if ($this->getParameter('sign_type') == 'RSA_1_1') {
			$result = (bool)openssl_verify($signPars, base64_decode($this->getParameter("sign")), $res);
			openssl_free_key($res);
			return $result;
		} else if($this->getParameter('sign_type') == 'RSA_1_256') {
			$result = (bool)openssl_verify($signPars, base64_decode($this->getParameter("sign")), $res, OPENSSL_ALGO_SHA256);
			openssl_free_key($res);
			return $result;
		}
	}
	
	/**
	*获取debug信息
	*/	
	function getDebugInfo() {
		return $this->debugInfo;
	}
	
	//获取xml编码
	function getXmlEncode($xml) {
		$ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
		if($ret) {
			return strtoupper ( $arr[1] );
		} else {
			return "";
		}
	}
	
	/**
	*设置debug信息
	*/	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
	
	/**
	 * 是否财付通签名
	 * @param signParameterArray 签名的参数数组
	 * @return boolean
	 */	
	function _isTenpaySign($signParameterArray) {
	
		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}			
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug信息
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;		
		
	
	}
	
}


?>