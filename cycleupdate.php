 <?php 
// auth & config
// Данные БД

$servername = "localhost";
$username = "magismo_newera";
$password = "z65qdc3xmfq8";
$db = "magismo_school";

// Подключаемся к серверу
$conn = new mysqli($servername, $username, $password, $db);
mysqli_set_charset($conn,'utf8');

// Check connection
if ($conn->connect_error) {
  die("Ошибка подключения к базам данных: " . $conn->connect_error);
}
echo "";

ini_set('display_errors', 1);
error_reporting(E_ALL);

//** КРОН ЗАДАНИЕ НАЧАЛО  ***///
$isCronJob = (isset($argv[1]) && $argv[1] == 'cron');
if ($isCronJob) {
    
$stage = "SELECT * FROM `oranjerie` ORDER BY `id`";
$oranjerie = mysqli_query($conn, $stage);

if (!$oranjerie) {
    die("Error in query: " . mysqli_error($conn));
}

while ($rr = mysqli_fetch_array($oranjerie)) {
    $cycle = $rr['stagenumber'];
    $rastenie = $rr['plant'];
    $pl_stat = $rr['plantstatus'];
    $plantdate = $rr['dateplanted'];
    $stagedate = $rr['stagedatechange'];
    $rotdate = $rr['daterotten'];
    $date = date("Y-m-d", time());
    $dateshuffled = $rr['dateshuffled'];
    $pl_stage = $rr['stagenumber'];
    $resistance = $rr['resistance'];
    $waterprocent = $rr['water'];
    $total = $rr['totalstages'];
	
	/*прописываем болезнь*/
	 //если сегодня рандомайзер не кидали, бросаем
	 //рандом болезней не будет производиться для растений на 0,1 этапах, а также уже больных растениях
      if($dateshuffled !== $date and $pl_stage != 0 and $pl_stage != 1 and $pl_stat != 3 and $pl_stat != 2) { 
      
      $illness = 5; // число болезни
      $shuffle = rand(0, $resistance); // рандом от 0 до 5
      
	  // если рандомное число совпадает с загаданным, растение заболевает
	  if($shuffle == $illness and $pl_stage != 0 and $pl_stage != 1) {
	      $gettingsick = "UPDATE `oranjerie` SET `dateshuffled` = '$date',  `plantstatus` = '3' WHERE `plantstatus` != 3";
				mysqli_query($conn, $gettingsick);
	  } 
	  // если однако рандомное число не совпадает, растение не заболевает
	  else {
	      // обновляем в базе дату, что сегодня кидали рандом, завтра цикл повторится
	      $notgettingsick = "UPDATE `oranjerie` SET `dateshuffled` = '$date' WHERE `plantstatus` != 3 and `plant`='$rastenie'";
				mysqli_query($conn, $notgettingsick);
	  }
      } 
      //если сегодня рандомайзер кидали, ничего не делаем
      else { }
	/* КОНЕЦ - прописи болезни*/
	
		// Если процент полива на нуле или меньше
	if($waterprocent <= 0) {
	     $isto = "UPDATE `oranjerie` SET `daterotten` = '$date',  `plantstatus` = '2' WHERE `water`  <= 0";
				mysqli_query($conn, $isto);
	}
	
	
// Истощаем растеньице каждый день на -25%
      $sql = "SELECT `waterhunger`, `water`, `dateshuffled`, `datehealthchanged`, `plantstatus` FROM `oranjerie` ORDER BY `id`";
$result = mysqli_query($conn, $sql);

if ($result) {
    // Check if there are rows returned
    if ($rs = mysqli_fetch_array($result)) {
        $waterhunger = $rs['waterhunger'];
        $datehealth = $rs['datehealthchanged']; // Check the column name, it seems different from your query
        $statplant = $rs['plantstatus'];
        $waterperc = $rs['water'];

        // The rest of your code goes here
    } else {
        // Handle the case where no rows were returned
        echo "No rows found in the result set.";
    }

    // Don't forget to free the result set when you're done with it
    mysqli_free_result($result);
} else {
    // Handle the case where the query failed
    echo "Query failed: " . mysqli_error($conn);
}
	
	// если растение болеет, ухудшается показатель здоровья
	 if($statplant == 3) {
	  //Если сегодня ухудшение здоровья имело место, то ничего не делаем
        if($datehealth == $date) {
        } 
      
        //Иначе истощаем
        else {
        $istoh = "UPDATE `oranjerie` SET `health` = health-'25', `datehealthchanged`='$date'";
				mysqli_query($conn, $istoh);
        }
      } else {} 
	
	

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
		
	
	  //Если нулевой цикл
        if($plantdate !== $date and $stagedate !== $date and $cycle == 0) {
        //Переходим к первому циклу на следующий день
        $isto = "UPDATE `oranjerie` SET `stagenumber` = stagenumber+'1', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
				mysqli_query($conn, $isto);
        }

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

	// Проверяем до какой даты будет длится каждый цикл
	$firstcycle = date('Y-m-d', strtotime($plantdate. ' + '.$one.' days'));
	$secondcycle = date('Y-m-d', strtotime($firstcycle. ' + '.$two.' days'));
	$thirdcycle = date('Y-m-d', strtotime($secondcycle. ' + '.$three.' days'));
	$fourthcycle = date('Y-m-d', strtotime($thirdcycle. ' + '.$four.' days'));
	$fifthcycle = date('Y-m-d', strtotime($fourthcycle. ' + '.$five.' days'));
	$sixthcycle = date('Y-m-d', strtotime($fifthcycle. ' + '.$six.' days'));

    // Сверяем сегодняшнюю дату с ожидаемой датой перехода на следующий цикл

if ($cycle == 1 && $secondcycle <= $date) {
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '2', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
        mysqli_query($conn, $isto);
    } elseif ($cycle == 2 && $thirdcycle <= $date) {
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '3', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
        mysqli_query($conn, $isto);
    } elseif ($cycle == 3 && $fourthcycle <= $date) {
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '4', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
        mysqli_query($conn, $isto);
    } elseif ($cycle == 4 && $fifthcycle <= $date) {
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '5', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
        mysqli_query($conn, $isto);
    } elseif ($cycle == 5 && $total == 6 && $sixthcycle <= $date) {
        $isto = "UPDATE `oranjerie` SET `stagenumber` = '6', `stagedatechange`='$date' WHERE `plant`='$rastenie'";
        mysqli_query($conn, $isto);
    }


	}    
}  
//** КРОН ЗАДАНИЕ КОНЕЦ  ***///
?>