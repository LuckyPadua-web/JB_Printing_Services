<?php
session_start();
include_once('config.php');
$msg='';
if(isset($_POST['submit'])){
	$time=time()-30;
	$ip_address=getIpAddr();
// Getting total count of hits on the basis of IP
	$query=mysqli_query($con,"select count(*) as total_count from loginlogs where TryTime > $time and IpAddress='$ip_address'");
	$check_login_row=mysqli_fetch_assoc($query);
	$total_count=$check_login_row['total_count'];
  //Checking if the attempt 3, or youcan set the no of attempt her. For now we taking only 3 fail attempted
	if($total_count==3){
		$msg="Too many failed login attempts. Please login after 30 sec";
	}else{
    //Getting Post Values
		$username=$_POST['username'];
		$password=md5($_POST['password']);
    // Coding for login
		$res=mysqli_query($con,"select * from user where username='$username' and  password='$password'");
		if(mysqli_num_rows($res)){
			$_SESSION['IS_LOGIN']='yes';
			mysqli_query($con,"delete from loginlogs where IpAddress='$ip_address'");

			echo "<script>window.location.href='dashboard.php';</script>";

		}else{
			$total_count++;
			$rem_attm=3-$total_count;
			if($rem_attm==0){
				$msg="Too many failed login attempts. Please login after 30 sec";
			}else{
				$msg="Please enter valid login details.<br/>$rem_attm attempts remaining";
			}
			$try_time=time();
			mysqli_query($con,"insert into loginlogs(IpAddress,TryTime) values('$ip_address','$try_time')");
		}
	}
}

// Getting IP Address
function getIpAddr(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ipAddr=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ipAddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ipAddr=$_SERVER['REMOTE_ADDR'];
	}
	return $ipAddr;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow">
	<title>Login Form</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	
</head>
<body>
	<div class="container" >
		
		<center>
			<div class="card col-md-8" style="margin-top: 30px;">
				<h3 class="text-center  pt-3 pb-1">Login form</h3>
				<h6 class="text-center  pt-2 pb-2">Username(admin), Password(1234)</h6>
				<div id="login-row" class="row justify-content-center align-items-center">
					<div id="login-column" class="col-md-6">
						<div id="login-box" class="col-md-12">
							<form id="login-form" class="form" method="post">
								<div class="form-group">
									<input type="text" name="username" placeholder="Username" id="username" class="form-control" required>
								</div>
								<div class="form-group">
									<input  type="password" name="password" placeholder="Password" id="password" class="form-control" required>
								</div>
								<div class="form-group">
									<input type="submit" name="submit" class="btn btn-info btn-md" value="Submit">
								</div>
								<div id="result" style="color: red;"><?php echo $msg?></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</center>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>