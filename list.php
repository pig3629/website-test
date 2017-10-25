<?php
    include("config.php");
    header("Content-Type:text/html; charset=utf-8");
?>
<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script type="text/javascript" src="Scripts/jquery-3.0.0.min.js"></script>
        <style>
            input,
            select {
                border-radius: 5px;
                border: 1px solid darkgray;
                height: 25px;
                padding-left: 10px;
                font-family: 微軟正黑體;
                font-size: 16px;
                outline: none;
            }

            input[type="checkbox"] {
                /* width: 50%; */
                cursor: pointer;
            }

            button {
                border-radius: 12px;
            }

            input[type="submit"],
            input[type="button"] {
                height: 30px;
                cursor: pointer;
            }

            table,
            td,
            th,
            tr {
                border: 1px solid #ddd;
                text-align: left;
                font-family: 微軟正黑體;
                border-collapse: collapse;
                padding: 15px;

            }
            .notice {
                font-weight: bold;
                font-size: 25px;
                color: crimson;
            }
        </style>
    </head>
    
    <body>
        <form id="form1" method="POST">
            <table id='show' style="margin-top: 5px;">
                <input type="text" id="serch" name="serch" placeholder="搜尋" />
                <input type="button" name="serch" value='搜尋' id="serch_submit" />
                <text class='notice' id="noneserch"></text>
                <thead>
                <tr>
                    <th>選取<input type="checkbox" id="checkAll" style="width: 50%" onclick="clickAll();"></th>
                    <th><span class='notice'>*</span>名字</th>
                    <th><span class='notice'>*</span>email</th>
                    <th><span class='notice'>*</span>性別</th>
                    <th>電話</th>
                    <th>職業</th>
                    <th>興趣</th>
                    <th>備註</th>
                    <th>編輯</th>
                </tr>
                </thead>
            <tbody id='show_list'>
    <?php
    $con = 0;
    $sql = "SELECT * FROM users ORDER BY name ASC";
    $rs = db_query($sql);
    $result = array();
    while($rst = db_fetch_array($rs)){
         $con++; //input用
        foreach($rst as $index=>$value){
            if (gettype($index)=='string'){
                //$result[$index] = $value; //將id變成key
                $result[$rst['id']][$index] = $value; //將id變成key
            }
        }
          // $json_array[]= $rst; 
    ?>
        <tr >
        <td style="width: 5%"><input type="checkbox" name="check[]" id="check<?php echo $con;?>" value=<?php echo $rst["id"];?>  style="width: 50%" first-chk="<?php echo $con;?>" ></td>
        <td ><input type="text" name="username<?php echo $con;?>" id="username<?php echo $con;?>" value="<?php echo $rst["name"];?>" onchange="checkName(<?=$con?>,<?php echo $rst["id"];?>)"></td>
        <td ><input type="text" name="email<?php echo $con;?>"  id="email<?php echo $con;?>" value="<?php echo $rst["email"];?>"  placeholder="請填信箱" onchange="checkEmail(<?=$con?>,<?php echo $rst["id"];?>)"></td>
        <td>
            <input type="radio" name="sex<?php echo $con;?>"  id="sex<?php echo $con;?>" value="1"  <?php if($rst["sex"]==1) echo 'checked';?>>男
            <input type="radio" name="sex<?php echo $con;?>"  id="sex<?php echo $con;?>"value="2"  <?php if($rst["sex"]==2) echo 'checked';?>>女
        </td>
        <td><input type="text" name="phone<?php echo $con;?>" id="phone<?php echo $con;?>" value="<?php echo $rst["phone"];?>"  placeholder="請填家電。04-2332xxxx" onchange="checkPhone(<?=$con?>,<?php echo $rst["id"];?>)"></td>
        <td>
            <select name="joblist<?php echo $con;?>" id="joblist<?php echo $con;?>" >

    <?php 
        $options = array('student'=>'學生','soldier'=>'軍人','normal'=>'一般行業','monk'=>'seafood' );  
        foreach($options as $key=>$value){?> 
        <option value="<?=$key?>" name="job"  <? if($rst['job'] == $key) echo 'selected'; ?>><?=$value?></option>
    <?}?>
            </select> 
        </td>
        <td>
    <?php      
        $chkecked = explode(',',$rst['interest']);
        $options = array('shopping'=>'逛街','sport'=>'運動','book'=>'看書','seafood'=>'讚嘆seafood');  
        foreach($options as $key=>$value){?> 
        <input type="checkbox"  name="interest<?php echo $con;?>[]"  id="interest<?php echo $con;?>" value="<?=$key?>" <? if(in_array($key,$chkecked)) echo 'checked'; ?>><?=$value?></option>
    <?}?>
        </td>
        <td><input type ="text" id="notes<?php echo $con;?>" name="notes" value="<?=$rst['notes']?>"/></td>
        <td><input type="button" name="edit<?php echo $con;?>" id='edit<?php echo $con;?>' value="修改" onclick="edit(<?php echo $con;?>,<?php echo $rst["id"];?>)"></td>
        </tr>
<?php } 
    $json_array = $result;  //將資料傳給js  ->265行
