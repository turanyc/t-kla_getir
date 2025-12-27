<?php 
session_start();


if (isset($_POST['kullanici_giris'])) {
	

	if ($_POST['kadi']=="edukey" && $_POST['pass']=="123456") {
		
		$_SESSION['kadi']=$_POST['kadi'];
		$_SESSION['pass']=$_POST['pass'];

		if (isset($_POST['beni_hatirla'])) {

			//cookie atama işlemleri

			setcookie("kadi","edukey",strtotime("+1 day"));
			setcookie("pass","123456",strtotime("+1 day"));
			
		} else {

			//cookie sil...

			setcookie("kadi","edukey",strtotime("-1 day"));
			setcookie("pass","123456",strtotime("-1 day"));
		}

		header("Location:index.php");
		exit;
	} else {

		//giriş bilgileri doğru değilse
		header("Location:login.php");
		exit;
	}



}

 ?>