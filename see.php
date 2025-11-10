  <?php 
 header('Content-Type: text/html; charset=utf-8');

// Подключаем конфиг
include "../config.php";

// Начинаем сессию
session_start();

// Выход
if (isset($_GET['exit'])) {
    // Удаляем сессии
    $_SESSION = array();
	session_destroy();
	
	// Удаляем кук
	setcookie("login", "", time()-3600);
	setcookie("id", "", time()-3600);
	
	header("Location: /index.php");
	/*
	// Оповещаем пользователя
	echo "Вы успешно вышли. Для перехода на главную страницу пройдите по <a href='../../index.html'>ссылке</a>.";
	
	// Завершаем сценарий
	exit();*/
}




// Вытаскиваем куки, если они есть
if(isset($_COOKIE['id']) && isset($_COOKIE['login'])) {
     // Для удобства создаем переменные для сессий
	$login = trim($_SESSION['login']);
	$id = $_SESSION['id'];
	
    // Создаем сессии пользователя - id и login
	$_SESSION['id'] = $_COOKIE['id'];
	$_SESSION['login'] = $_COOKIE['login'];
	
	// Обновляем куки, действительны в течение месяца ~30 дней
	setcookie("login", $_COOKIE['login'], time()+60*60*24*7*4);
	setcookie("id", $_COOKIE['id'], time()+60*60*24*7*4);
    
	//вписываем последний онлайн
	$sql = "UPDATE `users` SET `online` = '" . time() . "' WHERE `id` = '$id'";
        mysqli_query($conn, $sql) or die (mysqli_error());
        
    	//вписываем последний онлайн анимагу
	if($animag_approve == 1 and $animag_visibility == 1) {
	$sql = "UPDATE `animagus` SET `online` = '" . time() . "' WHERE `login` = '$login'";
        mysqli_query($conn, $sql) or die (mysqli_error()); }
    
} else {

// Иначе - производим авторизацию

// Если форма заполнена - пытаемся войти
if(!empty($_POST['auth'])) {
			// Защищаем код
			$gologin = strip_tags($_POST['logingo']);
			$gopassword = md5($_POST['password']);
	
	// Проверяем существование пользователя через БД
	$sql = "SELECT `id`, `password`, `dostup` FROM `users` WHERE `login` = '$gologin'";
	$res = mysqli_query($conn, $sql);
	
	// Если пользователь существует, продолжаем
	if(mysqli_num_rows($res)) {
		$rows = mysqli_fetch_array($res);
		$r_password = $rows['password'];
		$r_id = $rows['id'];
		$r_dost = $rows['dostup'];
			
		// Если профиль активен
		if($r_dost !== "-1") {
		
		// Если пароль совпадает
		if($gopassword == $r_password) {
		
		    // И последнее - доступ не должен быть меньше единицы
			//if($rows['dostup'] > 0) {
			
				// Создаем куки, действительны в течение месяца ~30 дней
					setcookie("login", $gologin, time()+60*60*24*7*4);
					setcookie("id", $r_id, time()+60*60*24*7*4);
                    setcookie("password", $r_password, time()+60*60*24*7*4);

					// Создаем сессии пользователя - id и login
			$_SESSION['id'] = $r_id;
			$_SESSION['login'] = $gologin;
			$_SESSION['password'] = $r_password;
			
		   // Оповещаем пользователя о возможности войти
	        	header("Location: https://". $_SERVER['HTTP_HOST'] ."/greenhouse/");
	        // echo "$_SESSION[login], Вы успешно вошли на сайт. Для продолжения пройдите по <a href='index.php'>ссылке</a>.";
            //} else $erorrs[] = "Ошибка доступа.";
		} else { $error .= "<span class='dashicons dashicons-welcome-comments'></span> Вы ввели неверный пароль.<br>"; }
	} else  { $error .= "<span class='dashicons dashicons-welcome-comments'></span> Ваш профиль канул в неактив. Увы, вход невозможен. <a href='https://magismo.ru/feedback.php?purpose=reenter'><u>Обратитесь к администрации</u></a>, если хотите восстановиться.<br>"; }
	} else  { $error .= "<span class='dashicons dashicons-welcome-comments'></span> Вы ввели неверный логин.<br>";  }
} 
}


// Часовой пояс
$zaprosik = "SELECT * FROM `users` WHERE `login`='$user'";
$obr = mysqli_query($conn, $zaprosik);
$spck = mysqli_fetch_array($obr);
$tz = $spck['timezone'];

if (empty($tz)) {
    date_default_timezone_set('Europe/Moscow');
    $year = 2022;
	$month = 02;
	$day = 18;
	$hour = 22;
	$min = 00;
	$sec = 00;

	$target = mktime($hour, $min, $sec, $month, $day, $year);
	$current = time();
	$difference = $target - $current;

	$rDay = floor($difference/60/60/24);
	$rHour = floor(($difference-($rDay*60*60*24))/60/60);
	$rMin = floor(($difference-($rDay*60*60*24)-$rHour*60*60)/60);
	$rSec = floor(($difference-($rDay*60*60*24)-($rHour*60*60))-($rMin*60));
}
else {
    date_default_timezone_set($tz);
    $year = 2022;
	$month = 02;
	$day = 18;
	$hour = 22;
	$min = 00;
	$sec = 00;

	$target = mktime($hour, $min, $sec, $month, $day, $year);
	$current = time();
	$difference = $target - $current;

	$rDay = floor($difference/60/60/24);
	$rHour = floor(($difference-($rDay*60*60*24))/60/60);
	$rMin = floor(($difference-($rDay*60*60*24)-$rHour*60*60)/60);
	$rSec = floor(($difference-($rDay*60*60*24)-($rHour*60*60))-($rMin*60));
}


 ?>
 
