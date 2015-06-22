<?php
class http {
	var $prot;
	var $host;
	var $conn;
	var $cookie;
	
    function http($prot, $host) {
		$this->prot = $prot;
		$this->host = $host;
		$this->cookie = "";
		$this->conn = curl_init();
		//curl_setopt($this->conn, CURLOPT_HEADER, true);
		curl_setopt($this->conn, CURLOPT_CRLF, true);
		//curl_setopt($this->conn, CURLOPT_VERBOSE, true);
		curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->conn, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->conn, CURLOPT_HTTPHEADER, array("Accept: */*", "Accept-Language: es-cl,es;q=0.3", "Host: $host", "Connection: Keep-Alive"));
		curl_setopt ($this->conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
	}
	
	function get($route, &$headersToFill=null) {
		//echo "Petición GET a ".$route."<br/>";
		curl_setopt($this->conn, CURLOPT_URL, $this->prot."://".$this->host.$route);
		curl_setopt($this->conn, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->conn, CURLOPT_HTTPGET, true);
		$html = curl_exec($this->conn);
		$pos = strpos($html, "\r\n\r\n");
		
		if ($pos !== false) {
			$headers = explode("\r\n", substr($html, 0, $pos));
			$headersToFill = array();
			foreach($headers as $header) {
				$pos = stripos($header, ":");
				$key = substr($header, 0, $pos);
				$value = substr($header, $pos+1);
				$headersToFill[$key] = $value;
				
				if (strcasecmp($key, "Set-Cookie") == 0) {
					$this->cookie .= $value."; ";
				}
			}
			$html = substr($html, $pos+2);
		}
		
		return $html;
	}
	
	function post($route, $data, &$headersToFill=null) {
		//echo "POST data a ".$route."<br/>";
		curl_setopt($this->conn, CURLOPT_URL, $this->prot."://".$this->host.$route);
		curl_setopt($this->conn, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->conn, CURLOPT_POST, true);
		$toSend = "";
		$first = true;
		
		foreach (array_keys($data) as $key) {
            if($first)
                $first = false;
            else
                $toSend .= "&";
            $toSend .= "$key=".$data[$key];
        }
		
		curl_setopt($this->conn, CURLOPT_POSTFIELDS, $toSend);
		$html = curl_exec($this->conn);
		$pos = strpos($html, "\r\n\r\n");
		
		if ($pos !== false) {
			$headers = explode("\r\n", substr($html, 0, $pos));
			$headersToFill = array();
			foreach($headers as $header) {
				$pos = stripos($header, ":");
				$key = substr($header, 0, $pos);
				$value = substr($header, $pos+1);
				$headersToFill[$key] = $value;
				
				if (strcasecmp($key, "Set-Cookie") == 0) {
					$this->cookie .= $value."; ";
				}
			}
			$html = substr($html, $pos+2);
		}
		
		return $html;
	}
	
	function close() {
		curl_close($this->conn);
	}
	
	function add2Headers($key, $value) {
		curl_setopt($this->conn, CURLOPT_HTTPHEADER, array("$key: $value"));
	}
}
?>