<?php
require("gump.class.php");
require("simple-php-captcha.php");

//Add vCube vaidator to GUMP
//Deleted: the two validators where added to GUMP
//GUMP::add_validator('blacklist', function($field, $input, $param = NULL) {return Vcube::validate_blacklist($field, $input, $param = NULL);});
//GUMP::add_validator('captcha', function($field, $input, $param = NULL) {return Vcube::validate_captcha($field, $input, $param = NULL);});
/*
 * Mapped input types:
 * input type=text/email/number
 * textarea 
 * 
 */
class Vcube extends GUMP{
	// Mail configuration
    protected static $mail_cnf = "to@mymail.com";
    protected static $site = "My site";
	protected static $thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
	
	public function fields_map(){
	$fields_map=array(
	'name'=>array(
		//Validation and Filtering options
		'validation_rules'=>'required|max_len,65|min_len,3|valid_name|blacklist',
		'filter_rules'=>'trim|sanitize_string',
		//Field options
		//If label is omitted the default is the field name with first letter capitalized.
		//If the field is 'required' the script will show an * after the label
		'label'=>'Name',
		//type: input/text' is the default value, it can be omitted 
		'type'=>'input/text', 
		//placeholder, has a default value
		'placeholder'=>'Your name',
		//class, has a default value and can be omitted
		'class'=>'form-control',
		//Specific attributes for Bootstrap Validator
		'data-minlength-error'=>'The field needs to be 3 or longer in length.',  
		'data-remote'=>'inc/verify.php',
		'data-remote-error'=>'The Name field is invalid',
	),
	'email'=>array(
		'type'=>'input/email',
		'placeholder'=>'Your email',	
		'validation_rules'=>'valid_email',
		'filter_rules'=>'trim|sanitize_email',
	),
	'object'=>array(
		'validation_rules'=>'alpha_numeric',
		'filter_rules'=>'trim|sanitize_string',
		//'data-remote'=>'inc/verify.php',
		//'data-remote-error'=>'The Object field is invalid',
	),
	'content'=>array(
		'type'=>'textarea',	
		'validation_rules'=>'max_len,600|blacklist',
		'filter_rules'=>'trim|sanitize_string',
		//'data-remote'=>'inc/verify.php', 
		//'data-remote-error'=>'Carattere o parola non validi.',
	),
	'captcha'=>array(
		//'type'=>'input/text',
		'validation_rules'=>'required|alpha_numeric|captcha',
		//'validation_rules'=>'alpha_numeric',
		'filter_rules'=>'trim',
		'data-remote'=>'inc/verify.php', 
		'data-remote-error'=>'The Captcha does not match.',
	)
	);
	
	return $fields_map;
	}
	