<!DOCTYPE html>
<html>
    <head>
<meta http-equiv="Content-Type" content="text/html;  charset=windows-1251" />
<meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">
<meta name="language" content="ru" />
<!--[if lt IE 9]><script src="/html5.js"></script><![endif]-->
<meta name="description" content="Университет магических искусств, основанный в 2011 году" />
<link rel="stylesheet" href="https://magismo.ru/greenhouse/css/styles.css" media="screen">
<link rel="icon" href="https://magismo.ru/favicon.ico" type="image/x-icon" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link rel="stylesheet" id="dashicons-css" href="../castle_style/dashicons.css" type="text/css" media="all">
<link rel="canonical" href="https://magismo.ru/">
<link rel="shortlink" href="https://magismo.ru/">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link href="https://magismo.ru/shops/css/hover.css" rel="stylesheet" media="all">



<title>Магисмо &middot; Оранжерея</title>
     
    </head>
    
<body onload="countdown();">



<script type="text/javascript" src="https://magismo.ru/greenhouse/js/effect.css"></script>

<style>
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  z-index: 1;
  bottom: 100%;
  left: 50%;
  margin-left: -200px;
  margin-bottom: 0px;
  /* Fade in tooltip - takes 1 second to go from 0% to 100% opac: */
  opacity: 0;
  transition: opacity 1s;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}

///////////

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: absolute; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 16px;
    background:url('https://magismo.ru/images/30117.jpg');
    background-size: cover;
    color: white;
}

.modal-body {
    padding: 2px 16px;
     height: 250px;
     overflow: auto;
}

.modal-footer {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
    float:left;
}
.box-shadow {
box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
border-radius:10px;
height: 150px;
width: 150px;
align-content: center;
display: inline-grid;
}

.plantname {
left: 46%;
position: absolute;
bottom: 38%;
font-size: 14pt;
color: #4e2f1a;
}

.plantstate {
left: 46%;
position: absolute;
bottom: 38%;
font-size: 14pt;
color: #4e2f1a;
}

.clearBoth { clear:both; }
progress {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  height: 1rem;
  overflow: hidden;
  font-size: .75rem;
  background-color: #e9ecef;
  border-radius: .25rem;
}

.progress-bar {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  color: #fff;
  text-align: center;
  background-color: #007bff;
  transition: width .6s ease;
    
}

*, ::after, ::before {
  box-sizing: border-box;
}

.health {
    box-sizing: content-box;
    height: 10px;
    position: absolute;
    margin: 0 0 -125px -478px;
    background: #555;
    border-radius: 15px;
    padding: 1px;
    box-shadow: inset 0 -1px 1px rgba(255, 255, 255, 0.3);
    width: 100%;
    bottom: 60px;
    left: 480px;
    color: #96d496;
}
.health > span {
  display: block;
  height: 100%;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  background-color: rgb(43, 194, 83);
  background-image: linear-gradient(
    center bottom,
    rgb(43, 194, 83) 37%,
    rgb(84, 240, 84) 69%
  );
  box-shadow: inset 0 2px 9px rgba(255, 255, 255, 0.3),
    inset 0 -2px 6px rgba(0, 0, 0, 0.4);
  position: relative;
  overflow: hidden;
 
}

.sick {
  box-sizing: content-box;
  height: 10px;
  position: absolute;
  margin: 0 0 -125px -478px;
  background: #555;
  border-radius: 15px;
  padding: 1px;
  box-shadow: inset 0 -1px 1px rgba(255, 255, 255, 0.3);
  width: 100%;
  bottom: 60px;
  left: 480px;
  color: #c29c2b;
}
.sick > span {
  display: block;
  height: 100%;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  background-color: rgb(194, 156, 43);
  background-image: linear-gradient(
    center bottom,
    rgb(194, 156, 43) 37%,
    rgb(84, 240, 84) 69%
  );	
  box-shadow: inset 0 2px 9px rgba(255, 255, 255, 0.3),
    inset 0 -2px 6px rgba(0, 0, 0, 0.4);
  position: relative;
  overflow: hidden;
}

.rotten {
  box-sizing: content-box;
  height: 10px;
  position: absolute;
  margin: 0 0 -125px -478px;
  background: #555;
  border-radius: 15px;
  padding: 1px;
  box-shadow: inset 0 -1px 1px rgba(255, 255, 255, 0.3);
  width: 100%;
  bottom: 60px;
  left: 480px;
  color: #ff6e9a;
}

.rotten > span {
  display: block;
  height: 100%;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  background-color: rgb(158, 6, 52);
  background-image: linear-gradient(
    center bottom,
    rgb(158, 6, 52) 37%,
    rgb(84, 240, 84) 69%
  );	
  box-shadow: inset 0 2px 9px rgba(255, 255, 255, 0.3),
    inset 0 -2px 6px rgba(0, 0, 0, 0.4);
  position: relative;
  overflow: hidden;
 
}

