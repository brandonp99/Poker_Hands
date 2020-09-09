<?php

  namespace App\Controller;

  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use App\Entity\Hands;
  use App\Form\uploadFile;
  use Symfony\Component\HttpFoundation\File\Exception\FileException;
  use Symfony\Component\HttpFoundation\File\UploadedFile;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\String\Slugger\SluggerInterface;
  use App\Service\FileUploader;
  use App\Service\DBConnect;
  use App\Service\ExamineCards;

  class pokerhands extends AbstractController
  {
    public function uploadFile(Request $request, FileUploader $fileUploader){
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $DBConnect = new DBConnect();
      $ExamineCards = new ExamineCards();
      $hands = new Hands();
      $form = $this->createForm(uploadFile::class, $hands);
      $form->handleRequest($request);
      $connection = $DBConnect->createConnection();

      //on forrm submit get file
      if($form->isSubmitted() && $form->isValid()){
        $handsFile = $form->get('hands')->getData();

        //if file is valid upload file and read
        if ($handsFile){
          $handsFilename = $fileUploader->upload($handsFile);
          $hands->setHandsFileName($handsFilename);

          $handle = fopen('%kernel.project_dir%/public/uploads/pokerHands/'.$handsFilename, "r");

          if($handle){

            $playerIDs = $DBConnect->getPlayerIds($connection, "Player 1", "Player 2");

            $winners = array();

            $allHands = [];


            while(($line = fgets($handle)) !== false){

              $playersHands = explode(' ', trim($line));

              $player1 = array_slice($playersHands, 0, 5);
              $player2 = array_slice($playersHands, 5, 5);

              $player1Rank = $ExamineCards->checkHands($player1);
              $player2Rank = $ExamineCards->checkHands($player2);

              if ($player1Rank == $player2Rank) {
                //$winner = 0;

                $player1NextBestCard = $ExamineCards -> checkNextBestCard($player1, $player1Rank);
                $player2NextBestCard = $ExamineCards -> checkNextBestCard($player1, $player2Rank);

                //draw wins set to 3 & 4 for players 1 & 2 respectively

                if ($player1NextBestCard > $player2NextBestCard) {
                  $winner = 3;
                }else {
                  $winner = 4;
                }


              }elseif ($player1Rank > $player2Rank) {
                $winner = $playerIDs[0];
              }else{$winner = $playerIDs[1];}

              array_push($winners, $winner);

              array_push($allHands, [$playerIDs[0], $player1]);
              array_push($allHands, [$playerIDs[1], $player2]);

            }



            $roundIds = $DBConnect->insertRounds($connection, $winners);

            $DBConnect->insertHands($connection, $allHands, $roundIds);
          }
        }

        $player1Wins = $DBConnect->countWins($connection, "Player 1");
        $player2Wins = $DBConnect->countWins($connection, "Player 2");
        $player1DrawWins = $DBConnect->countDrawWins($connection, 3);
        $player2DrawWins = $DBConnect->countDrawWins($connection, 4);

        return $this->render(
          "wins.html.twig",
          array(
            "player1Wins" => $player1Wins + $player1DrawWins,
            "player2Wins" => $player2Wins + $player2DrawWins,
            "player1DrawWins" => $player1DrawWins,
            "player2DrawWins" => $player2DrawWins
          )
);
      }

      return $this->render('base.html.twig', [
        'form' => $form->createView(),
      ]);
    }
  }

 ?>
