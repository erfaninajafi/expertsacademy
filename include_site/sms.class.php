<?php

class sendSMS{
	
	var $user="porsoo";
	var $pass="aaaa1111";
	var $txt="";
        var $tctCode="";
	var $mobile=NULL;
	var $api="http://www.niksms.com/sendByPost.php";
	var $send_number="9830006179";
	
	
	function post_request($url, $data, $referer='') {
	 
		$data = http_build_query($data);
	 
		$url = parse_url($url);
	 
		if ($url['scheme'] != 'http') { 
			die('Error: Only HTTP request are supported !');
		}
	 
		$host = $url['host'];
		$path = $url['path'];
	 
		$fp = fsockopen($host, 80, $errno, $errstr, 30);
	 
		if ($fp){
	 
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
	 
			if ($referer != '')
				fputs($fp, "Referer: $referer\r\n");
	 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);
	 
			$result = ''; 
			while(!feof($fp)) {
				$result .= fgets($fp, 128);
			}
		}
		else { 
			return array(
				'status' => 'err', 
				'error' => "$errstr ($errno)"
			);
		}
	 
		fclose($fp);
	 
		$result = explode("\r\n\r\n", $result, 2);
	 
		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';
	    $content=str_replace("\r\n"," ",$content);
        $part1=explode(',',$content);
        $part2=explode(' ',$part1[0]);
        if(count($part2)==2)
        {
            $part1=array_slice($part1,1,count($part1)-1); 
            $part2=array_slice($part2,1,count($part2)-1);   
            $part1=array_merge($part2,$part1);
        }                
        $content=implode(',',$part1);
        $content=str_replace("9 ","",$content);
        $content=str_replace(" ","",$content);
        $content=str_replace(",0","",$content);
		return array(
			'status' => 'ok',
			'header' => $header,
			'content' => $content
		);
    }

	function send()
	{
		$post_data = array(
			'login' => $this->user,
			'pass' => $this->pass,
			'msgBody' => $this->txt,
			'recNums' => $this->mobile,
			'senderNum' =>$this->send_number ,
            'send'=>'1'
		);
		 
		$result = $this->post_request($this->api, $post_data);
        return $result['content']; 
		if ($result['status'] == 'ok'){
		       	
		}
		else {
			return $result['error']; 
		}
	}
    
    function Recieve()
    {
        $post_data = array(
            'login' => $this->user,
            'pass' => $this->pass,
            'number' =>$this->send_number ,
            'send'=>'1'
        );
         
        $result = $this->post_request($this->api, $post_data);
        echo $result['content']; 
        if ($result['status'] == 'ok'){
                   
        }
        else {
            echo $result['error']; 
        }
    }
    
    function CheckStatus()
    {
        $post_data = array(
            'login' => $this->user,
            'pass' => $this->pass,
            'tctCode' =>$this->tctCode ,
            'send'=>'1'
        );
         
        $result = $this->post_request($this->api, $post_data);
        echo $result['content']; 
        if ($result['status'] == 'ok'){
                   
        }
        else {
            echo $result['error']; 
        }
    }
		
}


?>