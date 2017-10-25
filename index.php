<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="Scripts/jquery-3.0.0.min.js"></script>
	<style>
        input,select{
            border-radius: 5px;
            border: 1px solid darkgray;
            height: 25px;
            padding-left: 10px;
            font-family: 微軟正黑體;
            font-size:16px;
            outline:none;
        }
		input[type="submit"],input[type="button"]{
			height: 30px;
			cursor: pointer;
		}
		input[type="checkbox"],input[type="radio"]{
			width: 20px; 
  			height: 20px; 
  			cursor: pointer;
		}
		table, td, th {    
			border: 1px solid #ddd;padding: 15px;
		}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		.notice{
			font-weight: bold;
			font-size: 15px;
			color:crimson;
		}
	</style>
	</head>
		<body>
			<form name="form1" method="post" id="form1" onsubmit="return checkForm();" action="sql.php" >
				<table style="margin-bottom: 10px;">
					<thead>
						<tr>
						<th><span class='notice' style='font-size:25px;'>*</span>姓名:</th>
						<td><input type="text" name="username" id="username"  placeholder="請寫名字">
						<text class='notice' name="noticename" id="noticename"/></td>
						</tr>
						<tr>
						<th><span class='notice' style='font-size:25px;'>*</span>Email:</th>
						<td><input type="text" name="email" id="email"  placeholder="請填入信箱" onchange="checkEmail();" >
						<text class='notice' name="noticeemail" id="noticeemail" /></td>
						</tr>
						<tr>
						<th><span class='notice' style='font-size:25px;'>*</span>性別:</th>
						<td><input type="radio" name="sex"  id="male" value='1' checked >男
						<input type="radio" name="sex" id="female" value='2'>女
						<text type="hidden" name="notice3" ></td>
						</tr>
						<tr>
						<th>電話:</th>
						<td><input type="text" name="phone" id="phone" placeholder="請填寫電話"  onchange="isTel();">
						<text class='notice' name="noticephone" id="noticephone"/></td>
						</tr>
						<tr>
						<th>職業:</th>
						<td>
						<select name="joblist" id="joblist">
						<option value="student">學生</option>
						<option value="soldier">軍人</option>
						<option value="normal">一般行業</option>
						<option value="monk">seafood</option>
						</select>
						</tr>
						<tr>
						<th>興趣:</th>
						<td>
						<input type="checkbox" name="interest[]" id='shopping' value='shopping'>逛街
						<input type="checkbox" name="interest[]" id='sport' value='sport'>運動
						<input type="checkbox" name="interest[]" id='book' value='book'>看書
						<input type="checkbox" name="interest[]" id='seafood' value='seafood'>讚嘆妙禪
						</td>
						</tr>
						<tr>
						<th>備註:</th>
						<td><textarea name="notes" rows="4" cols="50" id="notes"placeholder="歡迎填寫~~"></textarea></td>
						</tr>
					</thead>
				</table>
				<input type="submit"  value="送出" >
				<input type="button"  value='資料清單' onclick="javascript:location.href='list.php'">
				<!-- <input type='submit'  > -->
				
			</form>


			<script type="text/javascript">
				var form = document.getElementById('form1'); //表單id

				$(document).ready(function(){
					//清除提示字
					  $("#username").keypress(function(){
					    $("#noticename").html(''); //append昨天是是著寫過，會包在input裡面。結果不如預期，就沿用原本的。<input><span></span></input>
					  });
					  $("#email").keypress(function(){
					    $("#noticeemail").html('');
					  });
					  $("#phone").keypress(function(){
					    $("#noticephone").html('');
					  });
					
					});
					
				//檢查表單的空格
				function doubleCheck(){
					var con = 0; 	//通關密語
					if($("#email").val() == ''){
						 $("#noticeemail").html('請填寫信箱');
						 $("#email").focus();
						 con = 1;
					}
					if($("#username").val() == ''){
						 $("#noticename").html('請填寫名字');
						 $("#username").focus();
						 con = 1;   
					}
					return con;  //將判斷內容傳出去
				}
				//檢查Email，是否有重複
				function checkEmail() {
					var strEmail = $("#email").val();
					var con=0;  //通關密語
					var emailRule = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/; //email的regular
					if(strEmail.search(emailRule)){ //serch沒找到返回值-1
					   	$('#email').focus(); 
						$("#noticeemail").html('email格式錯誤!');
						con =1;
					}else{
						$("#noticeemail").html('');
						$.ajax({   //格式對了，將資料傳出去
							method: "POST",
							url: "dataCRUD.php",
							async: false,  //變成請求同步，在還沒執行完ajax不會跑完結果
							data: { type: 'email_check_setting', email_chk : strEmail}
						}).done(function(data){
							if (data ==1){
								$("#noticeemail").html('傳送錯誤!');
							}
							if(data!=0){
								$("#noticeemail").html('有人使用過了!');
								$('#email').focus(); 
								con =1;
							}else if (data==0){
								$("#noticeemail").html('可以使用此信箱!');							
							}
						})
					}	
					return con;											
				} 
				//檢查電話號碼 
				function isTel(){ 
					var con = 0;	//通關密語
					var str = $('#phone').val();
					if(str!=''){
						var pass= /^(0\d+)-(\d{4,8})?$/; //格式必須是 0開頭 僅數字 - (數字6~8碼)，$字串結束 
						if(str.search(pass)) {   //如果返回是-1，代表輸入格式錯誤
							$('#phone').focus(); 
							$("#noticephone").html('電話格式錯誤!!\n02-xxxaaaa(區碼-6~8碼)');
							con = 1; 
						}else{
							$("#noticephone").html('');
						}
					}
					return con;
				}
				//送出前再檢查	
				function checkForm(){
					if(doubleCheck() == 0 && checkEmail() == 0 && isTel() == 0){   //判斷所有的空格
						if(confirm('確定送出表單?')){
							return true;
						}
					}
					return false;
				}
			</script>	

		</body>
</html>	


<!-- 				////****flag寫法*****//// 備案寫法
					// //檢查名子
					// var flag1 = false; 
					// if(form.username.value == ''){
					// 	$("#username").focus();
					// 	$("#noticename").html('請填寫名字');
					// 	flag1 = true;
						
					// }
					// //檢查信箱
					// if(form.email.value == ''){
					// 	if(!flag1) {
					// 		$("#email").focus();
					// 		flag1 = true;
					// 	}
					// 	$("#noticeemail").html('請填寫信箱');
					// }
					// //檢查電話 ，OK
					// if(form.phone.value == ''){
					// 	if(!flag1) {
					// 		$("#phone").focus();
					// 		flag1 = true;
					// 	} 
						
					// 	$("#noticephone").html('請填寫正確的電話格式02-xxx');
					// }
					////****flag寫法*****////
					
					//興趣,並沒有警告未通過，因為題目沒有要求
					// for(var i = 0; i <= interest.length; i++){
					// 	if(interest[i].checked){
					// 		selectedInterest.push(interest[i].id);
					// 	}
					// }

					// 
			 -->