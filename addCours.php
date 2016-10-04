<?php //zabezpieczenie przed wejście bez logowania
    session_start();
    
    if(!isset($_SESSION['loged'])){
        header('Location: index.php');
        exit();
    }

    $totFuel = 0;
    $totRoad = 0;
    
    require_once 'connect.php';
    
    //wyłuskiwanie do listy rozwijanej
    try{
        $valid=true;
         $conection = new mysqli($host, $db_user, $db_password, $db_name);
         
         $id = $_SESSION['id'];//id uzytkownika
         
          if($conection->connect_errno!=0){
             echo"Error: ".$conection->connect_errno;
         }else {
            
            $query = "SELECT * FROM cars WHERE userId = '$id'";
            
            if($valid==true){
                
                $result = $conection->query($query);
                
                $row = $result->fetch_assoc();
                
                $howMany = $result->num_rows;
                 for($i=1; $i<=$howMany; $i++){
                      $res = $conection->query("SELECT * FROM cars WHERE id = '$i' AND userId = '$id'");
                      $row2 = $res->fetch_assoc();
                      //samochód
                      $tabCar[$i] = $row2['mark'];
                      
                      //id
                      $tabId[$i] = $row2['id'];
						
                 }


            }else{
                throw new Exception($conection->errno);
            }
         }
         
         $conection->close();
         
    }  catch(Exceptione $e){
                 echo '<span class="error">Błąd serwera!</span>';
                 echo'</br>'; //deweloper infor
                 echo 'developer info: '.$e;
    }
    
    if(isset($_POST['from']) && isset($_POST['to']) && isset($_POST['distance']) 
            && isset($_POST['fuel']) && isset($_POST['cars'])){
        
        $valid=true;
        
        $mark = $_POST['cars'];
        
        $from = $_POST['from'];
        $to = $_POST['to'];
        $distance = $_POST['distance'];
        $fuel = $_POST['fuel'];
        $infoRoad = $_POST['infoRoad'];
        
        $userId = $_SESSION['id'];
        
          if(is_int($distance)){
                $valid=FALSE;
                $_SESSION['error_distance'] = "Podaj właściwą liczbę";
            }    
            
            if(is_float($fuel)){
                $valid=FALSE;
                $_SESSION['error_fuel'] = "Podaj liczbę całkowitą";
            }
            
            require_once 'connect.php';

              try{
                $conection = new mysqli($host, $db_user, $db_password, $db_name);
                if($conection->errno!=0){
                    throw new Exception(mysqli_connect_errno());
                } else {
                    
                    $valid=true;
                    
                    $carRes = $conection->query("SELECT * FROM cars WHERE "
                            . "mark = '$mark' AND userId = '$userId'");
                    
                    $row = $carRes->fetch_assoc();
                    
                    $carId = $row['id'];
                    
     // $query = "INSERT INTO cars(id, mark, capacity, yearProd, addInfo, userId)"
                  //    . "VALUES (NULL, '$mark', '$capacity', '$yearProd', '$infoCar', '$userId')";
                
                    
                    $query = "INSERT INTO course(id, from, to, distance, fuel, "
                            . "roadInfo, carId) VALUES  (NULL, '$from', '$to', "
                            . "'$distance', '$fuel', '$infoRoad', '$carId')";
                    
                  
                  $totFuel = $totFuel + $fuel;
                  $totRoad = $totRoad + $distance;
                    
                    if($valid==true){
                        
			if($conection->query($query)){
                        
                            $query2 = "INSERT INTO finalinfo(id, carId, totalFuelUsed, totalRoad)"
                            . "VALUES(NULL, '$carId', '$totFuel', '$totRoad')";        
                            if($conection->query($query2)){
                            
                                $_SESSION['coursAdd'] = "Trasa dodana";
                                    
                            }
			    
			} else {
                            throw new Exception($conection->errno);
                        }
                        
			$conection->close();			
                    }
                    
                    
                   
                }
              }catch(Exception $e){
                  echo '<span class="error">Błąd serwera!</span>';
                 echo'</br>'; //deweloper infor
                 echo 'developer info: '.$e;
              }
    
    }
?>

<!DOCTYPE html>
        
<html lang="pl">
    <head>
        
        
        <meta charset="UTF-8">
        
        <title>Aplikacja pozwalająca zapisać oraz sprawdzić ilość zużytego paliwa</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="paliwo spalanie pojazdy licznik kalkulator baza danych"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrom=1"/>
        
        <link rel="stylesheet" href="css/style.css" type="text/css"/>
        
          <script src="js/jquery.js"></script>
        <script type="text/javascript" src="js/slider.js"></script>
        
    </head>
    <body onload="change_slide()">
        <div class="container">
            
            <div id="header">
                <h1>LessFuel</h1>
                
                <div class="logingout">
                   <a href="interface.php">Strona główna</a>
                   <a href="addCar.php">Dodaj auto</a>
                    <a href="setEmail.php">Ustawienia</a>
                    <a href="logout.php">Wyloguj się</a>
                </div>
                
            </div>
            
            <div id="main_wall">
                
                <form method="post">
                    <div id="left_log">
                    <br/>
                    <br/>
                    <select id="textfield" name="cars">
                    
                    <?php 
                        for($i=1; $i<=$howMany; $i++){
                            echo '<option  name="cars">'.$tabCar[$i].'</option>';
                    }
                    
                    ?>  
                    
                    </select>
                   
                     <br/>
                    <br/>
                    Trasa z:
                    <br/>
                    <input type="text" id="textfield" name="from"/>
                    <br/>
                    <br/>
                    
                    <br/>
                    Trasa do:
                    <br/>
                    <input type="text" id="textfield" name="to"/>
                    <br/>
                    <br/>
                     
                    <br/>
                    Odległość:
                    <br/>
                    <input type="text" id="textfield" name="distance"/>
                    <br/>
                    <?php 
                         if(isset($_SESSION['error_distance'])){
                            echo '<div class="error">'.$_SESSION['error_distance'].'</div>'; 
                            unset($_SESSION['error_distance']);
                        }
                   
                    ?>
                    <br/>
                    
                    <br/>
                    Zużyte paliwo:
                    <br/>
                    <input type="text" id="textfield" name="fuel"/>
                    <br/>
                    <?php 
                         if(isset($_SESSION['error_fuel'])){
                            echo '<div class="error">'.$_SESSION['error_fuel'].'</div>'; 
                            unset($_SESSION['error_fuel']);
                        }
                   
                    ?>
                    <br/>
                   
                    <br/>
                    Dodatkowe informacje:
                    <br/>
                    <input type="text" id="textfield" name="infoRoad"/>
                    <br/>
                    <br/>
                     <?php 
                         if(isset($_SESSION['coursAdd'])){
                            echo '<div class="change">'.$_SESSION['coursAdd'].'</div>'; 
                            unset($_SESSION['coursAdd']);
                        }
						
			if(isset($_SESSION['error_road'])){
                            echo '<div class="error">'.$_SESSION['error_road'].'</div>'; 
                            unset($_SESSION['error_road']);
                        }
                   
                    ?>
                    <br/>
                    <br/>
                    <input type="submit" value="Dodaj" id="button"/>
                    </div>
                    </form>
                <div id="left_add">
                    <div id="photos"></div>
                </div>
                
                
                
            </div>
            
            <div id="footer">
               LessFuel &copy; Prawa zastrzeżone
               
               <div id="add">
                   Developed by Patryk Dróżdż
               </div>
               
               <div class="add2">

               </div>
               
               <div class="add2">
                   pdrozdz@onet.eu
               </div>
               
            </div>
            
        </div>
        
    </body>
</html>