.water {
  box-sizing: content-box;
  height: 10px;
  position: absolute;
  margin: 0 0 -85px -478px;
  background: #555;
  border-radius: 15px;
  padding: 1px;
  box-shadow: inset 0 -1px 1px rgba(255, 255, 255, 0.3);
  width: 100%;
  bottom: 60px;
  left: 480px;
  color: #b0f4d4;
}
.water > span {
  display: block;
  height: 100%;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  background-color: rgb(43, 152, 194);
  background-image: linear-gradient(
    center bottom,
    rgb(43, 194, 83) 37%,
    rgb(84, 240, 84) 69%
  );
  box-shadow: inset 0 2px 9px rgba(255, 255, 255, 0.3),
    inset 0 -2px 6px rgba(0, 0, 0, 0.4);
  position: relative;
  overflow: hidden;
 
}
.water > span:after,
.health > span:after,
.sick > span:after,
.animate > span > span {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  background-image: linear-gradient(
    -45deg,
    rgba(255, 255, 255, 0.2) 25%,
    transparent 25%,
    transparent 50%,
    rgba(255, 255, 255, 0.2) 50%,
    rgba(255, 255, 255, 0.2) 75%,
    transparent 75%,
    transparent
  );
  z-index: 1;
  background-size: 50px 50px;
  animation: move 2s linear infinite;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  overflow: hidden;
}

.animate > span:after {
  display: none;
}

@keyframes move {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: 50px 50px;
  }
}

