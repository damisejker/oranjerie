 <?php 
// auth & config
include "../_top.php";

header('Content-Type: text/html; charset=utf-8');

/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/

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


$names = $_SESSION['login'];
 ?>
 
<!DOCTYPE html>
<html>
    <head>
<meta http-equiv="Content-Type" content="text/html;  charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="language" content="ru" />
<!--[if lt IE 9]><script src="/html5.js"></script><![endif]-->
<meta name="description" content="Университет магических искусств, основанный в 2011 году" />
<link rel="stylesheet" href="https://magismo.ru/greenhouse/css/styles.css" media="screen">
<link rel="icon" href="https://magismo.ru/favicon.ico" type="image/x-icon" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link rel="stylesheet" id="dashicons-css" href="../castle_style/dashicons.css" type="text/css" media="all">
<link rel="canonical" href="https://magismo.ru/">
<link rel="shortlink" href="https://magismo.ru/">
<link href="https://magismo.ru/shops/css/hover.css" rel="stylesheet" media="all">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>



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



<div class="oranjerie" style="font-size:50px;color:#d1a11b;top: 0;right: 0;position:absolute" align="right">Оранжерея 
<br><span style="font-size:30px"><?=$names?></span>


</div>


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

    if(isset($_SESSION['login'])) {

// [MULTIPOT START] Автосоздание дефолтного горшка при первом входе
// Проверяем, есть ли у пользователя хотя бы один горшок
$sqlCheckPots = "SELECT COUNT(*) as pot_count FROM `pots` WHERE `login` = ?";
$stmtCheckPots = $conn->prepare($sqlCheckPots);
$stmtCheckPots->bind_param("s", $names);
$stmtCheckPots->execute();
$resultCheckPots = $stmtCheckPots->get_result();
$rowCheckPots = $resultCheckPots->fetch_assoc();

// Если у пользователя нет горшков, создаём дефолтный
if ($rowCheckPots['pot_count'] == 0) {
    $sqlCreateDefaultPot = "INSERT INTO `pots` (`login`, `name`, `pot_left`, `pot_top`) VALUES (?, 'Горшок', '50%', '80%')";
    $stmtCreateDefaultPot = $conn->prepare($sqlCreateDefaultPot);
    $stmtCreateDefaultPot->bind_param("s", $names);
    $stmtCreateDefaultPot->execute();
    $stmtCreateDefaultPot->close();
}
$stmtCheckPots->close();

// Привязываем старые растения без pot_id к дефолтному горшку
$sqlLinkOldPlants = "UPDATE `oranjerie` o
    INNER JOIN `pots` p ON o.`login` = p.`login`
    SET o.`pot_id` = p.`id`
    WHERE o.`pot_id` IS NULL
    AND o.`login` = ?
    AND p.`id` = (SELECT MIN(`id`) FROM `pots` WHERE `login` = ?)";
$stmtLinkOldPlants = $conn->prepare($sqlLinkOldPlants);
$stmtLinkOldPlants->bind_param("ss", $names, $names);
$stmtLinkOldPlants->execute();
$stmtLinkOldPlants->close();
// [MULTIPOT END]

// [MULTIPOT START] Обработчик добавления нового горшка
if (isset($_GET['add_pot']) && $_GET['add_pot'] == 'yes') {
    // Проверяем, сколько горшков уже есть у пользователя
    $sqlCountPots = "SELECT COUNT(*) as pot_count FROM `pots` WHERE `login` = ?";
    $stmtCountPots = $conn->prepare($sqlCountPots);
    $stmtCountPots->bind_param("s", $names);
    $stmtCountPots->execute();
    $resultCountPots = $stmtCountPots->get_result();
    $rowCountPots = $resultCountPots->fetch_assoc();
    $currentPotCount = $rowCountPots['pot_count'];
    $stmtCountPots->close();

    // Если горшков меньше 6, добавляем новый
    if ($currentPotCount < 6) {
        // Вычисляем позицию для нового горшка (смещаем вправо от центра)
        $offsetLeft = 10 + ($currentPotCount * 15); // каждый следующий горшок сдвигается на 15% вправо
        $newPotLeft = $offsetLeft . '%';
        $newPotTop = '80%';
        $newPotName = 'Горшок ' . ($currentPotCount + 1);

        $sqlAddPot = "INSERT INTO `pots` (`login`, `name`, `pot_left`, `pot_top`) VALUES (?, ?, ?, ?)";
        $stmtAddPot = $conn->prepare($sqlAddPot);
        $stmtAddPot->bind_param("ssss", $names, $newPotName, $newPotLeft, $newPotTop);
        $stmtAddPot->execute();
        $stmtAddPot->close();

        // Редирект для обновления страницы
        header("Location: index.php");
        exit();
    }
}
// [MULTIPOT END]

?>

<div style="font-size:50px;color:#d1a11b;top: 0;left: 0;" align="left">

<a href="https://magismo.ru/"><img src="https://magismo.ru/alchemy/elements/2737159.png" height="55"></a><br>

<a href="#" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/5811/5811606.png" height="55" title="Депозитарий"></a>



<?php
$date = date("Y-m-d", time());

//** ФУНКЦИЯ ПОЛИВА И УМЕНЬШЕНИЕ ПРОЦЕНТА  ***///

// Проверяем не поливали ли мы сегодня
	$sql = "SELECT * FROM `oranjerie` WHERE `login`='$names' ORDER BY `id`";
	$res = mysqli_query($conn, $sql);
	$rr = mysqli_fetch_array($res);
	$datewatered = $rr['datewatered'];
	$dateshuffled = $rr['dateshuffled'];
	$datesprayed = $rr['datesprayed'];
	$waterprocent = $rr['water'];
	$pl_stat = $rr['plantstatus'];
	$pl_stage = $rr['stagenumber'];
	$pl_name = $rr['plant'];
	$pl_total = $rr['totalstages'];
	$resistance = $rr['resistance'];
	

// Если мы поливали сегодня, предупреждаем
    if($datesprayed == $date) {
        // Истощаем здоровье на -25% если маг избыточно поливает растенье
        $spray = ", `health` = health-'25'";
    } elseif(isset($_GET['spray']) && $_GET['spray'] == "plant" and $pl_stat == 1) {
        $spray = ", `health` = health-'25'";
    }
    else {
        // иначе просто поливаем
        $spray = "";
    }

       // Если мы поливали сегодня, предупреждаем
    if($datewatered == $date) {
        // Истощаем здоровье на -25% если маг избыточно поливает растенье
        $water = ", `health` = health-'25'";
    } else {
        // иначе просто поливаем
        $water = "";
    }

// [MULTIPOT START] Обработчик полива с поддержкой pot_id
if (isset($_GET['water']) && $_GET['water'] == "plant") {
    // Получаем pot_id из GET или используем дефолтный горшок
    $potId = isset($_GET['pot_id']) ? intval($_GET['pot_id']) : null;

    if ($potId === null) {
        // Используем дефолтный горшок (первый по id)
        $sqlDefaultPot = "SELECT `id` FROM `pots` WHERE `login` = ? ORDER BY `id` LIMIT 1";
        $stmtDefaultPot = $conn->prepare($sqlDefaultPot);
        $stmtDefaultPot->bind_param("s", $names);
        $stmtDefaultPot->execute();
        $resultDefaultPot = $stmtDefaultPot->get_result();
        if ($rowDefaultPot = $resultDefaultPot->fetch_assoc()) {
            $potId = $rowDefaultPot['id'];
        }
        $stmtDefaultPot->close();
    }

    // полив
    $sqlWater = "UPDATE `oranjerie` SET `water` = '100', `datewatered`='$date' $water WHERE `login`=? AND `pot_id`=?";
    $stmtWater = $conn->prepare($sqlWater);
    $stmtWater->bind_param("si", $names, $potId);
    $stmtWater->execute();
    $stmtWater->close();

    echo "<script>alert('Растение полито!');</script>";
    echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();

    function poscrolim(){
        location.href='index.php';
    }
</script>";
}
// [MULTIPOT END]


//* Проверяем нет ли у нас функции поливки *//
	$sql = "SELECT `tid` FROM `depositarium` WHERE `login`='$names' and `tid`='353'";
	$res = mysqli_query($conn, $sql);
	
	// Если введенные данные уже есть в таблице
	if(mysqli_num_rows($res)) {
	    $sql = "SELECT * FROM `oranjerie` WHERE `login`='$names'";
     	$res = mysqli_query($conn, $sql);
	    $in = mysqli_fetch_array($res);
	    
	    $plantst = $in['plantstatus'];
	    $plantnn = $in['plant'];
	    $datewatered = $in['datewatered'];
	    $today = date("Y-m-d", time());

	    if($plantst == 1 or $plantst == 3) {
	     
	     if($datewatered == $today) {
        $areyousure = "data-confirm='Вы уже сегодня поливали. Вы уверены, что хотите полить растение ещё раз?  Будьте осторожны: избыточный полив может погубить растение.'";
        } else {
        // иначе просто поливаем
        $areyousure = "";
    }
	        
	        
	    echo '<br><a href="?water=plant" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/2157/2157654.png" height="55" title="Полить цветок" '.$areyousure.'></a>    ';
	    }
	    elseif($plantst == 2) {
	     $dead = "data-confirm='А какой смысл уже поливать? Ваше растение погибло.'";   
	        
	    echo '<br><a href="#" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/2157/2157654.png" height="55" title="Полить цветок" '.$dead.'></a>    ';
	    }
   
	} else {
	}
	//* Проверяем нет ли у нас функции поливки *//
	
	
// [MULTIPOT START] Обработчик опрыскивания с поддержкой pot_id
    if (isset($_GET['spray']) && $_GET['spray'] == "plant") {
        // Получаем pot_id из GET или используем дефолтный горшок
        $potIdSpray = isset($_GET['pot_id']) ? intval($_GET['pot_id']) : null;

        if ($potIdSpray === null) {
            $sqlDefaultPot = "SELECT `id` FROM `pots` WHERE `login` = ? ORDER BY `id` LIMIT 1";
            $stmtDefaultPot = $conn->prepare($sqlDefaultPot);
            $stmtDefaultPot->bind_param("s", $names);
            $stmtDefaultPot->execute();
            $resultDefaultPot = $stmtDefaultPot->get_result();
            if ($rowDefaultPot = $resultDefaultPot->fetch_assoc()) {
                $potIdSpray = $rowDefaultPot['id'];
            }
            $stmtDefaultPot->close();
        }

        // опрыскивание
        $sqlSpray = "UPDATE `oranjerie` SET `datesprayed`='$date', `plantstatus`='1' $spray WHERE `login`=? AND `pot_id`=?";
        $stmtSpray = $conn->prepare($sqlSpray);
        $stmtSpray->bind_param("si", $names, $potIdSpray);
        $stmtSpray->execute();
        $stmtSpray->close();

        $depo = "UPDATE `depositarium` SET `raz`=raz-'1' WHERE `tid`='2000' and `login`='$names'";
        mysqli_query($conn, $depo);

        echo "<script>alert('Растение опрыскано!');</script>";
        echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();

    function poscrolim(){
        location.href='index.php';
    }
</script>";
}
// [MULTIPOT END]
	
	
	//* Проверяем нет ли у нас функции опрыскивания растений *//
	$sql = "SELECT * FROM `depositarium` WHERE `login`='$names' and `tid`='2000' and `raz` != '0' or `login`='$names' and `tid`='419' and `raz` != '0'";
	$res = mysqli_query($conn, $sql);
	
	// Если введенные данные уже есть в таблице
	if(mysqli_num_rows($res)) {
	    $sql = "SELECT * FROM `oranjerie` WHERE `login`='$names'";
     	$res = mysqli_query($conn, $sql);
	    $in = mysqli_fetch_array($res);
	    
	    $plantst = $in['plantstatus'];
	    $plantnn = $in['plant'];
	    $today = date("Y-m-d", time());
	    $datesprayed = $in['datesprayed'];

	    if($plantst == 3) {
	        
	    if($datesprayed == $today) {
        $areyousurespray = "data-confirm='Вы уже сегодня опрыскивали растение. Вы уверены, что хотите опрыскать растение ещё раз?  Будьте осторожны: избыточное опрыскивание может погубить растение.'";
        } else {
        // иначе просто поливаем
        $areyousurespray = "";
    }
	            

	    echo '<br><a href="?spray=plant" id="myBtn"><img src="https://magismo.ru/greenhouse/images/repellent.png" height="55" title="Опрыскать цветок" '.$areyousurespray.'></a>    ';
	    }
	    elseif($plantst == 2) {
	     $dead = "data-confirm='А какой смысл уже опрыскивать? Ваше растение погибло.'";   
	        
	    echo '<br><a href="#" id="myBtn"><img src="https://magismo.ru/greenhouse/images/repellent.png" height="55" title="Опрыскать цветок" '.$dead.'></a>    ';
	    }
	    elseif($plantst == 1) {
	    
	     $dead = "data-confirm='А какой смысл опрыскивать если растение здоровое? Безцельное употребление может навредить растению.'";   
	        
	    echo '<br><a href="?spray=plant" id="myBtn"><img src="https://magismo.ru/greenhouse/images/repellent.png" height="55" title="Опрыскать цветок" '.$dead.'></a>    ';
	    }
   
	} else {
	}
	//* Проверяем нет ли у нас функции опрыскивания *//
	
	
	
// [MULTIPOT START] Обработчик удобрения с поддержкой pot_id
    if (isset($_GET['fertilize']) && $_GET['fertilize'] == "plant") {
        // Получаем pot_id из GET или используем дефолтный горшок
        $potIdFertilize = isset($_GET['pot_id']) ? intval($_GET['pot_id']) : null;

        if ($potIdFertilize === null) {
            $sqlDefaultPot = "SELECT `id` FROM `pots` WHERE `login` = ? ORDER BY `id` LIMIT 1";
            $stmtDefaultPot = $conn->prepare($sqlDefaultPot);
            $stmtDefaultPot->bind_param("s", $names);
            $stmtDefaultPot->execute();
            $resultDefaultPot = $stmtDefaultPot->get_result();
            if ($rowDefaultPot = $resultDefaultPot->fetch_assoc()) {
                $potIdFertilize = $rowDefaultPot['id'];
            }
            $stmtDefaultPot->close();
        }

        // удобрение
        $sqlFertilize = "UPDATE `oranjerie` SET `resistance`=resistance+'5', `health`='100' WHERE `login`=? AND `pot_id`=?";
        $stmtFertilize = $conn->prepare($sqlFertilize);
        $stmtFertilize->bind_param("si", $names, $potIdFertilize);
        $stmtFertilize->execute();
        $stmtFertilize->close();

        $depo = "UPDATE `depositarium` SET `used`='1' WHERE `tid`='418' and `login`='$names'";
        mysqli_query($conn, $depo);

        $thirdtur = "INSERT INTO `thirdtur` SET `name`='$names', `item` = 'Удобрение', `timefound`='".time()."', `turnir`='2', `otkuda`='$pl_stage'";
        mysqli_query($conn, $thirdtur);

        echo "<script>alert('Растение удобрено! Теперь растение устойчиво к паразитам в 5 раз!');</script>";
        echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();

    function poscrolim(){
        location.href='index.php';
    }
</script>";
}
// [MULTIPOT END]
	
		//* Проверяем нет ли у нас функции удобрения растений *//
	$sql = "SELECT * FROM `depositarium` WHERE `login`='$names' and `tid`='418' and `used` != '1'";
	$res = mysqli_query($conn, $sql);
	
	// Если введенные данные уже есть в таблице
	if(mysqli_num_rows($res)) {
	    $sql = "SELECT * FROM `oranjerie` WHERE `login`='$names'";
     	$res = mysqli_query($conn, $sql);
	    $in = mysqli_fetch_array($res);
	    
	    $plantst = $in['plantstatus'];
	    $plantnn = $in['plant'];

	    if($plantst == 1 or $plantst == 3) {

	    echo '<br><a href="?fertilize=plant" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/4284/4284880.png" height="55" title="Удобрить растение"></a>    ';
	    }
	    elseif($plantst == 2) {
	     $dead = "data-confirm='А какой смысл уже удобрять? Ваше растение погибло.'";   
	        
	    echo '<br><a href="#" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/4284/4284880.png" height="55" title="Удобрить растение" '.$dead.'></a>    ';
	    }
	   
   
	} else {
	}
	//* Проверяем нет ли у нас функции удобрения *//
	
	
	
// [MULTIPOT START] Обработчик удаления погибшего растения с поддержкой pot_id
        if (isset($_GET['plant']) && $_GET['plant'] == "dispose") {
            // Получаем pot_id из GET или используем дефолтный горшок
            $potIdDispose = isset($_GET['pot_id']) ? intval($_GET['pot_id']) : null;

            if ($potIdDispose === null) {
                $sqlDefaultPot = "SELECT `id` FROM `pots` WHERE `login` = ? ORDER BY `id` LIMIT 1";
                $stmtDefaultPot = $conn->prepare($sqlDefaultPot);
                $stmtDefaultPot->bind_param("s", $names);
                $stmtDefaultPot->execute();
                $resultDefaultPot = $stmtDefaultPot->get_result();
                if ($rowDefaultPot = $resultDefaultPot->fetch_assoc()) {
                    $potIdDispose = $rowDefaultPot['id'];
                }
                $stmtDefaultPot->close();
            }

           // функция порожнения горшка
           $sqlDispose = "DELETE FROM `oranjerie` WHERE `login`=? AND `pot_id`=?";
           $stmtDispose = $conn->prepare($sqlDispose);
           $stmtDispose->bind_param("si", $names, $potIdDispose);
           $stmtDispose->execute();
           $stmtDispose->close();

            echo "<script>alert('Погибшее растение высажено! Горшок пуст. Вы можете посадить следующее семя.');</script>";
            echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();

    function poscrolim(){
        location.href='index.php';
    }
</script>";

}
// [MULTIPOT END]   
	        
	        if($plantst == 1) { } 
	        elseif($plantst == 2) {
	        
	      // $message = "Сообщаем, что ваше растение $plantnn погибло! Предлагаем вам опорожнить горшочек. <a href=?plant=out style=color:red;>Опорожнить горшочек</a>";
	        
	       //echo "<script>alert('$message');</script>"; 
	       
	       $areyousure2 = "data-confirm='Вы уверены, что хотите опорожнить горшочек?'";
	       
	         echo '<br><a href="?plant=dispose" id="myBtn"><img src="images/8718055.png" height="55" title="Убрать погибшее растение" '.$areyousure2.'></a>    ';
	
	        }
	        else { }
	        
//* Проверяем статус цветка, даем возможность высадить *//
	$sql = "SELECT * FROM `oranjerie` WHERE `login`='$names'";
	$res = mysqli_query($conn, $sql);
	if(mysqli_num_rows($res)) {
if($pl_stage == $pl_total) {
    
    
  $plantdetails = "SELECT * FROM `plants` WHERE `name`='$pl_name'";
  $resde = mysqli_query($conn, $plantdetails);
  $rows = mysqli_fetch_array($resde);
  $imagelink = $rows['stage6'];
  
    

// [MULTIPOT START] Обработчик сбора урожая с поддержкой pot_id
	     if(isset($_GET['plant']) && $_GET['plant'] == "out") {
	         // Получаем pot_id из GET или используем дефолтный горшок
	         $potIdOut = isset($_GET['pot_id']) ? intval($_GET['pot_id']) : null;

	         if ($potIdOut === null) {
	             $sqlDefaultPot = "SELECT `id` FROM `pots` WHERE `login` = ? ORDER BY `id` LIMIT 1";
	             $stmtDefaultPot = $conn->prepare($sqlDefaultPot);
	             $stmtDefaultPot->bind_param("s", $names);
	             $stmtDefaultPot->execute();
	             $resultDefaultPot = $stmtDefaultPot->get_result();
	             if ($rowDefaultPot = $resultDefaultPot->fetch_assoc()) {
	                 $potIdOut = $rowDefaultPot['id'];
	             }
	             $stmtDefaultPot->close();
	         }

	         $dateadd = date("Y-m-d", time());

	     // функция добавления растения в депозитарий
		   $horlaer = "INSERT INTO `depositarium` SET `login`='$names', `date_add` = '$dateadd', `goodname`='$pl_name', `shop`='greenhouse', `picture`='$imagelink', `category`='plants'";
				mysqli_query($conn, $horlaer);

           // функция порожнения горшка
	   $sqlOut = "DELETE FROM `oranjerie` WHERE `login`=? AND `pot_id`=?";
	   $stmtOut = $conn->prepare($sqlOut);
	   $stmtOut->bind_param("si", $names, $potIdOut);
	   $stmtOut->execute();
	   $stmtOut->close();

				echo "<script>alert('Поздравляем! Вы собираете урожай. Растение убрано и помещено в ваш депозитарий! Горшок пуст. Вы можете посадить следующее семя.');</script>";
			    echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();

    function poscrolim(){
        location.href='index.php';
    }
</script>";

}
// [MULTIPOT END]
    
   echo "<br><a href='?plant=out' id='myBtn'><img src='https://magismo.ru/greenhouse/images/4284772.png' height='55' title='Собрать урожай'></a>";
   
    
} else {
    echo "";
}
	    


	}

// [MULTIPOT START] Кнопка добавления нового горшка
// Проверяем количество горшков у пользователя
$sqlCheckPotsCount = "SELECT COUNT(*) as pot_count FROM `pots` WHERE `login` = ?";
$stmtCheckPotsCount = $conn->prepare($sqlCheckPotsCount);
$stmtCheckPotsCount->bind_param("s", $names);
$stmtCheckPotsCount->execute();
$resultCheckPotsCount = $stmtCheckPotsCount->get_result();
$rowCheckPotsCount = $resultCheckPotsCount->fetch_assoc();
$userPotCount = $rowCheckPotsCount['pot_count'];
$stmtCheckPotsCount->close();

// Если горшков меньше 6, показываем кнопку добавления
if ($userPotCount < 6) {
    echo '<br><a href="?add_pot=yes" id="myBtn"><img src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png" height="55" title="Добавить горшок (у вас ' . $userPotCount . ' из 6)"></a>';
}
// [MULTIPOT END]

//** КОНЕЦ ФУНКЦИИ ПОЛИВА И УМЕНЬШЕНИЕ ПРОЦЕНТА  ***///
//** ЦИКЛЫ РАСТЕНИЙ (независимо от логина)  ***///







//Грузим изображения
	$stagesofusers = "SELECT * FROM `plants` WHERE `name`='$pl_name' ORDER BY `id`";
	$res = mysqli_query($conn, $stagesofusers);
	$rst = mysqli_fetch_array($res);
	$stage1 = $rst['stage1'];
    $stage2 = $rst['stage2'];
	$stage3 = $rst['stage3'];
	$stage4 = $rst['stage4'];
	$stage5 = $rst['stage5'];
	$stage6 = $rst['stage6'];
    
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


/*ПЕРВЫЙ ЦИКЛ - ЗДОРОВОЕ РАСТЕНИЕ*/
    if($pl_stage == 1 and $pl_stat == 1) {
        
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
        
   
    } 
    /*ПЕРВЫЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($pl_stage == 1 and $pl_stat == 2) {
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
    if($pl_stage == 2 and $pl_stat == 1) {

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
        
        
    } 
    /*ВТОРОЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($pl_stage == 2 and $pl_stat == 2) {
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
    elseif($pl_stage == 2 and $pl_stat == 3) {
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
    if($pl_stage == 3 and $pl_stat == 1) {

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
        
    } 
    /*ТРЕТИЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($pl_stage == 3 and $pl_stat == 2) {
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
    elseif($pl_stage == 3 and $pl_stat == 3) {
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
    if($pl_stage == 4 and $pl_stat == 1) {
        
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

        
    } 
    /*ЧЕТВЕРТЫЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($pl_stage == 4 and $pl_stat == 2) {
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
    elseif($pl_stage == 4 and $pl_stat == 3) {
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
    if($pl_stage == 5 and $pl_stat == 1) {
        
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
        
        
    }
    /*ПЯТЫЙ ЦИКЛ - СДОХШЕЕ РАСТЕНИЕ*/
    elseif($pl_stage == 5 and $pl_stat == 2) {
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
    elseif($pl_stage == 5 and $pl_stat == 3) {
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
    if($pl_stage == 6 and $pl_stat == 1) {
        
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
    elseif($pl_stage == 6 and $pl_stat == 2) {
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
    elseif($pl_stage == 6 and $pl_stat == 3) {
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
     
     $d = "SELECT DISTINCT `id`, `goodname`, `keyword`, `picture` FROM `depositarium` WHERE `login`='$names' and `used`!=1 and `category`='seeds' GROUP BY `goodname` ORDER BY `goodname` DESC";
    $obr = mysqli_query($conn, $d);
    if(mysqli_num_rows($obr)) {
 
    while($fetch = mysqli_fetch_assoc($obr)) {
    $tovar = $fetch['goodname'];
    $tovarid = $fetch['id'];
    $cc = $fetch['count'];
    $stages = $fetch['keyword'];
    $tovarp = $fetch['picture'];
    $initst = 1;
    $inithealth = 100;


    
// [MULTIPOT START] Обработчик посадки с выбором горшка
    if (isset($_POST['plant'.$tovarid])) {
		    $planter = $names;
		    $plantname = strip_tags($_POST['plantname']);
		    $selectedPotId = isset($_POST['pot_id_'.$tovarid]) ? intval($_POST['pot_id_'.$tovarid]) : 0;
		    $date = date("Y-m-d", time());

   // Проверяем, что выбран валидный горшок и он принадлежит пользователю
	$sqlCheckPot = "SELECT `id` FROM `pots` WHERE `id` = ? AND `login` = ?";
	$stmtCheckPot = $conn->prepare($sqlCheckPot);
	$stmtCheckPot->bind_param("is", $selectedPotId, $names);
	$stmtCheckPot->execute();
	$resultCheckPot = $stmtCheckPot->get_result();

	if($resultCheckPot->num_rows == 0) {
	    $error = "Выбран неверный горшок.";
	    $stmtCheckPot->close();
	} else {
	    $stmtCheckPot->close();

	    // Проверяем, что горшок свободен
	    $sqlCheckPlant = "SELECT `id` FROM `oranjerie` WHERE `login` = ? AND `pot_id` = ?";
	    $stmtCheckPlant = $conn->prepare($sqlCheckPlant);
	    $stmtCheckPlant->bind_param("si", $names, $selectedPotId);
	    $stmtCheckPlant->execute();
	    $resultCheckPlant = $stmtCheckPlant->get_result();

	    if($resultCheckPlant->num_rows > 0) {
	        $error = "Этот горшок уже занят другим растением.";
	        $stmtCheckPlant->close();
	    } else {
	        $stmtCheckPlant->close();

		   // обозначаем в депозитарии, что семечко посажено
		   $depo = "UPDATE `depositarium` SET `used` = '1' WHERE id='$tovarid'";
				mysqli_query($conn, $depo);

		   // заносим данные в базу оранжереи с pot_id
			$sqlPlant = "INSERT INTO `oranjerie` SET `login`=?, `pot_id`=?, `plant`=?, `health`=?, `water`='60', `dateplanted`=?, `waterhunger`=?, `totalstages`=?, `stagenumber`='0', `plantstatus`=?, `resistance`='5'";
			$stmtPlant = $conn->prepare($sqlPlant);
			$stmtPlant->bind_param("sisssssi", $planter, $selectedPotId, $plantname, $inithealth, $date, $date, $stages, $initst);
			$stmtPlant->execute();
			$stmtPlant->close();

			echo "<script language='javascript' type='text/javascript'>
    window.onLoad=poscrolim();

    function poscrolim(){
        location.href='https://magismo.ru/greenhouse/';
    }
</script>";
   	    }
   	}
}
// [MULTIPOT END]

// [MULTIPOT START] Форма с выбором горшка
   // Получаем все горшки пользователя
   $sqlGetPots = "SELECT p.id, p.name,
                  (SELECT COUNT(*) FROM `oranjerie` WHERE `pot_id` = p.id AND `login` = ?) as is_occupied
                  FROM `pots` p
                  WHERE p.`login` = ?
                  ORDER BY p.id";
   $stmtGetPots = $conn->prepare($sqlGetPots);
   $stmtGetPots->bind_param("ss", $names, $names);
   $stmtGetPots->execute();
   $resultPots = $stmtGetPots->get_result();

   $potOptions = "";
   while($potData = $resultPots->fetch_assoc()) {
       $potId = $potData['id'];
       $potName = $potData['name'];
       $isOccupied = $potData['is_occupied'] > 0;
       $disabled = $isOccupied ? "disabled" : "";
       $occupiedLabel = $isOccupied ? " (занят)" : " (свободен)";
       $potOptions .= "<option value='$potId' $disabled>$potName$occupiedLabel</option>";
   }
   $stmtGetPots->close();

   echo "<span class='box-shadow'><center>

   <img src='$tovarp' height='58'>

   <br><b> $tovar </b>

   <br>
   <form method='post'>
   <select name='pot_id_$tovarid' required style='margin: 5px 0; padding: 5px;'>
       <option value=''>Выберите горшок</option>
       $potOptions
   </select>
   <br>
   <button name='plant$tovarid' class='button button5'  data-confirm='Вы уверены, что хотите посадить это семя?'>Посадить</button>

   <input type='hidden' name='plantname' value='$tovar'>
   </form>
   </center>
   </span>
   &nbsp;";
// [MULTIPOT END]

    
    }
    }  else {
echo "<p>У вас пока нет семян в депозитарии. Отправьтесь <a href='https://magismo.ru/shops/oleander/seeds.html' target='_blank'>в лавку</a> за семенами.</p>"; 
    }
    
 
?>

    </div>
    
  </div>

</div>




<!-- [MULTIPOT START] Рендер нескольких горшков -->
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
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}

.pot {
  width: 18%;
  position: absolute;
  cursor: grab;
}
</style>

<div class="room">
<?php
// Получаем все горшки пользователя
$sqlGetAllPots = "SELECT * FROM `pots` WHERE `login` = ? ORDER BY `id`";
$stmtGetAllPots = $conn->prepare($sqlGetAllPots);
$stmtGetAllPots->bind_param("s", $names);
$stmtGetAllPots->execute();
$resultAllPots = $stmtGetAllPots->get_result();

// Рендерим каждый горшок в цикле
while ($potRow = $resultAllPots->fetch_assoc()) {
    $potId = $potRow['id'];
    $potName = $potRow['name'];
    $potLeft = $potRow['pot_left'];
    $potTop = $potRow['pot_top'];

    // Проверяем, есть ли растение в этом горшке
    $sqlGetPlant = "SELECT * FROM `oranjerie` WHERE `login` = ? AND `pot_id` = ?";
    $stmtGetPlant = $conn->prepare($sqlGetPlant);
    $stmtGetPlant->bind_param("si", $names, $potId);
    $stmtGetPlant->execute();
    $resultPlant = $stmtGetPlant->get_result();
    $plantRow = $resultPlant->fetch_assoc();

    // Мобильная подстройка
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $isMobile = strpos($userAgent, 'mobile');
    if ($isMobile !== false && $potId == 1) {
        $potLeft = '10%';
        $potTop = '50%';
    }

    ?>

    <div class="tooltip">
        <div class="pot" data-pot-id="<?=$potId?>" style="left: <?=$potLeft?>; top: <?=$potTop?>;">

        <?php if ($plantRow) {
            // Есть растение в горшке
            $plantname = $plantRow['plant'];
            $health = $plantRow['health'];
            $water = $plantRow['water'];
            $cvetstat = $plantRow['plantstatus'];
            $stagenum = $plantRow['stagenumber'];
            $healthperc = ($health < 0) ? 0 : $health;
            $waterperc = ($water < 0) ? 0 : $water;
        ?>

            <div class="plant"></div>

            <?php if($cvetstat == 3) { ?>
                <div class='infestedplant'></div>
            <?php } ?>

            <span class="tooltiptext">
                <span style='color:#4bb14f;text-transform: uppercase;'><?=$plantname?></span>
                <br>Стадия роста: <?=$stagenum?>

                <?php if($cvetstat == 3) { ?>
                    <div class='sick nostripes hvr-pulse-grow'>
                        <span style='width: <?=$healthperc?>%'></span>
                        <font style='font-size:11pt'>Здоровье <?=$healthperc?>%</font>
                        <img src='https://cdn-icons-png.flaticon.com/512/333/333661.png' height='15' title='Растение страдает от инфестации паразитами'>
                    </div>
                <?php } elseif($cvetstat == 2) { ?>
                    <div class='rotten nostripes hvr-pulse-grow'>
                        <span style='width: <?=$healthperc?>%'></span>
                        <font style='font-size:11pt'>Здоровье <?=$healthperc?>%</font>
                        <img src='https://cdn-icons-png.flaticon.com/512/983/983061.png' height='15' title='Растение погибло'>
                    </div>
                <?php } else { ?>
                    <div class='health nostripes hvr-pulse-grow'>
                        <span style='width: <?=$healthperc?>%'></span>
                        <font style='font-size:11pt'>Здоровье <?=$healthperc?>%</font>
                        <img src='https://cdn-icons-png.flaticon.com/512/1971/1971038.png' height='15' title='Растение здоровое'>
                    </div>
                <?php } ?>

                <div class="water nostripes hvr-pulse-grow">
                    <span style="width: <?=$waterperc?>%"></span>
                    <font style='font-size:11pt'>Полив <?=$waterperc?>%</font>
                    <img src='https://cdn-icons-png.flaticon.com/512/2114/2114534.png' height='15'>
                </div>
            </span>

        <?php } else { ?>
            <!-- Горшок пустой -->
            <span class="tooltiptext">
                <span style='color:#aaa;'>Горшок пуст</span>
                <br>Посадите семя из депозитария
            </span>
        <?php } ?>

        </div>
    </div>

    <?php
    $stmtGetPlant->close();
}
$stmtGetAllPots->close();
?>
</div>
<!-- [MULTIPOT END] -->

 

  

</div>
<?
 
}
}
?>

<!-- [MULTIPOT START] JavaScript для drag-and-drop нескольких горшков -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pots = document.querySelectorAll('.pot');

    pots.forEach(function(pot) {
        let offsetX = 0, offsetY = 0, drag = false;
        let currentPot = null;

        function startDrag(e) {
            drag = true;
            currentPot = pot;
            const rect = pot.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            offsetX = clientX - rect.left;
            offsetY = clientY - rect.top;

            // Convert offsets to percentages of the viewport to maintain consistency
            offsetX = (offsetX / window.innerWidth) * 100;
            offsetY = (offsetY / window.innerHeight) * 100;

            pot.style.cursor = 'grabbing';
        }

        function doDrag(e) {
            if (!drag || currentPot !== pot) return;
            e.preventDefault();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            // Convert the dragged position to percentages of the viewport
            const leftPercent = ((clientX - offsetX) / window.innerWidth) * 100;
            const topPercent = ((clientY - offsetY) / window.innerHeight) * 100;

            // Apply the new position in percentages
            pot.style.left = `${leftPercent}%`;
            pot.style.top = `${topPercent}%`;
        }

        function endDrag() {
            if (!drag || currentPot !== pot) return;
            drag = false;
            pot.style.cursor = 'grab';
            // Position is already in percentages, so you can save it directly
            const potId = pot.getAttribute('data-pot-id');
            savePotPosition(potId, pot.style.left, pot.style.top);
            currentPot = null;
        }

        pot.addEventListener('mousedown', startDrag);
        pot.addEventListener('touchstart', startDrag);

        document.addEventListener('mousemove', doDrag);
        document.addEventListener('touchmove', doDrag);

        document.addEventListener('mouseup', endDrag);
        document.addEventListener('touchend', endDrag);
        document.addEventListener('touchcancel', endDrag);
    });
});

function savePotPosition(potId, left, top) {
    const username = "<?= htmlspecialchars($names, ENT_QUOTES, 'UTF-8') ?>";

    fetch('save_pot_position.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `pot_id=${potId}&left=${left}&top=${top}&username=${username}`
    })
    .then(response => response.text())
    .then(data => {
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
</script>
<!-- [MULTIPOT END] -->

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