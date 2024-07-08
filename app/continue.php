<?php 

$domain = "http://localhost";



function jStrNum($i1,$i2,$i3=NULL) {
    if ($i3 != NULL) {
    return join("",array($i1,$i2,$i3));
    }
    return join("",array($i1,$i2));
}

function maxOf($i,$max) {
    if ($i > $max) {
        return $max;
    }
    return $i;
}

//position 3 is move type 0=attack,1=heal
$movelist = array( //TODO: replace with contract rpc call
    array(["Pinch", 15, 0], ["Hit",2,0],["Harden",5,1]),
    array(["Kick", 5,0], ["Stomp",7,0],["Roll",9,0],["Rest",2,1]),
    array(["Scratch",4,0], ["Hiss",0,0],["Bite",8,0],["Sunbathe",4,1]),
    array(["Pinch", 7, 0], ["Hit",4,0],["Harden",5,1]),
);
function showPlayerMoves($moves=array(["Scratch", 5,0], ["Stomp",7,0])) {
    $output = '';
    $size = sizeof($moves);
    sort($moves);
    for ($i = 0; $i < ($size>= 4 ? 4: $size); $i++) {
        $text = $moves[$i][0];
        $power = $moves[$i][1];
        $direction = $moves[$i][2];
        $output = jStrNum($output,jStrNum('<meta property="fc:frame:button:',jStrNum(jStrNum( $moves[$i][1] ,"00000"), $moves[$i][2]),jStrNum('" content="',$text,'" />\n')));
    }
    return $output;/*  '<meta property="fc:frame:button:1" content="666666" />
    <meta property="fc:frame:button:2" content="7777777" />
    <meta property="fc:frame:button:3" content="88888888" />
    <meta property="fc:frame:button:4" content="999999999" />
    '; */
}
 

function quit($msg) { 
  // Send a 500 Internal Server Error header
  header('HTTP/1.1 500 Internal Server Error');
  echo $msg; 
  exit; 
}

function isValidBoard($input) {
  // Use a regular expression to check if the input contains only numbers (0-9) and commas
  return preg_match('/^[0-9,]+$/', $input);
}

function isValidTurn($input) {
    // Use a regular expression to check if the input contains only numbers (0-1)
    return preg_match('/^[0-1]+$/', $input);
  }

function isValidId($input) {
    // Use a regular expression to check if the input contains only numbers (0-9999)
    return preg_match('/^[0-9999]+$/', $input);
  }
  
  function isValidHealth($input) {
    // Use a regular expression to check if the input contains only numbers (0-9999) and commas
    return preg_match('/^[0-9999,]+$/', $input);
  }

function unZeroArray($arr) { 
	return array_filter($arr, function ($value) {
    return $value !== 0;
	});
}

function minZero($num) {
    if ($num < 0) {
        return 0;
    }
    return $num;
}

function moveRight(&$board) { 
	for($i = 0; $i < 16; $i++) { 
		if(0 == ($i % 4)) { 
			$row = [$board[$i], $board[$i+1], $board[$i+2], $board[$i+3]]; 
			// make a new row with all numbers moved to the right
			$filtered = unZeroArray($row); 
			$zeros = array_fill(0, 4-count($filtered), 0);
			$newRow = array_merge($zeros,$filtered); 
			$board[$i] = $newRow[0]; 
			$board[$i+1] = $newRow[1]; 
			$board[$i+2] = $newRow[2]; 
			$board[$i+3] = $newRow[3]; 
		}
	}
}

function moveLeft(&$board) { 
	for($i = 0; $i < 16; $i++) { 
		if(0 == ($i % 4)) { 
			$row = [$board[$i], $board[$i+1], $board[$i+2], $board[$i+3]]; 
			// make a new row with all numbers moved to the right
			$filtered = unZeroArray($row); 
			$zeros = array_fill(0, 4-count($filtered), 0);
			$newRow = array_merge($filtered,$zeros); 
			$board[$i] = $newRow[0]; 
			$board[$i+1] = $newRow[1]; 
			$board[$i+2] = $newRow[2]; 
			$board[$i+3] = $newRow[3]; 
		}
	}
}

function moveUp(&$board) { 
	for($i = 0; $i < 4; $i++) { 
		$col = [$board[$i], $board[$i+4], $board[$i+8], $board[$i+12]]; 

		$filtered = unZeroArray($col); 
		$zeros = array_fill(0, 4-count($filtered), 0);
		$newCol = array_merge($filtered,$zeros); 
		$board[$i] = $newCol[0]; 
		$board[$i+4] = $newCol[1]; 
		$board[$i+8] = $newCol[2]; 
		$board[$i+12] = $newCol[3]; 
	}
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
	$boardArray = array_map('intval', $boardArray);
}


