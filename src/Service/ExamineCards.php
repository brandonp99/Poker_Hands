<?php
  namespace App\Service;

  class ExamineCards{



    public function checkHands($checkHand){

      $handRank;

      $suitUsage = array();
      $numberUsage = array();

      $highestSuitCount = 1;
      $highestNumberCount = 1;

      $card;
      $number;
      $suit;

      for ($i = 0; $i <= 4; $i++) {
        $card = str_split($checkHand[$i]);
        $suit[$i] = $card[1];

        //change high cards to number representation

        if($card[0] == 'T'){
          $number[$i] = 10;
        }elseif ($card[0] == 'J') {
          $number[$i] = 11;
        }elseif ($card[0] == 'Q') {
          $number[$i] = 12;
        }elseif ($card[0] == 'K') {
          $number[$i] = 13;
        }elseif ($card[0] == 'A') {
          $number[$i] = 14;
        }else {
          $number[$i] = intval($card[0]);
        }


        if (array_key_exists($suit[$i], $suitUsage)) {
          $suitUsage[$suit[$i]]++;
        } else {
          $suitUsage[$suit[$i]] = 1;
        }

        if (array_key_exists($number[$i], $numberUsage)) {

          $numberUsage[$number[$i] . ""]++;
        } else {
          $numberUsage[$number[$i] . ""] = 1;
        }

        if ($highestSuitCount < $suitUsage[$suit[$i]]) {
          $highestSuitCount = $suitUsage[$suit[$i]];
        }

        if ($highestNumberCount < $numberUsage[$number[$i] . ""]) {
          $highestNumberCount = $numberUsage[$number[$i] . ""];
        }
      }

      //hand ranks will start from 15 to accomodate for highest card in which case an Ace will be a 14
      sort($number);

      //check for Royal Flush, Straight Flush , straight & flush


        if ($this -> checkConsec($number)) {
          if ($highestSuitCount == 5) {
            if($number[0] == 10){
              //Royal Flush
              $handRank = 23;
            }else{
              //Straight Flush
              $highestCard = $number[4] / 100;

              $handRank = 22 + $highestCard;}
          }else{
            //straight
            $highestCard = $number[4] / 100;

            $handRank = 18 + $highestCard;}
        }elseif($highestSuitCount == 5){
          //flush
          $highestCard = $number[4] / 100;

          $handRank = 19 + $highestCard;
        }else{$handRank = $number[4];}

        if($number[0] == 2 && $number[4] == 14){
          $checkLowerAce = $number;
          $dump = \array_pop($checkLowerAce);
          if ($this -> checkConsec($checkLowerAce)) {
            if ($highestSuitCount == 5) {
              //Straight Flush - Lower Ace
              $highestCard = $number[4] / 100;

              $handRank = 22 + $highestCard;
            }else{
              //straight - Lower Ace
              $highestCard = $number[4] / 100;

              $handRank = 18 + $highestCard;
            }

          }
        }



      //check for three of a kind
      if ($highestNumberCount == 3) {
        $trippledCard = array_search($highestNumberCount,$numberUsage,true);
        $trippledCard = intval($trippledCard);

        $trippledCard = $trippledCard / 100;
        $handRank = 17 + $trippledCard;
      }

      //check for full house

      for ($i=0; $i <= 2; $i++) {
        if (count(array_keys($number, $number[$i])) == 3) {
          $threeOfAKindNum = $doubledCard = array_search($highestNumberCount,$numberUsage,true);
          $threeOfAKindNum = intval($threeOfAKindNum);

          $threeOfAKindNum = $threeOfAKindNum / 100;

          if (count(array_keys($number, $number[0])) == 2) {
            $handRank = 20 + $threeOfAKindNum;
          }elseif (count(array_keys($number, $number[4])) == 2) {
            $handRank = 20 + $threeOfAKindNum;
          }

        }
      }

      //check for pair
      if ($highestNumberCount == 2) {
        $doubledCard = array_keys($numberUsage, 2);
        $doubledCard = intval($doubledCard[0]);

        $doubledCard = $doubledCard / 100;

        $handRank = 15 + $doubledCard;
      }

      //check for two pair
      for ($i=0; $i < 3; $i++) {
        if (count(array_keys($number, $number[$i])) == 2) {
          if (count(array_keys($number, $number[$i+2])) == 2){

            $pairs = array_keys($numberUsage, 2);


            $pairs[0] = intval($pairs[0]);
            $pairs[1] = intval($pairs[1]);

            $pairs[0] = $pairs[0] / 100;
            $pairs[1] = $pairs[1] / 100;


            $handRank = 16 + $pairs[0] + $pairs[1];
          }
        }
      }


      //check for four of a kind
      if ($highestNumberCount == 4) {
        $quadrupledCard = array_search($highestNumberCount,$numberUsage,true);
        $quadrupledCard = intval($quadrupledCard);

        $quadrupledCard = $quadrupledCard / 100;
        $handRank = 21 + $quadrupledCard;
      }
      return $handRank;
          }

          public function checkNextBestCard($playerHand, $rank){
            $numberUsage = array();
            $number;
            $newHandRank;

            for ($i = 0; $i <= 4; $i++) {
              $card = str_split($playerHand[$i]);
              $suit[$i] = $card[1];

              if($card[0] == 'T'){
                $number[$i] = 10;
              }elseif ($card[0] == 'J') {
                $number[$i] = 11;
              }elseif ($card[0] == 'Q') {
                $number[$i] = 12;
              }elseif ($card[0] == 'K') {
                $number[$i] = 13;
              }elseif ($card[0] == 'A') {
                $number[$i] = 14;
              }else {
                $number[$i] = intval($card[0]);
              }


              if (array_key_exists($number[$i], $numberUsage)) {

                $numberUsage[$number[$i] . ""]++;
              } else {
                $numberUsage[$number[$i] . ""] = 1;
              }
            }

            sort($number);
            $roundedRank = round($rank);
            $rank = $rank * 100;
            $rankCardNum = $rank - ($roundedRank * 100);

            if ($rank > $number[4]) {
              $newHandRank = $number[4];
            }elseif ($rank == $number[4]) {
              $newHandRank = $number[3];
            }else{
              if ($rank == 15) {
                $keys = array_keys($numberUsage, 2);

                unset($number[$keys]);

                $newHandRank = $number[2];
              }elseif ($rank == 16) {
                $keys = array_keys($numberUsage, 2);

                unset($number[$keys[0]]);
                unset($number[$keys[1]]);

                $newHandRank = $number[0];
              }elseif($rank == 17){
                $keys = array_keys($numberUsage, 3);

                unset($number[$keys]);

                $newHandRank = $number[1];

              }elseif ($rank == 21) {
                $keys = array_keys($numberUsage, 4);

                unset($number[$keys]);

                $newHandRank = $number[0];
              }
            }


            return $newHandRank;
          }

      public function checkConsec($d) {
         for($i=0;$i<count($d);$i++) {
         if(isset($d[$i+1]) && $d[$i]+1 != $d[$i+1]) {
            return false;
         }
    }
    return true;
}
  }

 ?>
