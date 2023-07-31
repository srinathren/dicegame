<?php

function playGame($currentPlayer, $diceValue, $gameState) {
    $size = 9;
    $position = array_search((int)$currentPlayer, $gameState);

    if($gameState[4]==12){
        $position = 4;
    }

    $position = (int) $position;

    if ($position + $diceValue <= $size - 1) {
        if ($gameState[4] == 12) {
            $gameState[4] = $currentPlayer == 1 ? 2 : 1;
        } else if ($gameState[0] == 12) {
            $gameState[0] = $currentPlayer == 1 ? 2 : 1;
        } else {
            $gameState[$position] = 0;
        }

        if ($position + $diceValue == 4) {
            $gameState[$position] = 0;
            if ($gameState[4] == 0) {
                $gameState[4] = $currentPlayer;
            } else {
                $gameState[4] = 12;
            }
        } else {
            if ($gameState[$position + $diceValue] == 1 || $gameState[$position + $diceValue] == 2) {
                $gameState[0] = $currentPlayer == 1 ? 2 : 1;
            }
            $gameState[$position + $diceValue] = $currentPlayer;
        }
    }

    return $gameState;
}

session_start(); 

if (isset($_SESSION['gameState'])) {
    $gameState = $_SESSION['gameState'];
} else {
    $gameState = array_fill(0, 9, 0);
    $gameState[0] = 12;
    $gameState['currentPlayer'] = 1;
}

if (!isset($gameState['currentPlayer'])) {
    $gameState['currentPlayer'] = 1;
}

if (!isset($gameState['currentDice'])) {
    $gameState['currentDice'] = 0;
}

if (isset($_POST['rollButton'])) {
    $diceValue = rand(1, 3);
    $gameState['currentDice'] = $diceValue;
    $currentPlayer = $gameState['currentPlayer'];
    $gameState = playGame($currentPlayer, $diceValue, $gameState);

    $gameState['currentPlayer'] = $currentPlayer == 1 ? 2 : 1;

    $_SESSION['gameState'] = $gameState;

    if ($gameState[8] == 1 || $gameState[8] == 2) {
        $winner = $gameState[8];
        $gameState = array_fill(0, 9, 0);
        $gameState[0] = 12;
        echo '<script>alert("Player ' . $winner . ' is the winner! and the dice value was '. $diceValue .'.");</script>';
        $gameState['currentPlayer'] = 1;
        $gameState['currentDice'] = 0;
        session_unset(); 
        session_destroy(); 
    }

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>PHP Game</h1>
    <?php
    echo '<h3> Player '.$gameState['currentPlayer'].' turn</h3>';
    ?>
    <div id="game-board">
        <?php
        function display($gameState) {
            $board = '<table>';
            $board .= '<tr>';
            for ($i = 7; $i >= 5; $i--) {
                $board .= '<td>' . $gameState[$i] . '</td>';
            }
            $board .= '</tr>';
            $board .= '<tr>';
            $board .= '<td class="safe-spot">' . $gameState[0] . '</td>';
            $board .= '<td class="finish">' . $gameState[8] . '</td>';
            $board .= '<td class="safe-spot">' . $gameState[4] . '</td>';
            $board .= '</tr>';
            $board .= '<tr>';
            for ($i = 1; $i <= 3; $i++) {
                $board .= '<td>' . $gameState[$i] . '</td>';
            }
            $board .= '</tr>';
            $board .= '</table>';
            echo $board;
        }

        display($gameState);

        if($gameState['currentDice']!=0 ){
            $player = $gameState['currentPlayer'] == 2 ? 1 : 2;
            echo 'Player ' . $player . ' rolled : ' . $gameState['currentDice'];
        }
        ?>
    </div>
    <form method="post">
        <input type="submit" name="rollButton" value="Roll Dice">
    </form>
</body>
</html>
