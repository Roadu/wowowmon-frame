<?php

putenv('GDFONTPATH=' . realpath('.'));

$names = array("Crolix","Elepholo","Moltora","Green Mole",);
//second forms: Crazenea,Hippofree,Salamplume
function quit($msg) { 
  // Send a 500 Internal Server Error header
  header('HTTP/1.1 500 Internal Server Error');
  echo $msg; 
  exit; 
}

function getNameFromId($id) {
  switch ($id) {
    case 3:
      return "Green Mole";
      break;
    case 2:
      return "Moltora";
      break;
    case 1:
        return "Elepholo";
        break;
    case 0:
      return "Crolix";
      break;
    default:
      return "Unknown";
      break;
  }
 
}

function getHealthColor($image, $health_percent) {
  if ($health_percent > 0.8) {//green
    return imagecolorallocate($image, 53, 213, 53);
  }
  if ($health_percent > 0.3) {//yellow
    return imagecolorallocate($image, 206, 206, 27);
  }
  return imagecolorallocate($image, 213, 53, 53);//red
}

function showEnemyInfo($image,$health,$id) {
  $pokemon_name = getNameFromId($id);
  $fontColor = imagecolorallocate($image, 132, 50, 203);
  $font = 'DejaVuSans'; // Change the font path as needed
  $progres = 1;//percentage filled ex 0.80
  if (sizeof($health) == 2) {
    $progres = $health[0]/$health[1];
  }
  //draw progres bar border
  $black_color = imagecolorallocate($image, 10, 35, 66);
  $progres_border_w = 120;
  $progres_border_h = 5;
  $progres_border_x = 50;
  $progres_border_y = 30;
  imagerectangle($image, $progres_border_x, $progres_border_y, $progres_border_x+$progres_border_w, $progres_border_y+$progres_border_h, $black_color);
  imagettftext($image, 8, 0, $progres_border_x-20, $progres_border_y+8, $fontColor, $font, 'HP:');//write hp
  //draw progres bar border
  $progres_bar_color = getHealthColor($image, $progres);//rgb image colors
  $progres_bar_w = ($progres_border_w*$progres)-2;
  $progres_bar_h = $progres_border_h-2;
  $progres_bar_x = $progres_border_x+1;
  $progres_bar_y = $progres_border_y+1;
  imagefilledrectangle($image, $progres_bar_x, $progres_bar_y, $progres_bar_x+$progres_bar_w, $progres_bar_y+$progres_bar_h, $progres_bar_color);
  //enemy name
  imagettftext($image, 16, 0, $progres_border_x+12, $progres_border_y-10, $fontColor, $font, $pokemon_name);
  
  $bottom_left_corner_offset_x = 25;
  //horizontal line
  imageline($image, $progres_bar_x-$bottom_left_corner_offset_x, $progres_border_y+10, $progres_border_x+$progres_border_w+3, $progres_border_y+10, $black_color);
  //vertical line
  imageline($image, $progres_bar_x-$bottom_left_corner_offset_x, $progres_border_y+10, $progres_bar_x-$bottom_left_corner_offset_x, $progres_border_y-5, $black_color);
  
}


