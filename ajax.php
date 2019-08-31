<?php

	error_reporting (E_ALL ^ E_NOTICE);

	/* *****************************************************************************************************************
	 * Email validation
	 */

	// check form data
	$fname = remove_HTML( $_POST['name'] );
	$femail = remove_HTML( $_POST['email'] );
	$fweb = remove_HTML( $_POST['web'] );
	$ftext = remove_HTML( $_POST['text'] );
	$faction = remove_HTML( $_POST['action'] );

	$data = array();


	// Check email
	if ( 'contactForm' == $faction ) {
		$data['data'] = array(
			"name" => $fname,
			"email" => $femail,
			"web" => $fweb,
			"text" => $ftext,
			"action" => $faction
		);

		if ( empty($fname) || empty($femail) || empty($ftext) ||
		     !preg_match('|^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$|i', $femail) ) {
			$data['message'] = 'error';
		}
		else {
			$faddress = "hello@ishyoboy.com";
			$fsubject = "Message from Qusq";
			$fmailcontent = "Name: ".$fname."<br><br>"
							."Email: ".$femail."<br><br>"
							."Web: ".$fweb."<br><br>"
							."Message: ".$ftext;

			// To send HTML mail, the Content-type header must be set
			$fheaders  = 'MIME-Version: 1.0' . "\r\n";
			$fheaders .= 'Content-type: text/html; charset=utf-8' . "\r\n";

			// Additional headers
			$fheaders .= 'To: IshYoBoy <' . $faddress . '>' . "\r\n";
			//$fheaders .= 'From: <' . $femail . '>' . "\r\n";

			@ $email_sent = mail( $faddress, $fsubject, $fmailcontent, $fheaders );

			if ( $email_sent ) {
				$data['message'] = 'success';
			}
			else {
				$data['message'] = 'error';
			}
		}

	} else {

		$data['message'] = 'empty';

	}

	$json = json_encode($data);
	echo $json;




/* *********************************************************************************************************************
* Remove HTML from string
*/
function remove_HTML($s , $keep = '' , $expand = 'script|style|noframes|select|option'){
	/**///prep the string
	$s = ' ' . $s;

	/**///initialize keep tag logic
	if(strlen($keep) > 0){
		$k = explode('|',$keep);
		for($i=0;$i<count($k);$i++){
			$s = str_replace('<' . $k[$i],'[{(' . $k[$i],$s);
			$s = str_replace('</' . $k[$i],'[{(/' . $k[$i],$s);
		}
	}

	//begin removal
	/**///remove comment blocks
	while(stripos($s,'<!--') > 0){
		$pos[1] = stripos($s,'<!--');
		$pos[2] = stripos($s,'-->', $pos[1]);
		$len[1] = $pos[2] - $pos[1] + 3;
		$x = substr($s,$pos[1],$len[1]);
		$s = str_replace($x,'',$s);
	}

	/**///remove tags with content between them
	if(strlen($expand) > 0){
		$e = explode('|',$expand);
		for($i=0;$i<count($e);$i++){
			while(stripos($s,'<' . $e[$i]) > 0){
				$len[1] = strlen('<' . $e[$i]);
				$pos[1] = stripos($s,'<' . $e[$i]);
				$pos[2] = stripos($s,$e[$i] . '>', $pos[1] + $len[1]);
				$len[2] = $pos[2] - $pos[1] + $len[1];
				$x = substr($s,$pos[1],$len[2]);
				$s = str_replace($x,'',$s);
			}
		}
	}

	/**///remove remaining tags
	while(stripos($s,'<') > 0){
		$pos[1] = stripos($s,'<');
		$pos[2] = stripos($s,'>', $pos[1]);
		$len[1] = $pos[2] - $pos[1] + 1;
		$x = substr($s,$pos[1],$len[1]);
		$s = str_replace($x,'',$s);
	}

	/**///finalize keep tag
	for($i=0;$i<count($k);$i++){
		$s = str_replace('[{(' . $k[$i],'<' . $k[$i],$s);
		$s = str_replace('[{(/' . $k[$i],'</' . $k[$i],$s);
	}

	return trim($s);
}

?>