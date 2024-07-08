<?php 

$domain = "https://wowowmon.com";



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

function isEven($num) {
    return $num % 2 == 0;
}

/*
moves
0 Pinch
1 Hit
2 Harden
3 Kick
4 Stomp
5 Roll 
6 Rest
7 Scratch
8 Hiss
9 Bite
10 Sunbathe
11 Growl
12 Sunbeam
13 Photosynthesis
*/

$names = array("Crolix","Elepholo","Moltora","Green Mole",);
//position 3 is move type 0=attack,1=heal
$movelist = array( //TODO: replace with contract rpc call
    array([0,"Pinch", 25, 0], [1,"Hit",16,0],[2,"Harden",15,1],[7,"Scratch",17,0]),
    array([3,"Kick", 20,0], [4,"Stomp",14,0],[5,"Roll",36,0],[6,"Rest",15,1]),
    array([7,"Scratch",17,0], [8,"Hiss",0,0],[9,"Bite",20,0],[10,"Sunbathe",14,1]),
    array([11,"Growl", 3, 0], [1,"Hit",16,0],[13,"Photosynthesis",20,1],[12,"Sunbeam",33,0]),
);
//0 = health
$statlist = array(//TODO: replace with contract rpc call
    [70],
    [100],
    [80],
    [50]
);

function showPlayerMoves($moves=array([7,"Scratch", 5,0], [4,"Stomp",7,0])) {
    $output = '';
    $size = sizeof($moves);
    array_multisort($moves);
    for ($i = 0; $i < ($size>= 4 ? 4: $size); $i++) {
        $text = $moves[$i][1];
        $power = $moves[$i][2];
        $direction = $moves[$i][3];
        $output = jStrNum($output,jStrNum('<meta property="fc:frame:button:',jStrNum($i+1,'"',jStrNum('" content="',jStrNum($text,$direction == 0 ? "\n🗡️":"\n🛡️"),'" />\n'))));
    }
    return $output;/*  '<meta property="fc:frame:button:1" content="666666" />
    <meta property="fc:frame:button:2" content="7777777" />
    <meta property="fc:frame:button:3" content="88888888" />
    <meta property="fc:frame:button:4" content="999999999" />
    '; */
}