?>
        </tbody>
    </table>
    <hr>
        <input type ="button" onclick="javascript:location.href='index.php'" id="back" value="返回上一頁">
        <input type ="button" onclick="delete_file()" value="刪除">
        
    </form>
    <script>
        var temp_na ='';  //檢測名字的按鈕狀態
        var temp_tel =''; //檢測電話的按鈕狀態
        var temp_em ='';  //檢測信箱的按鈕狀態
        var temp_data = false
        var myArray = <?php echo json_encode($json_array); ?>; //資料庫抓到資料後，傳到document
            function callback() {
                var serch = document.getElementById("serch");
                var dataHtml = []; 
                var patternSerch = /^[\_\$\%\#\*\+\=\<\>\-\'\!\"\@\\\~\^\&\|]+/g;
                if(!serch.value.search(patternSerch)){
                    $("#noneserch").html('搜尋不包含特殊符號'); 
                    return false;
                }               
                $("#noneserch").html('');
                $.ajax({
                    method: "POST",
                    url: "dataCRUD.php",
                    data: { type: 'serch_chk',serch : serch.value},
                }).done(function(data){  
                    if (data==1) $("#noneserch").html('沒有資料!');
                    for (var i in data){
                        dataHtml.push("<tr id='inner"+i+"'>");
                        for(var idxKey in data[i]){ 
                            switch (idxKey){  //接到資料的keyname
                                case 'id':
                                    dataHtml.push("<td style=width: 5%><input input type='checkbox' name='check[]' value="+data[i]['id']+" id=check"+i+" style='width: 50%' first-chk="+i+">");
                                    break;
                                case 'sex': 
                                    var male='';
                                    var female='';
                                    male = "<td><input type='radio' name='sex"+i+"'   value='1' id='sex"+i+"' checked>男"+
                                    "<input type='radio' name='sex"+i+"'  value='2' id='sex"+i+"'>女";
                                    female = "<td><input type='radio' name='sex"+i+"'   value='1' id='sex"+i+"' >男"+
                                    "<input type='radio' name='sex"+i+"'  value='2' id='sex"+i+"' checked>女";
                                    if(data[i]['sex']==1){ //選擇男性印出男性checked的html
                                         dataHtml.push(male);
                                    }else{
                                         dataHtml.push(female);
                                    }
                                    break;
                                case 'job':
                                    var jobList={"student":"學生","soldier":"軍人","normal":"一般行業","monk":"seafood"};
                                    var option="<td><select name=joblist"+i+" id=joblist"+i+">";
                                    for(var jobName in jobList){
                                        option+="<option value="+jobName+" name=job ";
                                        if(data[i]['job'] == jobName) option+='selected'; 
                                        option+= ">"+jobList[jobName]+"</option>";
                                    }
                                    dataHtml.push(option);
                                    break;
                                case 'interest':
                                    var html ="<td>";
                                    if(!data[i]['interest']) data[i]['interest']='';
                                    var interestList={"shopping":"逛街","sport":"運動","book":"看書","seafood":"讚嘆seafood"};
                                    for (var intName in interestList ){
                                        html += "<input  type=checkbox name=interest"+i+"[] id="+intName+"";
                                        if(data[i]['interest'].indexOf(intName)> -1)   html+=" checked"; //indexOf(比對) keys
                                        html+=" value="+intName+">"+interestList[intName]+"";
                                    }
                                    dataHtml.push(html);
                                    break;
                                case 'email':
                                    dataHtml.push("<td><input type=text name="+idxKey+i+" id=email"+i+" placeholder=請填信箱 onchange=checkEmail("+i+","+data[i]['id']+") value="+data[i][idxKey]+" >");
                                    break;
                                case 'phone':
                                    dataHtml.push("<td><input type=text name="+idxKey+i+" id=phone"+i+" placeholder=請填家電。04-2332xxxx onchange=checkPhone("+i+","+data[i]['id']+")  value="+data[i][idxKey]+" >");
                                    break;
                                case 'name':
                                    dataHtml.push("<td><input type=text name=username"+i+" id=username"+i+" onchange=checkName("+i+","+data[i]['id']+") value="+data[i][idxKey]+" >");
                                    break;
                                default:
                                    dataHtml.push("<td><input type=text name="+idxKey+i+" id="+idxKey+i+" value="+data[i][idxKey]+">");
                            }
                            dataHtml.push("</td>");
                        }        /* for(var idxKey in data[i]) {印<td>內容} */
                         dataHtml.push("<td><input type=button value=編輯 name=edit"+i+"  id=edit"+i+" onclick=edit("+i+","+data[i]['id']+")>");
                    }           /* for( i in data) {印回來比數的內容} */ 
                    $("#show_list").html(dataHtml.join(''));    
                    if($("#serch").val()!=''){  //加在callback 會有問題
                        $("#back").attr("onclick","replace()"); //讓資料返回上一頁
                    }else{
                        $("#back").attr("onclick","javascript:location.href='index.php'"); //讓資料返回上一頁
                    }   
                     //console.log(typeof(dataHtml)); //Object // 是因為 dataHtml 是 array，才需要join成string,也可以用map
                })  /*  .done(function) 完成 */
                
            };
            /* 搜尋按下enter會呼叫call back() */
            $("#serch").keypress(function() {
                if (event.which == 13){
                    callback(); 
                }
            });

            $('#serch_submit').click(function(){
                callback();
            });
            /* 重整 */
            function replace(){ 
                location.href="list.php";
            }
            //$('#serch_submit').click(callback);
            /* 全選 */
            function clickAll(){ 
                if($("#checkAll").prop("checked")){ ///* prop傳回來是boolean,attr傳回來是'checked' */
                    $("input[name='check[]']").prop("checked", true);
                }else{
                    $("input[name='check[]']").prop("checked", false);
                }
            }   
            /* 刪除 */
            function delete_file(){ 
                var checkList = [];  //計算check值
                if(!$("input[name='check[]']").is(':checked')){ //沒有打勾時
                    alert('請選擇一項刪除!');
                    return false;
                } 
                if(confirm("確定要刪除?!")){
                    $("input[name='check[]']:checked").each(function(){       
                        checkList.push(this.value);        
                    });
                    $.ajax({
                        method: "POST",
                        url: "dataCRUD.php",
                        dataType:"json",
                        data: { type: 'checkbox_delete',check_id:checkList}
                    }).done(function(data){  
                        if(data == 1){ 
                            alert('找不到刪除的資料!');
                            return false;
                        }else{
                            for(var i in data){ //整的欄位的td刪掉
                                $("input[first-chk][value="+data[i]+"]").parent().parent().fadeOut(300, function() { $(this).remove();}); 
                            }
                        }                                
                    })
                }
                
            }
            
            /* 編輯修改 */
            function edit(id,num){
                //temp 在最外層有全域變數 
                if((temp_na + temp_tel + temp_em == 0) && confirm("確定要修改資料?")==true){  //3個驗證都通過
                    var int_ch=document.getElementsByName("interest"+id+"[]");
                    var phone_val =  $("#phone"+id).val();
                    var notes_val =  $("#notes"+id).val();
                    var temp_int = []; //interest值暫存
                    for(var k in int_ch){ 
                        if(int_ch[k].checked){ //當框框被打勾
                           temp_int.push(int_ch[k].value);
                        }
                    }
                    if(temp_int!='') var interest_val=temp_int.join(",") 
                    else var interest_val="null"  //當如果是空的就傳null
                    if(phone_val =='') var phone_val="null"  
                    if(notes_val =='') var notes_val="null"  
                    var arr_out = {
                        'id': num,
                        'name'  : $("#username"+id).val(),
                        'email' : $("#email"+id).val(),
                        'sex'   : $("#sex"+id+":checked").val(),
                        'phone' :  phone_val,
                        'job'   : $("#joblist"+id).val(), 
                        'interest' : interest_val,
                        'notes' :   notes_val
                    }
                    $.ajax({    
                        method: "POST",
                        url: "dataCRUD.php", 
                        dataType:"text",
                        data: { type: 'edit_check', edit_check : arr_out}
                    }).done(function(data){
                        if(data==1){
                            alert("修改失敗");
                            location.replace("list.php");
                        }else{
                            alert("修改成功");
                            myArray = data; //覆蓋舊的sql資料
                        }
                    })
                }
            }

            /* span清除，按鈕變更為不可用 */
            function span_empty(word,id,txt){
                if($(word+id).parent().find('span')!=''){
                    $(word+id).next().remove();
                }
                $(word+id).parent().append('<span class=notice>'+txt+'</span>');
                $(word+id).focus();
            }
            
            /* 信箱驗證 */
            function checkEmail(id,num) {
                var strEmail = $("#email"+id).val();
                var emailRule = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/; //email的regular
                switch(true){
                    case strEmail=='': 
                        span_empty("#email",id,'email不得為空!');
                        return temp_em=1;
                        break;
                    case (emailRule.test(strEmail)?false:true): //檢測信箱，合格傳false
                        span_empty("#email",id,'email格式錯誤!');
                         return temp_em=1;
                        break;
                    case strEmail == myArray[num]['email']: //當信箱相同時
                        $("#email"+id).next().remove(); //提示span消失
                        $("#email"+id).parent().append('<span class=notice style=color:orange>此為原信箱</span>');
                        $("#email"+id).next().fadeOut(1200);  //提示span2消失
                        return temp_em=0;
                        break;
                    default:
                        $.ajax({   
                            method: "POST",
                            url: "dataCRUD.php",
                            data: { type: 'email_check_setting', email_chk : strEmail}
                        }).done(function(data){
                            if(data == 1){
                                span_empty("#email",id,'有人使用過了!');
                                return temp_em=1;  
                            }else{
                                $("#email"+id).next().remove();
                                return temp_em=0;
                            }
                        })
                        break;
                }							
			} 
            /* 檢查電話 */
            function checkPhone(id,num){
                var phone_val =  $("#phone"+id).val();
                var phoneRule = /^(0\d+)-(\d{4,8})?$/;  
                switch(true){
                    case phone_val =='':
                        return temp_tel =0;
                        break;
                    case (phoneRule.test(phone_val)?false:true): //檢測電話，合格傳false
                        span_empty("#phone",id,'電話格式錯誤!');
                        return temp_tel = 1 ;
                        break;
                    default : 
                        $("#phone"+id).next().remove();
                        return temp_tel =0;
                    break;
                }   
            }
            /* 檢查名字 */
            function checkName(id,num){
                if($("#username"+id).val()==''){
                    span_empty("#username",id,'姓名不得為空!');
                    return false,temp_na=1;
                }else{
                     $("#username"+id).next().remove();
                     return temp_na=0;
                }
            }
            
        </script>
    </body>
</html>