function showUserInfo($image,$health, $id,$msg_x,$msg_y,$y=160) {
  $pokemon_name = getNameFromId($id);
  $fontColor = imagecolorallocate($image, 132, 50, 203);
  $font = 'DejaVuSans'; // Change the font path as needed
  $progres = 1;//percentage filled ex 0.80
  if (sizeof($health) == 2) {
    $progres = $health[0]/$health[1];
  }
  //draw progres bar border
  $black_color = imagecolorallocate($image, 10, 35, 66);
  $progres_border_w = 180;
  $progres_border_h = 10;
  $progres_border_x = 330;
  $progres_border_y = $y+60;
  imagerectangle($image, $progres_border_x, $progres_border_y, $progres_border_x+$progres_border_w, $progres_border_y+$progres_border_h, $black_color);
  imagettftext($image, 10, 0, $progres_border_x-24, $progres_border_y+10, $fontColor, $font, 'HP:');//write hp
  //draw progres bar border
  $progres_bar_color = getHealthColor($image, $progres);//rgb image colors
  $progres_bar_w = ($progres_border_w*$progres)-2;
  $progres_bar_h = $progres_border_h-2;
  $progres_bar_x = $progres_border_x+1;
  $progres_bar_y = $progres_border_y+1;
  imagefilledrectangle($image, $progres_bar_x, $progres_bar_y, $progres_bar_x+$progres_bar_w, $progres_bar_y+$progres_bar_h, $progres_bar_color);
  //enemy name
  imagettftext($image, 18, 0, $progres_border_x+45, $progres_border_y-10, $fontColor, $font, $pokemon_name);
  
  imagettftext($image, 18, 0, $progres_border_x+45, $progres_border_y+35, $black_color, $font, jStrNum($health[0]," / ",$health[1]));
  
  $bottom_left_corner_offset_x = -185;
  //horizontal line
  imageline($image, $progres_bar_x-30, $progres_border_y+40, $progres_border_x+$progres_border_w+5, $progres_border_y+40, $black_color);
  //vertical line
  imageline($image, $progres_bar_x-$bottom_left_corner_offset_x, $progres_border_y+40, $progres_bar_x-$bottom_left_corner_offset_x, $progres_border_y-5, $black_color);

  imagerectangle($image, $msg_x, $msg_y, $msg_x+$progres_border_w*3, $msg_y+$progres_border_h*4, $black_color);

}

function jStrNum($i1,$i2,$i3=NULL) {
  if ($i3 != NULL) {
  return join("",array($i1,$i2,$i3));
  }
  return join("",array($i1,$i2));
}

function isValidBoard($input) {
  // Use a regular expression to check if the input contains only numbers (0-9) and commas
  return preg_match('/^[0-9,]+$/', $input);
}
function isValidId($input) {
  // Use a regular expression to check if the input contains only numbers (0-9999)
  return preg_match('/^[0-9999]+$/', $input);
}

function isValidHealth($input) {
  // Use a regular expression to check if the input contains only numbers (0-9999) and commas
  return preg_match('/^[0-9999,]+$/', $input);
}

function isValidMessage($input) {
  //regex to check if input is a string between 0-20 charcters in length
  return true;//preg_match('/^(?=.{0,100}$)[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/',$input);
}

