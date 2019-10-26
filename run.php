<?php
include ("function.php");
function nama()
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$ex = curl_exec($ch);
	// $rand = json_decode($rnd_get, true);
	preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
	return $name[2][mt_rand(0, 14) ];
	}
function register($no)
	{
	$nama = nama();
	$email = str_replace(" ", "", $nama) . mt_rand(100, 999);
	$data = '{"name":"' . $nama . '","email":"' . $email . '@gmail.co.id","phone":"+' . $no . '","signed_up_country":"ID"}';
	$register = request("/v5/customers", "", $data);
	//print_r($register);
	if ($register['success'] == 1)
		{
		return $register['data']['otp_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($register));
		return false;
		}
	}
function verif($otp, $token)
	{
	$data = '{"client_name":"gojek:cons:android","data":{"otp":"' . $otp . '","otp_token":"' . $token . '"},"client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e"}';
	$verif = request("/v5/customers/phone/verify", "", $data);
	if ($verif['success'] == 1)
		{
		return $verif['data']['access_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($verif));
		return false;
		}
	}
	function login($no)
	{
	$nama = nama();
	$email = str_replace(" ", "", $nama) . mt_rand(100, 999);
	$data = '{"phone":"+'.$no.'"}';
	$register = request("/v4/customers/login_with_phone", "", $data);
	//print_r($register);
	if ($register['success'] == 1)
		{
		return $register['data']['login_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($register));
		return false;
		}
	}
function veriflogin($otp, $token)
	{
	$data = '{"client_name":"gojek:cons:android","client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e","data":{"otp":"'.$otp.'","otp_token":"'.$token.'"},"grant_type":"otp","scopes":"gojek:customer:transaction gojek:customer:readonly"}';
	$verif = request("/v4/customers/login/verify", "", $data);
	if ($verif['success'] == 1)
		{
		return $verif['data']['access_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($verif));
		return false;
		}
	}
function claim($token)
	{
	$data = '{"promo_code":"GOFOODBOBA07"};
	$claim = request("/go-promotions/v1/promotions/enrollments", $token, $data);
	if ($claim['success'] == 1)
		{
		return $claim['data']['message'];
		}
	  else
		{
      save("error_log.txt", json_encode($claim));
		return false;
		}
	}
echo "###############################\n";
echo "##Thx to: DEWA SUPERATIKADEWA##\n";
echo "###############################\n";
echo "Choose Login or Register?"\n;
echo "Login = 1 & Register = 2:"; 
$type = trim(fgets(STDIN));
if($type == 2){
echo "PENDAFTARAN\n";
echo "MASUKKAN 62 For NOMOR INDONESIA and 1 For NOMOR AMERIKA SERIKAT\n";
echo "Enter Number: ";
$nope = trim(fgets(STDIN));
$register = register($nope);
if ($register == false)
	{
	echo "GAGAL MENDAPATKAN OTP, Use NOMOR YANG BELUM DIGUNAKAN UNTUK GOJEK!\n";
	}
  else
	{
	echo "Enter Your OTP: ";
	// echo "Enter Number: ";
	$otp = trim(fgets(STDIN));
	$verif = verif($otp, $register);
	if ($verif == false)
		{
		echo "GAGAL MENDAFTAR!\n";
		}
	  else
		{
		echo "SUKSES AMBIL VOUCHER\n";
		$claim = claim($verif);
		if ($claim == false)
			{
			echo "GAGAL AMBIL VOUCHER, ULANGI AMBIL VOUCHER Manual\n";
			}
		  else
			{
			echo $claim . "\n";
			}
		}
	}
}else if($type == 1){
echo "PENDAFTARAN\n";
echo "MASUKKAN 62 UNTUK NOMOR INDONESIA dan 1 UNTUK NOMOR AMERIKA SERIKAT\n";
echo "Enter Number: ";
$nope = trim(fgets(STDIN));
$login = login($nope);
if ($login == false)
	{
	echo "GAGAL AMBIL VOUCHER!\n";
	}
  else
	{
	echo "Enter Your OTP: ";
	// echo "Enter Number: ";
	$otp = trim(fgets(STDIN));
	$verif = veriflogin($otp, $login);
	if ($verif == false)
		{
		echo "GAGAL LOGIN DENGAN NOMORMU!\n";
		}
	  else
		{
		echo "SUKSES AMBIL VOUCHER\n";
		$claim = claim($verif);
		if ($claim == false)
			{
			echo "GAGAL AMBIL VOUCHER, ULANGI AMBIL VOUCHER Manual\n";
			}
		  else
			{
			echo $claim . "\n";
			}
		}
	}
}
?>