function showWinOrLose() {
    return  '<meta property="fc:frame:button:1" content="Play Again" />
    <meta name="fc:frame:button:1:action" content="post"/>
  
    ';
    /*   <meta property="fc:frame:button:2" content="Mint" />
<meta name="fc:frame:button:2:action" content="link"/>
<meta name="fc:frame:button:2:target" content="https://mint.fun/base/0xc5feA370c28F79673B46835041ED2b255bCDaDe8"/> */
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
    // Use a regular expression to check if the input contains only numbers (0-100)
    return preg_match('/^[0-9999]+$/', $input);
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



$enemy_id = 0;
if(!empty($_GET['eid'])) { 
  $enemy_id = $_GET['eid']; 
  if(!isValidId($enemy_id)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
} else {
    $enemy_id = rand(0, sizeof($names)-1);
}
$user_id = $enemy_id;
if(!empty($_GET['id'])) { 
  $user_id = $_GET['id']; 
  if(!isValidId($user_id)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}else {
    
    $user_id = $enemy_id;
    while ($user_id == $enemy_id) {
        $n = rand(0, sizeof($names)-1);
        if ($n != $enemy_id) {
            $user_id = $n;
        }
    }
}
$turn = 0;
if(!empty($_GET['turn'])) { 
  $turn = $_GET['turn']; 
  if(!isValidTurn($turn)) { 
    quit("500 Internal Server Error - Something went wrong."); 
  }
}

$enemy_health = [$statlist[$enemy_id][0],$statlist[$enemy_id][0]];//get wowowmons default health
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
$user_health = [$statlist[$user_id][0],$statlist[$user_id][0]];//get wowowmons default health
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

function isHealthZero($health=[100,100]) {
    if ($health[0] <= 0) {
        return true;
    }
    return false;
}

function moveCalc($uid,$mindex,$enemy=false) {
    $moves = $movelist[$uid];
    array_multisort($moves);
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


$message = jStrNum("A wild ",$names[$enemy_id]," attacked!");
	// look for an action from the user and respond to it 
	if($_SERVER['REQUEST_METHOD'] === 'POST') {

		try { 
			$jsonData = file_get_contents('php://input');
			$data = json_decode($jsonData, true);  

			$btnIndex = strval($data['untrustedData']['buttonIndex']); 
            list($part1, $part2) = explode('00000', $btnIndex);
            
            if (isGameOver($user_health, $enemy_health, $turn)) {
                

                //reset game
                $turn = 0;
                $enemy_id = rand(0, sizeof($names)-1);
                $user_id = $enemy_id;
                while ($user_id == $enemy_id) {
                    $n = rand(0, sizeof($names)-1);
                    if ($n != $enemy_id) {
                        $user_id = $n;
                    }
                }
                $enemy_health = [$statlist[$enemy_id][0],$statlist[$enemy_id][0]];
                $user_health = [$statlist[$user_id][0],$statlist[$user_id][0]];
                

            } else {

                if ($turn > 0) {
                    if (!isEven($turn)) {
                        //player turn
                        $moves = $movelist[$user_id];
                        array_multisort($moves);
                        $move = $moves[$btnIndex-1];
                        $power = $move[2];
        
                        $message = jStrNum($names[$user_id]," used ",jStrNum($move[1],"!"));
                        if ($move[3] == 0) {
                            //attack 
                            $enemy_health[0]= minZero($enemy_health[0]-$power);
                        } else {
                            //heal
                            $user_health[0]= maxOf($user_health[0]+$power,$user_health[1]);
                        } 
                    } else {
                        //enemy turn
                        $moves = $movelist[$enemy_id];
                        array_multisort($moves);
    
                        $index = rand(0,sizeof($moves)-1);//change to max uid
                        $move = $moves[$index];
                        $power = $move[2];
        
                        $message = jStrNum($names[$enemy_id]," used ",jStrNum($move[1],"!"));
                        if ($move[3] == 0) {
                            //attack 
                            $user_health[0]= minZero($user_health[0]-$power);
                        } else {
                            //heal
                            $enemy_health[0]= maxOf($enemy_health[0]+$power,$enemy_health[1]);
                        } 
                    }
                }
               
            
               
                $turn = $turn + 1;
            }

            
          /*   switch($btnIndex) { 
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
			
				default: 
                    if ($turn == 1) {
                            
                        $turn = 0;
                    } else {
                        $turn = 1;
                    }
					break; 
			}   

            changeTurn(); */
            
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

$enemy_health_imploded = implode(',',$enemy_health);
$user_health_imploded = implode(',',$user_health);

$url = $domain;
if ($turn >= 1) {
    if ($turn > 1) {
        if (isHealthZero($user_health)) {
            //lost
            $url = jStrNum($url,"/screens/lose.png");
        } else if (isHealthZero($enemy_health)) {
            //win
            $url = jStrNum($url,"/screens/win.png");
        } else {
            $url = jStrNum($url,"/i.php?ehealth=",jStrNum($enemy_health_imploded,"&userhealth=",jStrNum($user_health_imploded,"&id=",jStrNum($user_id,"&eid=", jStrNum($enemy_id,"&message=",urlencode($message))))));

        }
    } else {
        $url = jStrNum($url,"/i.php?ehealth=",jStrNum($enemy_health_imploded,"&userhealth=",jStrNum($user_health_imploded,"&id=",jStrNum($user_id,"&eid=", jStrNum($enemy_id,"&message=",urlencode($message))))));
    }
    
} else {
    $url = jStrNum($url,"/screens/start.gif");
}

function isGameOver($uhealth, $ehealth, $t) {
    return (isHealthZero($uhealth) || isHealthZero($ehealth)) && $t > 1;
}

function showButtons($turn, $movelist,$user_id,$user_health,$enemy_health) {
    if ($turn == 0) {
        return '<meta property="fc:frame:button:1" content="Start" />';
    }
    if (isGameOver($user_health,$enemy_health,$turn)) {
        return showWinOrLose();
    }
    if (!isEven($turn)) {
        return showPlayerMoves($movelist[$user_id]);
    } else {
        return '<meta property="fc:frame:button:1" content="Continue" />';
    }
}
?>
<!DOCTYPE html>
<html>
	<head>              		<title>Playing WOWOMON</title>
		<meta property="og:title" content="Playing WOWOWMON" />
        
		<meta property='og:image' content="<?=$url?>" />
		<meta property="fc:frame" content="vNext" />
		<meta property="fc:frame:image" content="<?=$url?>" />
		
        <?=
           showButtons($turn, $movelist,$user_id,$user_health,$enemy_health)
            
        ?>
       
		<meta property="fc:frame:post_url" content="<?=$domain?>/play.php?ehealth=<?=$enemy_health_imploded?>&userhealth=<?=$user_health_imploded?>&id=<?=$user_id?>&eid=<?=$enemy_id?>&turn=<?=$turn?>" />
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
      <h1>WOWOWMON</h1>
      <p>Battle Monsters in a Farcaster Frame</p>
			<p>Play it here: <a href="https://warpcast.com/roadu/0xbe52d4d6">Warpcast</a></p>
     
            <p>Created by <a href="https://warpcast.com/roadu">Roadu</a> based on <a href="https://github.com/Montoya/2048frame">2048 Frame</a>.
    </div>
	</body>
</html>