$boardArray = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];  
if(!empty($_GET['b'])) { 
  // Create board from GET parameters
  $boardString = $_GET['b']; 
  if(!isValidBoard($boardString)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
  $boardArray = explode(',',$boardString); 
  if(count($boardArray)!=16) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}

$enemy_id = 1;
if(!empty($_GET['eid'])) { 
  // Create board from GET parameters
  $enemy_id = $_GET['eid']; 
  if(!isValidId($enemy_id)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}
$user_id = 1;
if(!empty($_GET['id'])) { 
  // Create board from GET parameters
  $user_id = $_GET['id']; 
  if(!isValidId($user_id)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}

$enemy_health = [1,1];
if(!empty($_GET['ehealth'])) { 
  // Create board from GET parameters
  $enemy_health = $_GET['ehealth']; 
  if(!isValidHealth($enemy_health)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
  $enemy_health = explode(',',$enemy_health); 
  if(count($enemy_health)!=2) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}
$user_health = [1,1];
if(!empty($_GET['userhealth'])) { 
  // Create board from GET parameters
  $user_health = $_GET['userhealth']; 
  if(!isValidHealth($user_health)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
  $user_health = explode(',',$user_health); 
  if(count($user_health)!=2) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}

$message = "test";
if(!empty($_GET['message'])) { 
  // Create board from GET parameters
  $message = urldecode($_GET['message']); 
  $message = substr($message,0,strlen($message));
  if(!isValidMessage($message)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}

// Set the size of the board
$boardSize = 4;
$cellSize = 70;

// Create an empty image
$imageWidth = 573;
$imageHeight = 300;
$image = imagecreate($imageWidth, $imageHeight);

// Set background color
$backgroundColor = imagecolorallocate($image, 255, 253, 247);
imagefill($image, 0, 0, $backgroundColor);

// Set cell border color
$borderColor = imagecolorallocate($image, 10, 35, 66);


$fontColor = imagecolorallocate($image, 132, 50, 203);
$font = 'DejaVuSans'; // Change the font path as needed
// Draw the grid
/* for ($i = 0; $i < $boardSize; $i++) {
    for ($j = 0; $j < $boardSize; $j++) {
        $x1 = 10 + $j * $cellSize;
        $y1 = 10 + $i * $cellSize;
        $x2 = 10 + ($j + 1) * $cellSize;
        $y2 = 10 + ($i + 1) * $cellSize;

        // Draw the cell border
        imagerectangle($image, $x1, $y1, $x2, $y2, $borderColor);

        // Check if there is a value other than 0 at this cell
        $index = $i * 4 + $j; 
        if($boardArray[$index]!=0) { 
          $p = imagettfbbox(12, 0, $font, $boardArray[$index]);
          $halfwidth = intval(($p[2]-$p[0])/2); 
          imagettftext($image, 12, 0, $x1 + 35 - $halfwidth, $y1 + 42, $borderColor, $font, $boardArray[$index]); 
        }
    }
} */

// Draw the title 

//imagettftext($image, 16, 0, 30, 10, $fontColor, $font, 'Pokemon Name');
//imagettftext($image, 24, 0, 309, 250, $fontColor, $font, jStrNum('40302 Hame',$enemy_id));

function showOwnSprite($image,$user_id,$y=160) {
  //show owned sprite back
  $spriteurl = jStrNum("farcamon/",$user_id,"/back.png");
  /* TODO: show shadow instead of sprite in dead state
  if ($enemy_health[0] == 0) {
    $enemy_image = "images/shadow.png";
  } */
  list($enemywidth,$enemyheight) = getimagesize($spriteurl);
  $sprite = @imagecreatefrompng($spriteurl);//@ supresses a error

  $scaledsprite = imagescale($sprite, 200,145);
  imagecopymerge($image, $scaledsprite, 50, $y-38, 0, 0, 200, 145, 100);
  // Free up memory
  imagedestroy($sprite);
  imagedestroy($scaledsprite);
}

function showEnemySprite($image,$enemy_id) {
  $enemy_image = jStrNum("farcamon/",$enemy_id,"/front.png");
  list($enemywidth,$enemyheight) = getimagesize($enemy_image);
  $enemy = @imagecreatefrompng($enemy_image);
  $scaledenemy = imagescale($enemy, 200,125);
  imagecopymerge($image, $scaledenemy, 315, 30, 0, 0, 200,125, 100);
  // Free up memory
  imagedestroy($enemy);
  imagedestroy($scaledenemy);
}

$msg_x_start = 15;
$msg_y_start = 248;
function lowerFighter($image,$health,$id,$msg_x,$msg_y,$y=160) {
  showUserInfo($image,$health,$id,$msg_x,$msg_y,$y);
  showOwnSprite($image,$id,$y);
}


//showEnemyInfo($image, $enemy_health,$enemy_id);

//lowerFighter($image,$user_health,$user_id,$msg_x_start,$msg_y_start, 140);
//showEnemySprite($image,$enemy_id,);


$black_color = imagecolorallocate($image, 10, 35, 66);
imagettftext($image, 15, 0, $msg_x_start+5, $msg_y_start+25, $fontColor, $font, $message);

// Save the image to a file (you can also use imagepng, imagejpeg, etc.)
imagepng($image, 'world.png');

// Ignore
/* 
$identifier = implode('-',$boardArray); 
imagepng($image, 'b'.$identifier.'png');
*/

// Output the image to the browser
header('Content-Type: image/png');
imagepng($image);

// Free up memory
imagedestroy($image);

?>