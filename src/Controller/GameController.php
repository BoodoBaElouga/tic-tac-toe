<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private $gameState;
    /**
     * @var \int[][]
     */
    private array $winningConditions;

    /**
     * @return mixed
     */
    public function getGameState()
    {
        return $this->gameState;
    }


    #[Route('/', name: 'Tic Tac Toe')]
    public function homePage(): Response
    {
        return $this->render('game/homepage.html.twig');
    }

    #[Route('/play', name: 'init_game')]
    public function initGame(): Response
    {
        $this->gameState = array("", "", "", "", "", "", "", "", "");
        $this->winningConditions = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6]
        ];
        return $this->render('game/playingfield.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player1"]);
    }

    #[Route('/manager', name: 'game_manager')]
    public function gameManager(Request $request): Response
    {
        $typedCell = $request->request->get('typed_cell');
        $player = $request->request->get('player');

        // prüfen, ob der Spielzug zulläsig ist
        if ($this->handleCellPlayed($this->getGameState(), intval($typedCell), $player)) {
            if ($player === "Player1") {
                $response = $this->render('game/bottomGameInfo.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player2"]);
                $response->setStatusCode(403);
                return $response;
            }
            $response = $this->render('game/bottomGameInfo.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player1"]);
            $response->setStatusCode(403);
            return $response;
        } // Spielzug unzulässig
        else {
            // unentschieden
            if ($this->isDraw($this->getGameState())) {
                if ($player === "Player1") {
                    $response = $this->render('game/bottomGameInfo.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player2"]);
                    $response->setStatusCode(403);
                    return $response;
                }
                $response = $this->render('game/bottomGameInfo.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player1"]);
                $response->setStatusCode(403);
                return $response;
            } elseif (!)


            if ($player === "Player1") {
                $response = $this->render('game/bottomGameInfo.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player1"]);
                $response->setStatusCode(405);
                return $response;
            }
            $response = $this->render('game/bottomGameInfo.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player2"]);
            $response->setStatusCode(405);
            return $response;
        }
    }

    /**
     * Diese Methode gibt False zurück, falls der Spielzug unzulässig ist und True andersfalls
     * @param array $gameState
     * @param int $playedCellIndex
     * @param $player_id
     * @return bool
     */
    private function handleCellPlayed(array $gameState, int $playedCellIndex, $player_id): bool
    {
        if (empty($gameState[$playedCellIndex])) {
            $gameState[$playedCellIndex] = $player_id;
            return true;
        }
        return false;
    }


    /**
     * Die Methode gibt True, wenn es keine Spielmöglichkeit mehr gibt und False andersfalls
     * @param array $gameState
     * @return bool
     */
    private function isDraw(array $gameState): bool
    {
        foreach ($gameState as $element) {
            if (empty($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Die Methode gibt den Namen des Spielers an, der gewonnen hat und false andersfalls
     * @param array $gameState
     * @param $winnigConditions
     * @return false|string
     */
    private function isWining(array $gameState, $winnigConditions): string|false
    {
        $player1Indexes = $this->getAllTypedCellFromPlayer($gameState, "Player1");
        $player2Indexes = $this->getAllTypedCellFromPlayer($gameState, "Player2");
        if ($this->checkForWiningCondition($winnigConditions, $player1Indexes)) {
            return "Player1";
        }
        if ($this->checkForWiningCondition($winnigConditions, $player2Indexes)) {
            return "Player2";
        }
        return false;
    }

    /**
     * Gibt alle von einem Spieler getippten Zellen zurück
     * @param array $gameState
     * @param $playerId
     * @return array
     */
    private function getAllTypedCellFromPlayer(array $gameState, $playerId) {
        $playerIndexes = array();
        for ($i = 0; $i < count($gameState); $i++) {
            if ($gameState[$i] === $playerId) {
                $player1_indexes[] = $i;
            }
        }
        return $playerIndexes;
    }


    /**
     * Die Methode zeigt an, ob die von einem Spieler getippten Zellen in der $winningCondition liegen.
     * Im positiven Fall, wird true zurückgegeben und False andersfalls
     * @param $winingCondition
     * @param $playerIndexes
     * @return bool
     */
    private function checkForWiningCondition($winingCondition, $playerIndexes): bool{
        foreach ($winingCondition as $item) {
            if ($item === $playerIndexes) {
                return true;
            }
        }
        return false;
    }
}
