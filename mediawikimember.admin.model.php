<?php
class mediawikimemberAdminModel extends mediawikimember {

  private $cURL;
  private $cookiefile;

  function __construct() {
    $this->cookiefile = tempnam("/tmp", "CURLCOOKIE");
    $useragent = 'Mediawiki for XE Member';
    $url = $this->getURL() . '/api.php';

    $curloptions =
      array(
        CURLOPT_COOKIEFILE => $this->cookiefile,
        CURLOPT_COOKIEJAR => $this->cookiefile,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_USERAGENT => $useragent,
        CURLOPT_POST => true
      );

    $this->cURL = curl_init();
    curl_setopt_array($this->cURL, $curloptions);
    curl_setopt($this->cURL, CURLOPT_URL, $url);
  }

  function __destruct() {
    curl_close($this->cURL);
    unlink($this->cookiefile);
  }

	function getGroupListById($user_id = NULL) {
		$oMemberModel = &getModel('member');
		$member = $oMemberModel->getMemberInfoByUserID($user_id);
		return $oMemberModel->getMemberGroups($member->member_srl);
	}

	function grantCheck($user_id = NULL) {
		if ($user_id == NULL) return NULL;

		$group_list = $this->getGroupListById($user_id);

		if ($group_list[75307] || $group_list[3]) {
      return true;
    }

		return false;
	}

  function getToken($type = NULL) {

    switch ($type) {
      case "login": $token = "logintoken"; break;
      case "createaccount": $token = "createaccounttoken"; break;
      case "csrf": $token = "csrftoken"; break;
      default: return NULL;
    }

    $option["meta"] = "tokens";
    $option["type"] = $type;
    
    $res = $this->sendQuery('query', $option);
    // print_r($res);
    return $res->query->tokens->$token;
  }

  function getURL() {
    $config = getModel('module')->getModuleConfig('mediawikimember');
    return $config->site_protocol . $config->site_url;
  }


  /**
   * Send
   */

  function sendQuery($action = NULL, $option = NULL) {
    if ($action == NULL) return NULL;

    $config = getModel('module')->getModuleConfig('mediawikimember');

    $option["action"] = $action;
    $option["format"] = 'json';
    $option = http_build_query($option);

    curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $option);

    $res = curl_exec($this->cURL);

    if(curl_errno($this->cURL)){
      print_r("Error 004: " . curl_error($this->cURL));
    }

    return json_decode($res);
  }

  /**
   * Account
   */

  function createAccount($username = NULL, $password = NULL, $email = NULL, $realname = NULL) {

    if ($username == NULL || $password == NULL || $email == NULL || $realname == NULL) return NULL;

		if ($this->grantCheck($username) == false) return NULL;
    if ($this->adminLogin() !== "Success") return NULL;
    if ($username == "admin") return NULL;

    $option = array();
    $option["createtoken"] = $this->getToken('createaccount');
    
    $option["username"] = $username;
    $option["password"] = $password;
    $option["retype"] = $password;
    $option["realname"] = $realname;
    $option["email"] = $email;
    $option["createreturnurl"] = $this->getURL();

    $res = $this->sendQuery('createaccount', $option);

    $this->logout();

    return $res;
  }

  function adminLogin() {
    $config = &getModel('module')->getModuleConfig('mediawikimember');
    return $this->login($config->admin_id, convert_uudecode($config->admin_pw));
  }

  function login($username = NULL, $password = NULL) {
    if ($username == NULL || $password == NULL) return NULL;

    $option = array();
    $option["lgtoken"] = $this->getToken('login');
    $option["lgname"] = $username;
    $option["lgpassword"] = $password;

    $res = $this->sendQuery('login', $option);

    if ($res->login->result == "Success") return "Success";
    else return "Fail";
  }

  function logout() {
    $res = $this->sendQuery('logout', $option);
  }

  function changeUserPassword($username = NULL, $proldpassword = NULL, $prnewpassword = NULL) {

		if ($this->grantCheck($username) == false) return NULL;
    if ($this->login($username, $proldpassword) !== "Success") return NULL;

    $option["changeauthtoken"] = $this->getToken('csrf');
    $option["changeauthrequest"] = "MediaWiki\\Auth\\PasswordAuthenticationRequest";
    $option["password"] = $prnewpassword;
    $option["retype"] = $prnewpassword;

    $res = $this->sendQuery('changeauthenticationdata', $option);

    $this->logout();
    return $res;
  }

  function deleteUser($username = NULL) {
    return $res = $this->sendQuery('unlinkaccount', $option);
  }
}
?>