<?
    include('config.php');
    ini_set('default_charset','utf-8');
    
    $value='yes'; //回應傳送結果
    if(!$_POST) $value = 'undefined'; 
    if($_POST){  //判斷傳送過來的方式
        if(empty($_POST['username'])||empty($_POST['sex'])||empty($_POST['email'])) exit; //三者皆空，人生空空，跳開
        $patternEmail = "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/"; 
        if (!preg_match($patternEmail,$_POST["email"])) exit; //不符合信箱格式就跳開
        $email = $_POST["email"]; //以後直接用$_POST，不用直接放在變數
        $sql = "SELECT * FROM users WHERE email=?"; 
        $name=$_POST['username'];
        $rs = db_query($sql,array($email));
        if(!db_eof($rs)){ // 檢查有沒有重複
            exit;
        }        
        $phone='';  //初始值為空
        /* 檢查電話格式 */
        $patternPhone = "/^(0\d+)-(\d{6,8})?$/";
        if(!empty($_POST["phone"])){  //判斷boolean是否true 或false
            if(preg_match($patternPhone,$_POST["phone"])){ 
                $phone=$_POST["phone"]; 
            }else{      //不符合格式跳開
                 exit;
             }
        }
        $sex=$_POST['sex'];
        $job;        
        $interest=''; //初始值為空，這裡要設否則在insert會無值顯是錯誤
        $notes='';  //初始值為空
        if(!empty($_POST['joblist']))  $job=$_POST['joblist'];
        if(!empty($_POST['interest'])) $interest =implode(',',$_POST['interest']);
        if(!empty($_POST['notes']))    $notes=$_POST['notes'];
        $sql = "INSERT INTO users (email,name,sex,phone,job,interest,notes) VALUES ('$email','$name','$sex','$phone','$job','$interest','$notes')";
        $rs = db_query($sql);
        if ($rs === false) {
            $value = 'no';   //驗證是否傳送成功
        }
    }

   
            // $rs = db_query($sql);
            // echo "<script language=javascript>window.alert('送出成功!!╮(╯_╰)╭');</script>";
            // header("Refresh: 0; url=list.php");exit;
        

            // echo "<script language=javascript>window.alert('傳送不成功!! ╯‵□′)╯︵┴─┴');</script>";
            // echo "<script language=javascript>history.back();</script>";exit;
        

            // echo "<script language=javascript>window.alert('傳送格式錯誤!!');</script>";exit;
    
?>
    <script type="text/javascript">
        var value="<?php echo $value; ?>";
        if(value=='yes'){
            alert('送出成功!!╮(╯_╰)╭');
        }
        if(value=='no'){
            alert('傳送不成功!! ╯‵□′)╯︵┴─┴');history.back();
        }
        if(value=='undefined'){
            alert('傳送格式錯誤!!');history.back();
        }
    </script> 
<?= header("Refresh: 0; url=list.php");?>