	public function vcube_run(){
			session_start();//need to stard a session to create a captcha

			if(!$_SESSION['captcha']['code']){
				header("Refresh:0");
				}
			else{
				$_POST['sess_cap'] = $_SESSION['captcha']['code'];	
			}
			
			$_SESSION['captcha'] = simple_php_captcha();
			
			$messages='';

			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				
				//make Tests
				if(self::tests($_POST)===true){
					
					//$gump = new GUMP(); 
					$validation_rules=self::get_validation_rules_array();
					self::validation_rules($validation_rules);
			
					$_POST = self::sanitize($_POST); //Sanitize is always better
					
					//if the form passes tests the script makes Validation
					//run GUMP
					$validated_data = GUMP::run($_POST);
					
					if($validated_data === false) {
				    	$messages=self::get_errors_array();
						//var_dump($errors);
					} else {
					//if also validation is ok do action
						//var_dump($validated_data); // validation successful
						$mail_config=self::build_email($validated_data);

						//send email
						if (mail($mail_config[0],$mail_config[1],$mail_config[2],$mail_config[3])) {
							if (!empty($thanksPage)) {
							header("Location: $thanksPage");
							exit;
							} else {
							$messages['mail_success'] = 'Message successfully sent!';
							$disable = true;
							}
						} 
						else {
							$messages['mail_error'] = 'Your mail could not be sent this time.';
						} 
					}
				}
				else{
					$messages['test_error']="No bots please!";
				}
			}
		 return $messages;
		}
	
	private function default_values($key=null){
		$default=array(
		'filter_rules'=>'trim|sanitize_string',
		'type'=>'input/text', 
		'placeholder'=>'Fill in this field',
		'class'=>'form-control'
		);
		if(!$key) return $default;
		else return $default[$key];
	}
	
	public function badwords(){
		$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript", );
	
	return $badwords;
	
	}
	
	public function get_validation_rules_array(){
		$fields=$this->fields_map();
		foreach($fields as $fname=>$fparam){
			$rules[$fname]=$fparam['validation_rules'];
		}
		return $rules;
	}
	
	public function get_filter_rules_array(){
		$fields=$this->fields_map();
		foreach($fields as $fname=>$fparam){
			$rules[$fname]=$fparam['filter_rules'];
		}
		return $rules;
	}
	
	/**
	 * This function maps the configuration array
	 * uses its option to build the fields tag
	 * in the right way to trigger BootstrapValidator
	 * and set the html with bootstrap classes
	 **/
	public function build_form($messages=null){
		if(isset($_POST)) {
			$_POST = self::sanitize($_POST);//Sanitize is always better
			$filter_rules=self::get_filter_rules_array();
			self::filter_rules($filter_rules);
			//echo "<hr>";
			//var_dump($filter_rules);
			$_POST=self::filter($_POST, $filter_rules);
			//var_dump($_POST);
			}
		$fields=$this->fields_map();
		
		//Test failure Error
		$html='';
		if(isset($messages['test_error'])) {
			$html.="<div class=\"alert alert-danger\">".$messages['test_error']."</div>";
			$disable=true;
		}
		elseif(isset($messages['mail_success'])) {
			$html.="<div class=\"alert alert-success\">".$messages['mail_success']."</div>";
			$disable=true;
		}
		elseif(isset($messages['mail_error'])) {
			$html.="<div class=\"alert alert-danger\">".$messages['mail_error']."</div>";
			$disable=true;
		}
		//$messages['mail_success']
		//Start the form
		$html.='<form action="'.basename($_SERVER['SCRIPT_FILENAME']).'" data-toggle="validator" role="form" class="form form-horizontal" id="contactus" method="post" accept-charset="utf-8">';
		
		//Parse Fields
		foreach($fields as $fname=>$fparam){
			//Label
			if(isset($fparam['label'])){
				$label=$fparam['label'];
				}
			else {
				$label=ucfirst(trim($fname));
				}
			
			if($this->is_required($fname)){
				$label.="*";
				}
			
			$html.='<div class="form-group';
			if(isset($errors[$fname])) $html.=' has-error';
			$html.='">';
			$html.=	'<label for="'.$fname.'" class="control-label col-sm-3">'.$label.'</label>
						<div class="col-sm-9">';
			//captcha image
			if($fname=='captcha'){
				$html.='<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code">';
				}			
						
						
			//INPUTP TYPES
			//echo $fname."->".$fparam['type']."<br>";
			//input/text is default
			if(isset($fparam['type'])){
				$type=$fparam['type'];
				}
			else {
				$type=self::default_values('type');
				}
					
			if(strstr($type,'/')){
				//echo "composite";
				$str=trim(strstr($type,'/'),'/');
				$html.='<input type="'.$str.'" name="'.$fname.'" id="'.$fname.'" ';
				}
			elseif($type=='textarea'){
				$html.='<textarea name="'.$fname.'" id="'.$fname.'" ';
				}
			
			//name and id attributes
			//$html.='name="'.$fname.'" id="'.$fname.'" value="" ';
			
			//placeholder
			if(isset($fparam['placeholder'])){
				$placeholder=$fparam['placeholder'];
				}
			else {
				$placeholder=self::default_values('placeholder');
				}
			$html.='placeholder="'.$placeholder.'" ';
	
			//class: default: form-control
			if(isset($fparam['class'])){
				$class=$fparam['class'];
				}
			else {
				$class=self::default_values('class');
				}
			$html.='class="'.$class.'" ';
			
			//required
			if($this->is_required($fname)){
				$html.="required ";
				}
			
			//parse validation rules
			if(isset($fparam['validation_rules'])){			
			$rules = explode('|', $fparam['validation_rules']);
			//max_len, min_len
			foreach ($rules as $rule) {
				
                    $method = null;
                    $param = null;
					// Check if we have rule parameters
                    if (strstr($rule, ',') !== false) {						
                        $rule   = explode(',', $rule);
                        //$method = 'validate_'.$rule[0];
                        $param  = $rule[1];
                        $rule   = $rule[0];
                        
                        if($rule=='max_len')
							{
							$maxlen='maxlength="'.$param.'" ';
							}
						else {$maxlen='';}
						
                        if($rule=='min_len')
							{
							$minlen='data-minlength="'.$param.'" ';
							if(isset($fparam['data-minlength-error'])){
								$minlen.='data-minlength-error="'.$fparam['data-minlength-error'].'" ';
								}
							}
						else {$minlen='';}
						//echo $maxlen.$minlen."<br>";
						$html.=$maxlen.$minlen;
						
					}
				}			
			}
			//data-remote/data-remote-error
			if(isset($fparam['data-remote'])){
				$html.='data-remote="'.$fparam['data-remote'].'" ';
				if(isset($fparam['data-remote'])){
					$html.='data-remote-error="'.$fparam['data-remote-error'].'" ';
					}
				}
			
			//end of input field string
			// we add the value here
			
			if(strstr($type,'/')){
				$html.='value="';
				if(isset($_POST[$fname]) AND $fname!='captcha') $html.=$_POST[$fname];
				$html.='">';
				}
			elseif($type=='textarea'){
				$html.='>';
				if(isset($_POST[$fname])) $html.=self::filter_sanitize_string($_POST[$fname]);
				$html.='</textarea>';
				}
			
			//html error block 	
			$html.='</div><div class="help-block with-errors col-sm-offset-3 col-sm-9">';
			
			if(isset($messages[$fname])) $html.="<ul class=\"list-unstyled\"><li>".$messages[$fname]."</li></ul>";
			
			$html.='</div></div>';
			}
			
			//hidden input fields for testing
			$html.='<input type="hidden" name="st" id="st" value="'.time().'">';
			$html.='<input type="hidden" name="placebofield" id="placebofield" value="">';

			//submit button
			$html.='<div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<input type="submit" id="submit-form" name="send" value="Send" class="btn btn-default" ';
						if (isset($disable) && $disable === true) $html.= ' disabled="disabled"';
			$html.=	'></div></div>';

			//End the form
			$html.="</form>";
			
					
			echo $html; 
		}
	
	/*
	 * Build the email
	 * 
	 */
	 public function build_email($data){
		 unset($data['captcha']);
		 unset($data['sess_cap']);
		 unset($data['placebofield']);
		 unset($data['send']);
		 unset($data['st']);
		 
		$message = "Messaggio inviato da ".self::$site.": \n\n";
		foreach ($data as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subval) {
					$message .= ucwords($key) . ": " . $subval . "\r\n";
				}
			} else {
				$message .= ucwords($key) . ": " .$val . "\r\n";
			}
		}
		$message .= "\r\n";
		$message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
		
		if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
			$headers   = "From: ".self::$mail_cnf."\r\n";
		} else {
			$headers   = "From: ".self::$site."<".self::$mail_cnf.">\rn";
		}
		$headers  .= "Reply-To: {$data['email']}\r\n";
		$email = $data['email'];
		
		$object=$data['object'];
		
		$mail_config=array(self::$mail_cnf,$object,$message,$headers);
		return $mail_config;
		/*if (mail(self::$mail_cnf,$object,$message,$headers)) {
			if (!empty($thanksPage)) {
				header("Location: $thanksPage");
				exit;
			} else {
				$mailsent[] = 'Messaggio mandato con successo.';
				$disable = true;
			}
		}*/ 
		//else {$error_msg[] = 'Your mail could not be sent this time. ['.$points.']';} 
		
		}
	
	/*
	 * Retrun an array with required fields
	 */	
	private function get_required(){
	$fields=$this->fields_map();

	foreach ($fields as $key => $val) {
		if(isset($val['validation_rules'])){			
			$rules = explode('|', $val['validation_rules']);
            if (in_array('required', $rules)){
				$keys[]=$key;
				}
			}
		}
	return $keys;
	}
	
	/*
	 * Verify if a field is required
	 * 
	 */
	private function is_required($name){
		$requiredFields=$this->get_required();
		if (in_array($name, $requiredFields)){
			return true;
			}
		else return false;	
		}
	
	/*
	 * Return server code to ajax call
	 * made by Bootstrap Validator
	 * 
	 */
	public function get_response_code($flag){
	//var_dump($flag);
	if( $flag===true){
		http_response_code(200); //good
		}
	else {
		http_response_code(418); //bad
		}	
	}
	
	/* 
	 * Extended validation: moved to GUMP file
	 * this comment is just a reminder
	 * 
	 * Validate against a blacklist
	 *
	public function validate_blacklist($field, $input, $param = NULL){}
	/*
	 * Validate captcha
	 * 
	 *
	public function validate_captcha($field, $input, $param = NULL){}*/
	
	/* 
	 * Apply strpos with an array
	 * found on php.net
	 * 
	 */
	public function strpos_array($haystack, $needles) {
	    if ( is_array($needles) ) {
	        foreach ($needles as $str) {
	            if ( is_array($str) ) {
	                $pos = strpos_array($haystack, $str);
	            } else {
	                $pos = strpos($haystack, $str);
	            }
	            if ($pos !== FALSE) {
	                return $pos;
	            }
	        }
	    } else {
	        return strpos($haystack, $needles);
	    }
	}
	
	/*
	 * TESTS 
	 * inspired by:https://github.com/mccarthy/phpFormProtect/tree/master/phpfp
	 * and
	 * https://github.com/jemjabella/PHP-Mail-Form/blob/master/mail_form_v2.txt
	 * 
	 */
	public function tests($data){
		$score=0;
		if(self::test_isbot()!==false){
			$score=+1;
			}			
		if(self::test_hidden_form_field($data)!==false){
			$score=+2;
			}
			
		if(self::test_timed_form_submission($data)!==false){
			$score=+3;
			}
		//var_dump($score);
		if($score==0) return true;
		else return false;
		}
		 
	public function test_isbot(){
		
		//uncomment next line to test the test
		//$_SERVER['HTTP_USER_AGENT']=$_SERVER['HTTP_USER_AGENT'].'Blaiz';
		
		$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

		foreach ($bots as $bot)
			if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
				return true;

		if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
			return true;
	
	return false;
	}
	
	public function test_hidden_form_field($data) {
		//uncomment next line to test the test
		//$data["placebofield"]='xxx';
		if(isset($data["placebofield"]) && strlen($data["placebofield"]) > 0) {
			return true;	
		}
		else {
			return false;
		}
	}
	
	public function test_timed_form_submission($data) {
		$display_time = $data["st"];
		$submit_time = time();
		$result_time = $submit_time-$display_time;
		//echo "<hr>".$submit_time."-".$display_time."=".$result_time."<hr>";
		//less than 2 seconds or more than 60 minutes
		$min_time=2;
		//Test, set this to $max_time=3; to test the test
		$max_time=3600;

		if($result_time < $min_time || $result_time > $max_time) {
			return true;
		}
		else {			
			return false; 	
		}
		
	}

	
	
} // EOC