.orange > span {
  background-image: linear-gradient(#f1a165, #f36d0a);
}

.red > span {
  background-image: linear-gradient(#f0a3a3, #f42323);
}

.nostripes > span > span,
.nostripes > span::after {
  background-image: none;
}

#page-wrap {
  width: 490px;
  margin: 80px auto;
}


.button {
  background-color: #04AA6D; /* Green */
  border: none;
  color: white;
  padding: 16px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}

.button1 {
  background-color: white; 
  color: black; 
  border: 2px solid #04AA6D;
}

.button1:hover {
  background-color: #04AA6D;
  color: white;
}

.button2 {
  background-color: white; 
  color: black; 
  border: 2px solid #008CBA;
}

.button2:hover {
  background-color: #008CBA;
  color: white;
}

.button3 {
  background-color: white; 
  color: black; 
  border: 2px solid #f44336;
}

.button3:hover {
  background-color: #f44336;
  color: white;
}

.button4 {
  background-color: white;
  color: black;
  border: 2px solid #e7e7e7;
}

.button4:hover {background-color: #e7e7e7;}

.button5 {
  background-color: white;
  color: black;
  border: 2px solid #555555;
}

.button5:hover {
  background-color: #555555;
  color: white;
}
</style>





<?php
if (empty($_SESSION['login'])) {
?>
<div class='noauth'><h3>Пожалуйста, войдите в систему, чтобы начать взаимодействия в оранжерее.</h3>
    <?php 
    echo $error;
    ?>
    <form method='post'>
	    
  <p>
    <label>Ваш логин:<br></label>
    <input type='text' name='logingo' value="<?php if(isset($_COOKIE["login"])) { echo $_SESSION['login']; } ?>" id="login" required>

  </p>

  <p id="form-login-username">
    <label>Ваш пароль:<br></label>
    <input type="password" name="password" value="<?php if(isset($_COOKIE["password"])) { echo $_SESSION['password']; } ?>" id="password" required>
 </p>
<!--<br><p style="float:left;white-space: nowrap;">
    
    <input type="checkbox" name="remember" id="mijc" class='art'><label for="mijc">Запомнить меня</label>
</p>
<br>-->
<br>
<input type="submit" name="auth" value="Войти" class="art-button">


</form>
    
    </div>
    
<?php
} else {
    
if(@$_GET['user']) {
$user = strip_tags(mb_convert_encoding($_GET['user'], "Windows-1251", "utf-8"));   
$ex = explode(" ", $user);

$sex = "SELECT `sex` FROM `users` WHERE `login` = '$user'";
$result = mysqli_query($conn, $sex);
$res = mysqli_fetch_array($result);
$gender = $res['sex'];

// Function to detect Italian names (simplified)
function isItalianName($name) {
    // A basic check for Italian-sounding names; can be expanded as needed
    return mb_strpos($name, 'Джиа') !== false || mb_substr($name, -1) == "и";
}

// Function to convert a name to the genitive case
function convertToGenitive($name, $isFemale) {
    if ($isFemale) {
        if (mb_substr($name, -1) == "а") {
            return mb_substr($name, 0, -1) . "ы";
        } elseif (mb_substr($name, -1) == "я") {
            return mb_substr($name, 0, -1) . "и";
        } else {
            return $name;
        }
    } else {
        if (mb_substr($name, -1) == "й") {
            return mb_substr($name, 0, -1) . "я";
        } elseif (!isItalianName($name) && mb_substr($name, -1) != "а" && mb_substr($name, -1) != "е" && mb_substr($name, -1) != "и" && mb_substr($name, -1) != "о") {
            return $name . "а";
        } else {
            return $name;
        }
    }
}

$firstName = $ex[0];
$lastName = $ex[1];

if ($gender == 0) { // Male
    $firstName = convertToGenitive($firstName, false);
    // Avoid modifying last names that are Italian or don't follow Russian genitive case rules
    if (!isItalianName($lastName) && substr($lastName, -4) == "ский") {
        $lastName = substr_replace($lastName, "ого", -4);
    } elseif (!isItalianName($lastName) && substr($lastName, -1) != "а" && substr($lastName, -1) != "е" && substr($lastName, -1) != "и" && substr($lastName, -1) != "о") {
        $lastName = $lastName . "а";
    }
    $np = "$firstName $lastName";
} elseif ($gender == 1) { // Female
    $firstName = convertToGenitive($firstName, true);
    if (mb_substr($lastName, -4) == "ская") {
        $lastName = mb_substr($lastName, 0, -2) . "ой";
    }
    $np = "$firstName $lastName";
}

    
     
/*
<div style="font-size:50px;color:#d1a11b;top: 0;left: 0;" align="left">

<a href="#" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/5811/5811606.png" height="55" title="Депозитарий"></a>

*/
?> 


<div class="oranjerie" style="font-size:50px;color:#d1a11b;top: 0;right: 0;position:absolute" align="right">Оранжерея 
<br><span style="font-size:30px"><?=$np ?></span>


</div>

<div style="font-size:50px;color:#d1a11b;top: 0;left: 0;" align="left">
<a href="https://magismo.ru/"><img src="https://magismo.ru/alchemy/elements/2737159.png" height="55"></a><br>
<a href="/greenhouse"><img src="https://cdn-icons-png.flaticon.com/512/4346/4346273.png" height="55" title="Вернуться к себе в оранжерею"></a>
</div>


<?php
$date = date("Y-m-d", time());




//** ФУНКЦИЯ ПОЛИВА И УМЕНЬШЕНИЕ ПРОЦЕНТА  ***///
/*
// Проверяем не поливали ли мы сегодня
	$sql = "SELECT `datewatered`, `waterhunger`, `dateplanted`, `stagechangedate` FROM `oranjerie` WHERE `login`='$user'";
	$res = mysqli_query($conn, $sql);
	$rr = mysqli_fetch_array($res);
	$datewatered = $rr['datewatered'];
	
	// Если мы поливали сегодня, предупреждаем
    if($datewatered == $date) {
        $areyousure = "data-confirm='Вы уже сегодня поливали. Вы уверены, что хотите полить растение ещё раз?  Будьте осторожны: избыточный полив может погубить растение.'";
        // Истощаем здоровье на -25% если маг избыточно поливает растенье
        $water = ", `health` = health-'25'";
    } 
      
    // Истощаем растеньице каждый день на -25%
        $sql = "SELECT `waterhunger`, `water` FROM `oranjerie` ORDER BY `id`";
	$result = mysqli_query($conn, $sql);
	$rs = mysqli_fetch_array($result);
	$waterprocent = $rr['water'];
	$waterhunger = $rs['waterhunger'];
	$datehealth = $rs['datehealthchanged'];
	$statplant = $rs['plantstatus'];
	$waterperc = $rs['water'];
	
		// Если процент полива на нуле или меньше
	if($waterprocent <= 0) {
	     $isto = "UPDATE `oranjerie` SET `daterotten` = '$date',  `plantstatus` = '0' WHERE `water`  <= 0";
				mysqli_query($conn, $isto);
	}


        //Если сегодня истощение имело место, то ничего не делаем
        if($waterhunger == $date) {
        } 
        elseif($waterperc < 50 and $datehealth !== $date) {
             $isto = "UPDATE `oranjerie` SET `health` = health-'15', `datehealthchanged`='$date' WHERE `water` < 50";
				mysqli_query($conn, $isto);
        }
        //Иначе истощаем
        else {
        $isto = "UPDATE `oranjerie` SET `water` = water-'15', `waterhunger`='$date'";
				mysqli_query($conn, $isto);
        }
    
	 */

/*
if($_GET['water'] == "plant") {

// полив
		   $water = "UPDATE `oranjerie` SET `water` = '100', `datewatered`='$date' $water WHERE `login`='$user'";
				mysqli_query($conn, $water);
				echo "<script>alert('Растение полито!');</script>";
}


// Проверяем нет ли у нас функции поливки
	$sql = "SELECT `tid` FROM `depositarium` WHERE `login`='$user' and `tid`='353'";
	$res = mysqli_query($conn, $sql);
	
	
	
	
	// Если введенные данные уже есть в таблице
	if(mysqli_num_rows($res)) {
	    echo '<br><a href="?water=plant" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/2157/2157654.png" height="55" title="Полить цветок" '.$areyousure.'></a>    ';
	   
	} else {
	}
	*/
//** КОНЕЦ ФУНКЦИИ ПОЛИВА И УМЕНЬШЕНИЕ ПРОЦЕНТА  ***///


//** ЦИКЛЫ РАСТЕНИЙ (независимо от логина)  ***///


		$stage = "SELECT `stagenumber`, `plant`, `plantstatus` FROM `oranjerie` WHERE `login`= '$user' ORDER BY `id`";
	$res = mysqli_query($conn, $stage);
	while ($rr = mysqli_fetch_array($res)) {
	$cycle = $rr['stagenumber'];
	$rastenie = $rr['plant'];
	$plantstat = $rr['plantstatus'];
	}
	

	$details = "SELECT * FROM `oranjerie` WHERE `plant`='$rastenie'";
	$res = mysqli_query($conn, $details);
		while ($r = mysqli_fetch_array($res)) {
		    $plantdate = $r['dateplanted'];
		    $stagedate = $r['stagechangedate'];
		    $rotdate = $r['daterotten'];
		}
	
	  //Если нулевой цикл 
        /* if($plantdate !== $date and $stagedate !== $date and $cycle == 0) {
        //Переходим к первому циклу на следующий день
        $isto = "UPDATE `oranjerie` SET `stagenumber` = stagenumber+'1', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
				mysqli_query($conn, $isto);
        } */

     //Аккумулируем данные о том, сколько длится каждый цикл (в днях)
	$stagesettings = "SELECT * FROM `plants` WHERE `name`='$rastenie' ORDER BY `id`";
	$res = mysqli_query($conn, $stagesettings);
	$rst = mysqli_fetch_array($res);
	$one = $rst['stage1_duration'];
	$stage1 = $rst['stage1'];
	$two = $rst['stage2_duration'];
	$stage2 = $rst['stage2'];
	$three = $rst['stage3_duration'];
	$stage3 = $rst['stage3'];
	$four = $rst['stage4_duration'];
	$stage4 = $rst['stage4'];
	$five = $rst['stage5_duration'];
	$stage5 = $rst['stage5'];
	$six = $rst['stage6_duration'];
	$stage6 = $rst['stage6'];
	$total = $rst['totalstages'];

     //высота мушек летающих вокруг растения
    $fly2 = $rst['stage2_sickheight'];
    $fly3 = $rst['stage3_sickheight'];
    $fly4 = $rst['stage4_sickheight'];
    $fly5 = $rst['stage5_sickheight'];
    $fly6 = $rst['stage6_sickheight'];
    
    //местоположение растений - RIGHT VALUE
    $right1 = $rst['stage1_right'];
    $right2 = $rst['stage2_right'];
    $right3 = $rst['stage3_right'];
    $right4 = $rst['stage4_right'];
    $right5 = $rst['stage5_right'];
    $right6 = $rst['stage6_right'];
    
     //местоположение растений - bottom VALUE
    $bottom1 = $rst['stage1_bottom'];
    $bottom2 = $rst['stage2_bottom'];
    $bottom3 = $rst['stage3_bottom'];
    $bottom4 = $rst['stage4_bottom'];
    $bottom5 = $rst['stage5_bottom'];
    $bottom6 = $rst['stage6_bottom'];
    
    //размер растений 
    $size1 = $rst['stage1_size'];
    $size2 = $rst['stage2_size'];
    $size3 = $rst['stage3_size'];
    $size4 = $rst['stage4_size'];
    $size5 = $rst['stage5_size'];
    $size6 = $rst['stage6_size'];
	
	
	// Проверяем до какой даты будет длится каждый цикл
	$firstcycle = date('Y-m-d', strtotime($plantdate. ' + '.$one.' days'));
	$secondcycle = date('Y-m-d', strtotime($firstcycle. ' + '.$two.' days'));
	$thirdcycle = date('Y-m-d', strtotime($secondcycle. ' + '.$three.' days'));
	$fourthcycle = date('Y-m-d', strtotime($thirdcycle. ' + '.$four.' days'));
	$fifthcycle = date('Y-m-d', strtotime($fourthcycle. ' + '.$five.' days'));
	$sixthcycle = date('Y-m-d', strtotime($fifthcycle. ' + '.$six.' days'));

    // Сверяем сегодняшнюю дату с ожидаемой датой перехода на следующий цикл
    
/*ПЕРВЫЙ ЦИКЛ - ЗДОРОВОЕ РАСТЕНИЕ*/
    if($cycle == 1 and $plantstat == 1) {
        
     echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom1."%;
  bottom: 0;
  left: 25%;
  right: ".$right1."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage1."');
  height: ".$size1."%;
}
</style>";
        
        /*
        // если сегодня день когда 1 цикл может перейти на 2
        if($secondcycle <= $date) {
            //Переходим ко второму циклу
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '2', `stagedatechange`='$date' WHERE `plant`='$rastenie' ";
				mysqli_query($conn, $isto);
        } else {} */
    } 
    /*ПЕРВЫЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($cycle == 1 and $plantstat == 2) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom1."%;
  bottom: 0;
  left: 25%; 
  right: ".$right1."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage1."');
  height: ".$size1."%;
  -webkit-filter: grayscale(105%); /* Safari 6.0 - 9.0 */
   filter: grayscale(105%);
}
</style>";
    }
    
    /*ВТОРОЙ ЦИКЛ*/
    if($cycle == 2 and $plantstat == 1) {

         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom2."%;
  bottom: 0;
  left: 25%;
  right: ".$right2."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  height: ".$size2."%;
  background-image: url('".$stage2."');
}
</style>";        
        
        // если сегодня день когда 2 цикл может перейти на 3
       /* if($thirdcycle <= $date) {
            //Переходим ко второму циклу
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '3', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
				mysqli_query($conn, $isto);
        } else {}*/
    } 
    /*ВТОРОЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($cycle == 2 and $plantstat == 2) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom2."%;
  bottom: 0;
  left: 25%;
  right: ".$right2."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage2."');
  height: ".$size2."%;
  -webkit-filter: grayscale(105%); /* Safari 6.0 - 9.0 */
  filter: grayscale(105%);
}
</style>";
    }
    
    /*ВТОРОЙ ЦИКЛ - БОЛЬНОЕ РАСТЕНИЕ*/
    elseif($cycle == 2 and $plantstat == 3) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom2."%;
  bottom: 0;
  left: 25%;
  right: ".$right2."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  height: ".$size2."%;
  background-image: url('".$stage2."');
  -webkit-filter: sepia(65%); /* Safari 6.0 - 9.0 */
  filter: sepia(65%);
}


