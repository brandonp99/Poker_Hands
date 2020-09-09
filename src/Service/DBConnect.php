<?php
  namespace App\Service;

  class DBConnect{

    public function createConnection(){
      $servername = "127.0.0.1";
      $username = "root";
      $password = "root";
      $DBname = "pokerhands";

      $conn = new \mysqli($servername, $username, $password, $DBname);

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      //echo "Connected successfully";

      return $conn;
    }

    public function addData($conn, $player1Hand, $player2Hand, $winner){
      $queryHand1 = $conn -> real_escape_string($player1Hand);
      $queryHand2 = $conn -> real_escape_string($player2Hand);

      if ($winner !== 0) {
        $findWinnerID = "SELECT id FROM players WHERE name = '$winner';";

        $winnerID = \mysqli_query($conn, $findWinnerID);

        if(!$winnerID){
          echo "Error: " . $findWinnerID . "<br>" . mysqli_error($conn);
        }

          $winnerID = implode(\mysqli_fetch_assoc($winnerID));

      }else{$winnerID = 0;}


      $roundsQuery = "INSERT INTO rounds (winner_id) VALUES ('$winnerID');";

      $roundsId = "SELECT id FROM rounds ORDER BY id DESC LIMIT 1;";

      if(!\mysqli_query($conn, $roundsQuery)){
        echo "Error: " . $roundsQuery . "<br>" . mysqli_error($conn);
      }

      $result = \mysqli_query($conn, $roundsId);

      if(!$result){
        echo "Error: " . $roundsQuery . "<br>" . mysqli_error($conn);
      }

      $result = implode(\mysqli_fetch_assoc($result));

      $player1Query = "INSERT INTO hands (round_id, player_id, hand) VALUES ('$result', '1', '$queryHand1');";

      $player2Query = "INSERT INTO hands (round_id, player_id, hand)VALUES ('$result', '2', '$queryHand2');";

      if(!\mysqli_query($conn, $player1Query)){
        echo "Error: " . $roundsQuery . "<br>" . mysqli_error($conn);
      }

      if(!\mysqli_query($conn, $player2Query)){
        echo "Error: " . $roundsQuery . "<br>" . mysqli_error($conn);
      }

    }

    public function insertHands($conn, $playerHands, $roundIds){

      $query = "INSERT INTO hands (round_id, player_id, hand) VALUES ";

      for ($i=0; $i < count($playerHands); $i++) {
        $playerId = $playerHands[$i][0];
        $roundId = $roundIds[($i == ( count($playerHands) - 1) ? round($i / 2) - 1 : round($i / 2))];
        $roundHands = $conn -> real_escape_string(json_encode($playerHands[$i][1]));
        $query .= "('$roundId', '$playerId', '$roundHands')". ($i == ( count($playerHands) - 1) ? "; " : ", ");
      }

      if(!\mysqli_query($conn, $query)){
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
      }
    }

    public function getPlayerIds($conn, $player1Name, $player2Name){
      $playerIDs = array();
      $player1Query = "SELECT id FROM players WHERE name = '$player1Name'";
      $player2Query = "SELECT id FROM players WHERE name = '$player2Name'";

      $player1ID = \mysqli_query($conn, $player1Query);
      $player2ID = \mysqli_query($conn, $player2Query);

      $playerIDs[0] = implode(\mysqli_fetch_assoc($player1ID));
      $playerIDs[1] = implode(\mysqli_fetch_assoc($player2ID));

      return $playerIDs;

    }

    public function insertRounds($conn, $winnerIds){

      $query = "INSERT INTO rounds (winner_id) VALUES ";

      for ($i=0; $i < count($winnerIds); $i++) {
        $query .= "('$winnerIds[$i]')". ($i == ( count($winnerIds) - 1) ? "; " : ", ");
      }


      $roundsId = "SELECT id FROM rounds ORDER BY id DESC LIMIT ".count($winnerIds).";";

      if(!\mysqli_query($conn, $query)){
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
      }

      $result = \mysqli_query($conn, $roundsId);

      if(!$result){
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
      }

      $roundIds = array();
      while ($row = \mysqli_fetch_assoc($result)) {
        array_push($roundIds, $row["id"]);
      }


      return $roundIds;

    }

    public function countWins($conn, $player){

      $findPlayerID = "SELECT id FROM players WHERE name = '$player';";

      $playerID = \mysqli_query($conn, $findPlayerID);

      if(!$playerID){
        echo "Error: " . $findPlayerID . "<br>" . mysqli_error($conn);
      }

        $playerID = implode(\mysqli_fetch_assoc($playerID));

      $playerWins = "SELECT count(*) FROM rounds WHERE winner_id = '$playerID'";

      $wins = \mysqli_query($conn, $playerWins);

      if(!$wins){
        echo "Error: " . $playerWins . "<br>" . mysqli_error($conn);
      }

        $wins = implode(\mysqli_fetch_assoc($wins));

        return $wins;

    }

    public function countDrawWins($conn, $drawWinID){
      $playerWins = "SELECT count(*) FROM rounds WHERE winner_id = '$drawWinID'";

      $wins = \mysqli_query($conn, $playerWins);

      if(!$wins){
        echo "Error: " . $playerWins . "<br>" . mysqli_error($conn);
      }

        $wins = implode(\mysqli_fetch_assoc($wins));

        return $wins;
    }
        }

 ?>
