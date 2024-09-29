<form name="form1" id="form1" method="post" onsubmit="return false;">
	<div id="wrapper">
		<div id="login" class="animates form">
			<h1>LOGIN</h1> 
			<div style="background:#ffffff; border:dotted thin; margin-bottom:8px; display:none" id="messagebox_main">
				<div style="float:left; display:none" id="loading">
					<img style="height: 24px;padding-top: 5px;" src="<?php echo baseUrl(); ?>assets/images/loading.gif"></div><div style="float:left; padding:6px ; color:#FF0000; font-size:14px;font-family: " trebuchet="" ms","myriad="" pro"Berlin Sans FB Demi"="" id="messagebox"></div>
				<div style="clear:both"></div>
			</div>
			<p> 
				<label for="username" class="uname" data-icon="u">&nbsp;</label>
				<input autocomplete="off" id="username" name="username" type="text" placeholder="Username">
			</p>
			<p> 
				<label for="password" class="youpasswd" data-icon="p">&nbsp;</label>
				<input autocomplete="off" id="password" name="password" type="password" placeholder="Password"> 
			</p>
			<p class="keeplogin" style="display: none;"> 
				<input type="checkbox" name="keepingme" id="keepingme"> 
				<label for="loginkeeping">Keep me logged in</label>
			</p>

			<div class="set-1">
				<ul>
					<li><a class="login-button" href="javascript:void(0);" onclick="jQuery('#form1').submit();">login</a></li>
					<strong></strong>
				</ul>
			</div>

		</div>
	</div>
</form>


<script>
	jQuery('#form1 #username, #form1 #password').keydown(function (e) {
		if (e.keyCode == 13) {
			$('#form1').submit();
		}
	}) ;

	$(document).ready(function(){ 
		$("#form1").submit(function(){
	
			var isChecked = $('#keepingme:checked').val()?'1':'';
			//alert(isChecked);
		
			$("#messagebox_main").fadeIn();
			$("#loading").show();
		
			if($("#username").val()=='')
			{
				$("#messagebox").hide();
				$("#messagebox").html('Enter your username');
				$("#loading").fadeOut();
				$("#messagebox").fadeIn();
				$("#username").focus();
				return false;
			}		
			else if($("#password").val()=='')
			{
				$("#messagebox").hide();
				$("#messagebox").html('Enter your password');
				$("#loading").fadeOut();
				$("#messagebox").fadeIn();
				$("#password").focus();
				return false;
			}		
		
		
			var json_data =  {
				username:$("#username").val(),
				password:$("#password").val(),
				loginkeeping:isChecked
				
			}; 
		
	
			$.post('<?php echo siteUrl(); ?>login/onLogin', json_data, function(data){	
				if(data.status == 'success')
				{
					$("#messagebox").hide();
					$("#messagebox").html(data.view);
					$("#loading").hide();
					$("#messagebox").fadeIn();
								
					window.location.href="<?php echo siteUrl(); ?>dashboard";
								
				}	
				if(data.status == 'error')
				{
					$("#messagebox").hide();
					$("#messagebox").html(data.view);
					$("#loading").hide();
					$("#messagebox").fadeIn();
								
				}
			});						   
		});						   

	});

</script>