$enemy_id = 0;
if(!empty($_GET['eid'])) { 
  $enemy_id = $_GET['eid']; 
  if(!isValidId($enemy_id)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}
$user_id = 1;
if(!empty($_GET['id'])) { 
  $user_id = $_GET['id']; 
  if(!isValidId($user_id)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}
$turn = 1;
if(!empty($_GET['turn'])) { 
  $turn = $_GET['turn']; 
  if(!isValidTurn($turn)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}

$enemy_health = [100,100];
if(!empty($_GET['ehealth'])) { 
  // Create board from GET parameters
  $enemy_health_string = $_GET['ehealth']; 
  if(!isValidHealth($enemy_health_string)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
  $enemy_health = explode(',',$enemy_health_string); 
  if(count($enemy_health)!=2) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }

	$enemy_health = array_map('intval', $enemy_health);
}
$user_health = [100,100];
if(!empty($_GET['userhealth'])) { 
  // Create board from GET parameters
  $user_health_string = $_GET['userhealth']; 
  if(!isValidHealth($user_health_string)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
  $user_health = explode(',',$user_health_string); 
  if(count($user_health)!=2) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }

	$user_health = array_map('intval', $user_health);
}
function changeTurn() {
    if ($turn == 0) {
        $turn = 1;
    } else {
        $turn = 0;
    }
}

function moveCalc($uid,$mindex,$enemy=false) {
    sort($movelist);
    $moves = $movelist[$uid];
    sort($moves);
    $move = $moves[$btnIndex];
    $power = $move[1];

    if ($move[2] == 0) {
        //attack 
        $enemy_health[0]= minZero(($enemy ? $user_health : $enemy_health)[0]-$power);
    } else {
        //heal
        $user_health[0]= minZero(($enemy ? $enemy_health : $user_health)[0]+$power);
    } 
}


$message = "";
	// look for an action from the user and respond to it 
	if($_SERVER['REQUEST_METHOD'] === 'POST') {

        $message="hisifhwofhweoif";
		try { 
			$jsonData = file_get_contents('php://input');
			$data = json_decode($jsonData, true);  

			$btnIndex = strval($data['untrustedData']['buttonIndex']); 
            list($part1, $part2) = explode('00000', $btnIndex);
            

            

           
			/* switch($btnIndex) { 
                case 1:
                    if ($turn == 0) {
                        $turn = 1;
                    } else {
                        $turn = 0;
                    }
				case 2: 
					
                    if ($turn == 1) {
                        
                       $turn = 0;
                    } else {
                        $turn = 1;
                    }
                    
					break; 
				case 3: 

                   
                    if ($turn == 1) {
                        
                       $turn = 0;
                    } else {
                        $turn = 1;
                    }
                   
					break; 
				case 4: 
                    if ($turn == 1) {
                        
                        $turn = 0;
                     } else {
                         $turn = 1;
                     }
                    
					break; 
				default: 
					break; 
			}  */

            changeTurn();
            if ($turn == 0) {
                $enemy_id = 2;
                $moves = $movelist[$user_id];
                sort($moves);
                $move = $moves[$btnIndex-1];
                $power = $move[1];

                if ($move[2] == 0) {
                    //attack 
                    $enemy_health[0]= minZero($enemy_health[0]-$power);
                } else {
                    //heal
                    $user_health[0]= maxOf($user_health[0]+$power,$user_health[1]);
                } 
                $turn = 1;
            } else {

                $enemy_id = 3;
                $index = mt_rand(0,3);//change to max uid
                $moves = $movelist[$enemy_id];
                sort($moves);
                $move = $moves[$index-1];
                $power = $move[1];

                if ($move[2] == 0) {
                    //attack 
                    $user_health[0]= minZero($user_health[0]-$power);
                } else {
                    //heal
                    $enemy_health[0]= maxOf($enemy_health[0]+$power,$enemy_health[1]);
                } 
                
               // changeTurn();
            }
            
		}
		catch(Exception $e) { 
			quit($e); 
		}
	}

/*
$filePath = 'example.txt';

// Write data to the file
file_put_contents($filePath, $progress); 
*/

$boardString = implode(',',$boardArray); 
$enemy_health = implode(',',$enemy_health);
$user_health = implode(',',$user_health);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Playing FarcaMon</title>
		<meta property="og:title" content="Playing FarcaMon" />
		<meta property='og:image' content="<?=$domain?>/i.php?b=0,0,0,2,2,0,0,0,0,0,0,0,0,24,0,0&ehealth=<?=$enemy_health?>&userhealth=<?=$user_health?>&id=<?=$user_id?>&eid=<?=$enemy_id?>&message=<?=$message?>" />
		<meta property="fc:frame" content="vNext" />
		<meta property="fc:frame:image" content="<?=$domain?>/i.php?b=0,0,0,2,2,0,0,0,0,0,0,0,0,24,0,0&ehealth=<?=$enemy_health?>&userhealth=<?=$user_health?>&id=<?=$user_id?>&eid=<?=$enemy_id?>&message=<?=$message?>" />
		<?=
            $turn == 1 ?
              showPlayerMoves($movelist[$user_id])
            :
                //enemy turn
                 '
                <meta property="fc:frame:button:5" content="Continue" />
                ';
            
        ?>
       
		<meta property="fc:frame:post_url" content="<?=$domain?>/play.php?b=0,0,0,2,2,0,0,0,0,0,0,0,0,24,0,0&ehealth=<?=$enemy_health?>&userhealth=<?=$user_health?>&id=<?=$user_id?>&eid=<?=$enemy_id?>&turn=<?=$turn?>" />
		<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
      <style type="text/css">
        body { 
          display:flex;
          justify-content:center;
          align-items:center;
          height:100vh;
          background-color:#8465cb;
        }
        #page { 
          width:760px;
          background-color:#fff;
          border-radius:24px;
          padding:24px 36px;
        }
        h1+p { 
          margin-top:-1em;
          opacity:0.7; 
          font-weight:700; 
        }
      </style>
	</head>
	<body>
		<div id="page">
      <h1>2048 Frame</h1>
      <p>Fully playable 2048 in a Farcaster Frame</p>
			<p>Play it here: <a href="https://warpcast.com/m0nt0y4/0x8c27e729">warpcast.com/m0nt0y4/0x8c27e729</a></p>
      <p>Want to see the code? Go to: <a href="https://github.com/Montoya/2048frame/">github.com/Montoya/2048frame/</a></p>
      <p>It's open source (MIT License), feel free to use it for your own projects.</p>
    </div>
	</body>
</html>