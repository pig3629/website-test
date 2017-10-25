<?
	//::PDO function Utilities By Mic rou ()
	date_default_timezone_set("Asia/Taipei") ;

	//$CompanyName = "";
	$GobalWebRoot = $_SERVER['DOCUMENT_ROOT'];
	
	//::for首頁輪撥圖片
	$newspic_Path = 'data/news/';
	
 	$VenderDB = 'test';
	$dsn = "mysql:host=localhost;dbname=$VenderDB";
	$db = new PDO($dsn, "root","16657143");
	$db->exec("set names utf8");
	
	/* 用法示範程式
	$sql = "SELECT * FROM admins where ACCOUNT=? and PWD=?";
	$rs = db_query($sql,array('sa','111'));
	$r = db_fetch_array($rs);
	print_r($r);
	var_dump(db_eof($rs));	
	db_close();
	
	//:: get the ID of the last inserted row or sequence value
	$db->lastInsertId();
	*/
	
	//--- Common Functions -----------------------------------------------------------------//
	function removeSymbol($targetString){
		$symbolArray = array("<",">","'",'"');
		foreach ($symbolArray as $value) $targetString = str_replace($value, "", $targetString);
		return $targetString;
	}
	
	function check_input($value) {
		//白名單
		$wlist = array('ai-siou','money-money');
		if(in_array($value,$wlist)) return $value;
		
		$patten = "/([ \-#'<>])/";
		$rzt = preg_split($patten, $value);
		if(strlen($rzt[0])>16) return substr($rzt[0],0,16); else return $rzt[0];
	}
	
	//---	Database Functions	--------------------------------------------------------------//	
	function db_close() {
		$db = null;
	}
	
	function db_query($sql) {
		global $db;
		$paramCT = func_num_args();
		if($paramCT==1) {
			$rs = $db->prepare($sql);
			$rs->execute();
			return $rs; 
		} else if($paramCT>1) {
			$paramAR = func_get_args();
			$rs = $db->prepare($sql);
			$rs->execute($paramAR[1]);
			return $rs;
		} else return false;
	}
	
	function db_fetch_array($rs) {
		return $rs->fetch();
	}

	function db_num_rows($rs) {
		return $rs->rowCount();
	}
	
	function db_eof($rs) {
		return $rs->rowCount()==0;
	}
?>