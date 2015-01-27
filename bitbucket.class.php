<?php

class bitbucket
{
	public static $user;
	public static $pass;
	public static $csrftoken;
	public static $base_url;
	public static $login_url;
	public static $useragent;
	
	function csrftoken($csrf = 'csrftoken='){
	$headers = get_headers(self::$login_url);
	$headers = print_r($headers, true);
	$position_of_csrftoken = strpos($headers, $csrf) + strlen($csrf);
	$csrftoken = substr($headers, $position_of_csrftoken, 32);
	return $csrftoken;
	}

	function login($user, $pass, $echo = 1){
	$ch = curl_init();
	self::$login_url = self::$base_url . "/account/signin/";
	curl_setopt($ch, CURLOPT_URL, self::$login_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,'username='.urlencode($user).'&password='.urlencode($pass).'&csrfmiddlewaretoken='.urlencode(self::$csrftoken));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: csrftoken=".self::$csrftoken));  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=".self::$csrftoken);
	curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
	curl_setopt($ch, CURLOPT_REFERER, self::$base_url);
	$html = curl_exec($ch) or die(curl_error($ch));
	if($echo == 1)
	echo $html;
	}
	
	function owner_id(){
	$this->login(self::$user, self::$pass, self::$csrftoken, $echo = 0);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, self::$base_url."/repo/create");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: csrftoken=".self::$csrftoken));  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=".self::$csrftoken);
	curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
	curl_setopt($ch, CURLOPT_REFERER, self::$base_url);
	$html = curl_exec($ch) or die(curl_error($ch));
	$start_data_current_user = strpos($html, 'data-current-user=') + 18;
	$data_json_length = strpos($html, '"', $start_data_current_user+1) - $start_data_current_user;
	$json = substr($html, $start_data_current_user+1, $data_json_length-1);
	$json = str_replace('&quot;', '"', $json);
	$json = json_decode($json);
	$id = $json->id;
	return $id;
	}
	
	function create_repo($repo_name, $description="", $is_private="on", $forking="no_public_forks", $no_forks= false, $no_public_forks=true){
	$this->login(self::$user, self::$pass, self::$csrftoken, $echo = 0);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, self::$base_url."/repo/create");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,'owner='.urlencode($this->owner_id()).'&name='.urlencode($repo_name).'&description='.urlencode($description).'&csrfmiddlewaretoken='.urlencode(self::$csrftoken).'&is_private='.urlencode($is_private).'&forking='.urlencode($forking).'&no_forks='.urlencode($no_forks).'&no_public_forks='.urlencode($no_public_forks).'&scm=git&language=');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(""));  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=$csrftoken;");
	curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
	curl_setopt($ch, CURLOPT_REFERER, self::$base_url);
	$html = curl_exec($ch) or die(curl_error($ch));
	echo $html;
	}
	
	function make_file($file_name, $repo_name, $file_content, $branch = "master"){
	$this->login(self::$user, self::$pass, self::$csrftoken, $echo = 0);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, self::$base_url."/!api/internal/repositories/".self::$user."/$repo_name/oecommits/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"branch\":\"$branch\",\"files\":[{\"path\":\"$file_name\",\"content\":\"$file_content\"}],\"message\":\"$file_name created online with Bitbucket\",\"parents\":[\"\"],\"repository\":{\"full_name\":\"".self::$user."/$repo_name\"},\"timestamp\":\"2015-01-27T14:23:49.531Z\",\"transient\":false}");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-CSRFToken: ".self::$csrftoken));  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=".self::$csrftoken);
	curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
	curl_setopt($ch, CURLOPT_REFERER, self::$base_url);
	$html = curl_exec($ch) or die(curl_error($ch));
	echo $html;
	
	}
	
	function import_repo($repo_name, $url="https://github.com/ziphir/web.git", $description="", $is_private="on", $forking="no_public_forks", $no_forks= false, $no_public_forks=true){
	$this->login(self::$user, self::$pass, self::$csrftoken, $echo = 0);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, self::$base_url."/repo/import");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"source_scm=git&source=source-git&goog_project_name=&goog_scm=svn&sourceforge_project_name=&sourceforge_mount_point=&sourceforge_scm=svn&codeplex_project_name=&codeplex_scm=svn&url=".urlencode($url)."&username=&password=&owner=".$this->owner_id()."&name=$repo_name&description=".urlencode($description)."&is_private=$is_private&forking=$forking&no_forks=$no_forks&no_public_forks=$no_public_forks&language=&csrfmiddlewaretoken=".self::$csrftoken."&scm=git");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(""));  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=".self::$csrftoken);
	curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
	curl_setopt($ch, CURLOPT_REFERER, self::$base_url);
	$html = curl_exec($ch) or die(curl_error($ch));
	echo $html;
	}
	
	function raw_node($repo_name){
	$this->login(self::$user, self::$pass, self::$csrftoken, $echo = 0);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, self::$base_url."/".self::$user."/$repo_name/src");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(""));  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION["cookie"]);
	curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=".self::$csrftoken);
	curl_setopt($ch, CURLOPT_USERAGENT, self::$useragent);
	curl_setopt($ch, CURLOPT_REFERER, self::$base_url);
	$html = curl_exec($ch) or die(curl_error($ch));
	$start_of_raw_node = strpos($html, 'data-current-cset=') + 19;
	$raw_node_length = 40;
	$raw_node = substr($html, $start_of_raw_node, $raw_node_length);
	if(ctype_xdigit($raw_node))
	return $raw_node;
	else
	return false;
	}
}
?>