.infestedplant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: -5%;
  bottom: ".$fly2."%;
  left: 25%;
  right: ".$right2."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('https://magismo.ru/greenhouse/images/bugs.gif');
  height:25%;
  </style>
";

    }
    
    
    /*ТРЕТИЙ ЦИКЛ*/
    if($cycle == 3 and $plantstat == 1) {

echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom3."%;
  bottom: 0;
  left: 25%;
  right: ".$right3."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage3."');
  height: ".$size3."%;
}
</style>";              
        
        // если сегодня день когда 3 цикл может перейти на 4
       /* if($fourthcycle <= $date) {
            //Переходим ко второму циклу
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '4', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
				mysqli_query($conn, $isto);
        } else {}*/
    } 
    /*ТРЕТИЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($cycle == 3 and $plantstat == 2) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom3."%;
  bottom: 0;
  left: 25%;
  right: ".$right3."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage3."');
  height: ".$size3."%;
  -webkit-filter: grayscale(105%); /* Safari 6.0 - 9.0 */
  filter: grayscale(105%);
}
</style>";
    }
    
 /*ТРЕТИЙ ЦИКЛ - БОЛЬНОЕ РАСТЕНИЕ*/
    elseif($cycle == 3 and $plantstat == 3) {
         echo "<style>.plant {
 background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom3."%;
  bottom: 0;
  left: 25%;
  right: ".$right3."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage3."');
  height: ".$size3."%;
  -webkit-filter: sepia(65%); /* Safari 6.0 - 9.0 */
  filter: sepia(65%);
}


