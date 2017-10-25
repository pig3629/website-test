<?php
    include('config.php');
    header("Content-Type:text/html; charset=utf-8");
    header("Content-Type: application/json; charset=utf-8");
    
      switch ($_POST['type']) {
        case 'email_check_setting':      email_check_setting();   break;
        case 'serch_chk':                serch_chk();             break;
        case 'checkbox_delete':          checkbox_delete();       break;
        case 'edit_check':               edit_check();            break;
      }

    //檢查有無重複的email
      function email_check_setting(){ 
        $pattern = "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/";
        if(!empty($_POST["email_chk"])){  //判斷傳送過來是否為空
            if(preg_match( $pattern,$_POST["email_chk"])){
                $email=$_POST["email_chk"];
                $sql = "SELECT * FROM users WHERE email=?"; 
                $rs = db_query($sql,array($email));
                echo db_num_rows($rs); // 檢查有沒有重複
            }
        }else{
            echo 1;
        }
      }
      
      //查詢名字
      function serch_chk(){  //查到的姓名
        $result=array();
        $u=0;
        $patternSerch = "/^[\_\$\%\#\*\+\=\<\>\-\'\!\"\@\\\~\^\&\|]+/";
        $serch = htmlentities($_POST["serch"],ENT_QUOTES,"UTF-8");
        if(preg_match($patternSerch,$serch)>0){  
          echo 1; 
          exit;
        }
        $sql = "SELECT * FROM users WHERE name LIKE ? ORDER BY name ASC"; 
        $rs = db_query($sql,array("$serch%"));  
        while($r = db_fetch_array($rs)){
          $u++;
          foreach($r as $index=>$value){
            if (gettype($index)=='string'){
              $result[$u][$index] = $value;
            }
          }
        }
        if(!empty($result)){ // 直接判斷有沒有組成的資料，沒有用num_rows 因為 與傳送"空格"有關
          echo json_encode($result);
        }else{
          echo '1'; //沒有資料
        }
      }

      //刪除
      function checkbox_delete(){
        $result = array();
        if(empty($_POST['check_id'])) exit;
        $questionMarks = str_repeat("?,",count($_POST['check_id'])-1)."?";
        $sql = "SELECT * FROM users where id in ($questionMarks)"; 
        $rs = db_query($sql,$_POST['check_id']); 
        while($r=db_fetch_array($rs)){
          $result[]=$r['id'];    
        } 
        $str_del= implode(",",$result);
        $sql = "DELETE FROM users where id in ($str_del)";//刪除比數,將符合的比數刪除 
        $rs = db_query($sql); 
        echo json_encode($result); //將資料用json方式傳回去
        if(empty($result))  echo '1';
      } 
      /* 邊輯修改 */
      function edit_check(){
        $result = array();
        $arr = array();    
        $id = $_POST['edit_check']['id'];
        $sql = "SELECT * FROM users where id =?"; 
        $rs = db_query($sql,array($_POST['edit_check']['id']));  //資料庫的資料
        $r=db_fetch_array($rs);
        foreach($r as $index=>$value){
          if(gettype($index)=='string'){ //資料抓字串
            $str = gettype($index)=='string';
            $result[$index] = $value;
          }
        }
        $ans_diff = array_diff($_POST['edit_check'],$result); //差集，比對資料
        //驗證信箱
        if(!empty($ans_diff['email'])){
            $patternEmail = "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/";
            if(preg_match($patternEmail,$ans_diff['email'])==false){
              echo 1;
              exit;
            }
        }
        //驗證電話
        if(!empty($ans_diff['phone'])&& $ans_diff['phone']!='null'){
          $patternPhone= "/^(0\d+)-(\d{4,8})?$/";
          if(preg_match($patternPhone,$ans_diff['phone'])==false){ //驗證失敗跳開
            echo 1;
            exit;
          }
        }
        foreach($ans_diff as $key=>$value){
          if($value == "null") $value = ''; //存成空值
          $str .= $key."="."'$value'".",";
        }
        $str = ltrim(rtrim($str,","),1); //去除逗號
        $sql = "UPDATE users SET $str where id = $id";
        db_query($sql);
        //將新的資料丟給前台
        if(db_query($sql)!=''){
          $sql2 = "SELECT * FROM users";
          $as = db_query($sql2);
          while($ast=db_fetch_array($as)){
            foreach($ast as $index=>$value){
              if (gettype($index)=='string'){
                $result[$ast['id']][$index] = $value; //將id變成key
              }
            }
            $ww[$ast['id']]=$result[$ast['id']];
          }
          echo json_encode($ww);
        }else{
          echo 1;//失敗
        }
        
      }  
?>