.infestedplant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$fly3."%;
  bottom: 0;
  left: 25%;
  right: ".$right3."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('https://magismo.ru/greenhouse/images/bugs.gif');
  height:35%;
  </style>
";
 }   
    
    /*ЧЕТВЁРТЫЙ ЦИКЛ*/
    if($cycle == 4 and $plantstat == 1) {
        
echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom4."%;
  bottom: 0;
  left: 26%;
  right: ".$right4."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage4."');
  height: ".$size4."%;
}
</style>";       

        // если сегодня день когда 4 цикл может перейти на 5
      /*  if($fifthcycle <= $date) {
            //Переходим ко второму циклу
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '5', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
				mysqli_query($conn, $isto);
        } else {}*/
    } 
    /*ЧЕТВЕРТЫЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($cycle == 4 and $plantstat == 2) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom4."%;
  bottom: 0;
  left: 26%;
  right: ".$right4."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage4."');
  height: ".$size4."%;
  -webkit-filter: grayscale(105%); /* Safari 6.0 - 9.0 */
  filter: grayscale(105%);
}
</style>";
    }
    
   /*ЧЕТВЕРТЫЙ ЦИКЛ - БОЛЬНОЕ РАСТЕНИЕ*/
    elseif($cycle == 4 and $plantstat == 3) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom4."%;
  bottom: 0;
  left: 26%;
  right: ".$right4."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage4."');
  height: ".$size4."%;
  -webkit-filter: sepia(65%); /* Safari 6.0 - 9.0 */
  filter: sepia(65%);
}


.infestedplant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$fly4."%;
  bottom: 0;
  left: 25%;
  right: ".$right4."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('https://magismo.ru/greenhouse/images/bugs.gif');
  height:45%;
  </style>
";
 }     
    
    /*ПЯТЫЙ ЦИКЛ*/
    if($cycle == 5 and $plantstat == 1) {
        
echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom5."%;
  bottom: 0;
  left: 25%;
  right: ".$right5."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage5."');
  height: ".$size5."%;
}
</style>";            
        
        // если сегодня день когда 5 цикл может перейти на финальный
       /* if($total == 6 and $sixthcycle <= $date) {
            //Переходим ко второму циклу
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '6', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
				mysqli_query($conn, $isto);
        } else {}*/
    }
    /*ПЯТЫЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($cycle == 5 and $plantstat == 2) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom5."%;
  bottom: 0;
  left: 25%;
  right: ".$right5."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage5."');
  height: ".$size5."%;
  -webkit-filter: grayscale(105%); /* Safari 6.0 - 9.0 */
  filter: grayscale(105%);
}
</style>";
    }
    
  /*ПЯТЫЙ ЦИКЛ - БОЛЬНОЕ РАСТЕНИЕ*/
    elseif($cycle == 5 and $plantstat == 3) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom5."%;
  bottom: 0;
  left: 25%;
  right: ".$right5."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage5."');
  height: ".$size5."%;
  -webkit-filter: sepia(65%); /* Safari 6.0 - 9.0 */
  filter: sepia(65%);
}

.infestedplant {
  background-repeat:no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$fly5."%;
  bottom: 0;
  left: 25%;
  right: ".$right5."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('https://magismo.ru/greenhouse/images/bugs.gif');
  height:45%;
  </style>
";
 }       
    
    /*ФИНАЛЬНЫЙ ШЕСТОЙ ЦИКЛ*/
    if($cycle == 6 and $plantstat == 1) {
        
echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom6."%;
  bottom: 0;
  left: 25%;
  right: ".$right6."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage6."');
  height: ".$size6."%;
}
</style>";            
    
        
    }
    /*ШЕСТОЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($cycle == 6 and $plantstat == 2) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom6."%;
  bottom: 0;
  left: 25%;
  right: ".$right6."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage6."');
  height: ".$size6."%;
  -webkit-filter: grayscale(105%); /* Safari 6.0 - 9.0 */
   filter: grayscale(105%);
}
</style>";
    }
    
      /*ШЕСТОЙ  ЦИКЛ - БОЛЬНОЕ РАСТЕНИЕ*/
    elseif($cycle == 6 and $plantstat == 3) {
         echo "<style>.plant {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$bottom6."%;
  bottom: 0;
  left: 25%;
  right: ".$right6."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('".$stage6."');
  height: ".$size6."%;
  -webkit-filter: sepia(65%); /* Safari 6.0 - 9.0 */
  filter: sepia(65%);
}

.infestedplant {
  background-repeat:no-repeat;
  background-size: contain;
  background-position: center center;
  position: absolute;
  top: ".$fly5."%;
  bottom: 0;
  left: 25%;
  right: ".$right5."%;
  margin: 0;
  display: inline-block;
  visibility: visible;
  background-image: url('https://magismo.ru/greenhouse/images/bugs.gif');
  height:45%;
  </style>
";
 }       
    


//** КОНЕЦ ЦИКЛОВ РАСТЕНИЙ  ***///
?>



   </div> 


<!-- Trigger/Open The Modal -->


<!-- The Modal -->
<div id="myModal" class="modal" style="display:none">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">x</span>
      <h2>Ваш депозитарий</h2>
    </div>
    <div class="modal-body">
   <?  
     
     $d = "SELECT DISTINCT `id`, `goodname`, `keyword`, `picture` FROM `depositarium` WHERE `login`='$user' and `used`!=1 and `category`='seeds' GROUP BY `goodname` ORDER BY `goodname` DESC";
    $obr = mysqli_query($conn, $d);
    if(mysqli_num_rows($obr)) {
 
    while($fetch = mysqli_fetch_assoc($obr)) {
    $tovar = $fetch['goodname'];
    $tovarid = $fetch['id'];
    $cc = $fetch['count'];
    $stages = $fetch['keyword'];
    $tovarp = $fetch['picture'];

    if(@$_POST['plant'.$tovarid.'']) {
		    $planter = $user;
		    $plantname = strip_tags($_POST['plantname']);
		    $date = date("Y-m-d", time());
		    
		    
   // Проверяем нет ли уже в горшочках места
	$sql = "SELECT `id` FROM `oranjerie` WHERE `login`='$user'";
	$res = mysqli_query($conn, $sql);
	// Если введенные данные уже есть в таблице
	if(mysqli_num_rows($res)) {
	    $error = "Горшок уже занят другим растением. ";
	   
	} else {
	
		    
		    
		    
		   // обозначаем в депозитарии, что семечко посажено
		   $depo = "UPDATE `depositarium` SET `used` = '1' WHERE id='$tovarid'";
				mysqli_query($conn, $depo);
		   
		   // заносим данные в базу оранжереи
			$sql = "INSERT INTO `oranjerie` SET `login`='$planter', `plant`='$plantname', `health`='100', `water`='0', `dateplanted`='$date', `waterhunger`='$date', `totalstages`='$stages', `stagenumber`='0', `plantstatus`='1'";
			mysqli_query($conn, $sql);
			echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();
    
    function poscrolim(){
        location.href='https://magismo.ru/greenhouse/';
    }
</script>";
   	}    
}

   echo "<span class='box-shadow'><center>
   
   <img src='$tovarp' height='58'> 
   
   <br><b> $tovar </b>
   
   <br>
   <form method='post'>
   <input type='submit' name='plant$tovarid' value='Посадить' class='art-button'  data-confirm='Вы уверены, что хотите посадить это семя?'>
   
   <input type='hidden' name='plantname' value='$tovar'>
   </form>
   </center>
   </span>
   &nbsp;";

    
    }
    }  else {
echo "<p>У вас пока нет семян в депозитарии. Отправьтесь <a href='https://magismo.ru/shops/oleander/seeds.html' target='_blank'>в лавку</a> за семенами.</p>"; 
    }
?>

    </div>
    
  </div>

</div>




<div class="room">
 
<div class="tooltip">
<?
// Проверяем нет ли уже в горшочках места
	$sql = "SELECT * FROM `oranjerie` WHERE `login`='$user'";
	$res = mysqli_query($conn, $sql);
     $stagenum = "нет";
     while($rows = mysqli_fetch_array($res)) {
     
     $plantname = $rows['plant'];
     $health = $rows['health'];
     $water = $rows['water'];
     $cvetstat = $rows['plantstatus'];
     $stagenum = $rows['stagenumber'];
     
     if($health < 0) { 
        $healthperc = 0; 
        
    } else { 
        $healthperc = $health;
    }
     }
     
     
// Fetch the pot position
$sql = "SELECT pot_left, pot_top FROM pot_positions WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
$isMobile = strpos($userAgent, 'mobile');

if ($row = $result->fetch_assoc()) {
    $potLeft = $row['pot_left'];
    $potTop = $row['pot_top'];

    // Adjust if a mobile device is detected and the saved positions are out of view
    if ($isMobile !== false) {
        // Example adjustment, you might need to fine-tune these
        $potLeft = '10%'; // Adjust for mobile
        $potTop = '50%'; // Adjust for mobile
    }
} else {
    // Default position if not set
    $potLeft = '25%';
    $potTop = '72%';
}    
     
  ?>

<div class="tooltip">
    <div class="pot" style="left: <?php echo $potLeft; ?>; top: <?php echo $potTop; ?>;"> 
    <?
    // Проверяем нет ли уже в горшочках места
	$sql = "SELECT `id` FROM `oranjerie` WHERE `login`='$user'";
	$res = mysqli_query($conn, $sql);
	// Если введенные данные уже есть в таблице
	if(mysqli_num_rows($res)) {
	 ?>  
	
  <!--  <div class="plantname"><?//$plantname?><br></div> -->
    
    
     <div class="plant"> </div>
     
    <? if($cvetstat == 3) {

	     echo "  <div class='infestedplant'> </div>";
    }
     ?>
   <span class="tooltiptext"><span style='color:#4bb14f;text-transform: uppercase;'><?=$plantname?></span>
    <br>Стадия роста: <?=$stagenum?>
    
     <?php
// Цветок болен
	 if($cvetstat == 3) {

	     //echo "  <div class='infestedplant'> </div>";
	     
	     echo "<div class='sick nostripes hvr-pulse-grow'>
	 <span style='width: ".$healthperc."%' class=''></span>
	 <font style='font-size:11pt'>Здоровье ".$healthperc."%</font> <img src='https://cdn-icons-png.flaticon.com/512/333/333661.png' height='15' title='Растение страдает от инфестации паразитами'>
	 </div>";
	 
	 } 
	 
	 elseif($cvetstat == 2) {

	  
	     
	     echo "<div class='rotten nostripes hvr-pulse-grow'>
	 <span style='width: ".$healthperc."%' class=''></span>
	 <font style='font-size:11pt'>Здоровье ".$healthperc."%</font> <img src='https://cdn-icons-png.flaticon.com/512/983/983061.png' height='15' title='Растение погибло'>
	 </div>";
	 
	 } 
	 // Цветок здоров
	 else {
	  echo "<div class='health nostripes hvr-pulse-grow'>
	 <span style='width: ".$healthperc."%'></span>
	 <font style='font-size:11pt'>Здоровье ".$healthperc."%</font> <img src='https://cdn-icons-png.flaticon.com/512/1971/1971038.png' height='15' title='Растение здоровое'>
	 </div>";
	 }
	 ?>

    
    
    <?php
    if($water < 0) { 
        $waterperc = 0; 
        
    } else { 
        $waterperc = $water;
    }
    ?>
    
    <div class="water nostripes hvr-pulse-grow">
	<span style="width: <?=$waterperc?>%"></span>
	 <font style='font-size:11pt'>Полив <?=$waterperc?>%</font> <img src='https://cdn-icons-png.flaticon.com/512/2114/2114534.png' height='15'>
    </div>
    <?php
	} else {
	}
	    ?>
    </span> 
   </div>
    
    
</div>   
   
</div>
</div>

 <!-- <span class="tooltiptext">
  <button id="myBtn">Посадить семя</button> <br>
  </span>-->
</div>

 

  

</div>
<?
 
} else
{}
    

} 

?>


<script>

// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<script>
function expandit(id){

  obj = document.getElementById(id);

  if (obj.style.display=='none') obj.style.display='';

  else obj.style.display='none';}
</script>

<script>
    $(document).on('click', ':not(form)[data-confirm]', function(e){
    if(!confirm($(this).data('confirm'))){
        e.stopImmediatePropagation();
        e.preventDefault();
    }
});

$(document).on('submit', 'form[data-confirm]', function(e){
    if(!confirm($(this).data('confirm'))){
        e.stopImmediatePropagation();
        e.preventDefault();
    }
});

$(document).on('input', 'select', function(e){
    var msg = $(this).children('option:selected').data('confirm');
    if(msg != undefined && !confirm(msg)){
        $(this)[0].selectedIndex = 0;
    }
});
</script>

<script>
function myFunction() {
  alert('Hello');
}
</script>

</body>
</html>