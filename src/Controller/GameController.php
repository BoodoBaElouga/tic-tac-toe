<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private string $test = "test";

    private function getTest(): string
    {
        return $this->test;
    }

    private int $scorePlayer1;

    private int $scorePlayer2;

    private string $currentPlayer;

    private function getCurrentPlayer(): string
    {
        return $this->currentPlayer;
    }

    private array $gameState;

    private $winningConditions;

    /**
     * @return array
     */
    public function getGameState()
    {
        return $this->gameState;
    }


    public function getWinningConditions()
    {
        return $this->winningConditions;
    }

    public function getScorePlayer1(): int
    {
        return $this->scorePlayer1;
    }

    public function getScorePlayer2(): int
    {
        return $this->scorePlayer2;
    }

    public function setScorePlayer1(int $score): void
    {
        $this->scorePlayer1 = $score++;
    }

    public function setScorePlayer2(int $score): void
    {
        $this->scorePlayer2 = $score++;
    }

    public function setCurrentPlayer(string $player): void
    {
        $this->currentPlayer = $player;
    }

    #[Route('/', name: 'Tic Tac Toe')]
    public function homePage(): Response
    {
        return $this->render('game/homepage.html.twig');
    }

    #[Route('/play', name: 'init_game')]
    public function initGame(Request $request): Response
    {
        $session = $request->getSession();

        $this->scorePlayer1 = 0;
        $this->scorePlayer2 = 0;
        $this->currentPlayer = "Player1";
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

        $session->set('info',
            ['scorePlayer1' => $this->getScorePlayer1(),
                'scorePlayer2' => $this->getScorePlayer2(),
                'currentPlayer' => $this->getCurrentPlayer(),
                'gameState' => $this->getGameState(),
                'winningConditions' => $this->getWinningConditions()]);


        return $this->render('game/playingfield.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player1"]);
    }

    #[Route('/manager', name: 'game_manager')]
    public function gameManager(Request $request): Response
    {
        $session = $request->getSession();
        $this->scorePlayer1 = $session->get('info')['scorePlayer1'];
        $this->scorePlayer2 = $session->get('info')['scorePlayer2'];
        $this->currentPlayer = $session->get('info')['currentPlayer'];
        $this->gameState = $session->get('info')['gameState'];
        $this->winningConditions = $session->get('info')['winningConditions'];

        $typedCell = $request->request->get('typed_cell');
        $player = $request->request->get('player');


        // prüfen, ob der Spielzug zulläsig ist
        if ($this->handleCellPlayed($this->gameState, intval($typedCell), $player, $session)) {
            if ($player === "Player1") {
                $this->setCurrentPlayer("Player2");
            } else {
                $this->setCurrentPlayer("Player1");
            }

            // wenn insgesammt 5 Zellen schon befüllt sind, sollte man mit der Auswertung beginnen
            $typedCell = $this->countTypedCells($this->getGameState());


            if ($typedCell >= 5) {
                // check ob das Spiel schon gewonnen ist

                // Player1 hat gewonnen
                if ($this->isWining($this->getGameState(), $this->getWinningConditions()) === "Player1") {
                    $this->setScorePlayer1($this->getScorePlayer1());

                    // Info in der Session speichern
                    $sessionInfo = $session->get('info');
                    $sessionInfo['scorePlayer1'] = $this->getScorePlayer1();

                    $response = $this->render('game/finish.html.twig', ["winner" => "Player1"]);
                    $response->setStatusCode(200);
                    return $response;
                } // Player2 hat gewonnen
                elseif ($this->isWining($this->getGameState(), $this->getWinningConditions()) === "Player2") {
                    $this->setScorePlayer2($this->getScorePlayer2());

                    // Info in der Session speichern
                    $sessionInfo = $session->get('info');
                    $sessionInfo['scorePlayer2'] = $this->getScorePlayer2();
                    $session->set('info', $sessionInfo);

                    $response = $this->render('game/finish.html.twig', ["winner" => "Player2"]);
                    $response->setStatusCode(200);

                    return $response;
                } // Kein oder noch kein Gewinner
                else {
                    // überprüfen ob unentschieden steht
                    if ($this->isDraw($this->getGameState())) {
                        $response = $this->render('game/draw.html.twig', ["message" => $this->getGameState()]);
                        $response->setStatusCode(200);

                        return $response;
                    } // Noch kein Gewinner (Spiel noch nicht beendet)
                    else {
                        $response = $this->render('game/bottomGameInfo.html.twig',
                            ["score1" => $this->getScorePlayer1(), "score2" => $this->getScorePlayer2(), "turnInfo" => $this->getCurrentPlayer()]);

                        $response->setStatusCode(200);
                        return $response;
                    }
                }
            }
            // Weniger als 6 Zellen befüllt. Das Spiel geht ganz normal weiter.
            $response = $this->render('game/bottomGameInfo.html.twig',
                ["score1" => $this->getScorePlayer1(), "score2" => $this->getScorePlayer2(), "turnInfo" => $this->getCurrentPlayer()]);

            $response->setStatusCode(200);
            return $response;

        } // Spielzug unzulässig
        else {
            if ($player === "Player1") {
                $response = $this->render('game/bottomGameInfo.html.twig',
                    ["score1" => $this->getScorePlayer1(), "score2" => $this->getScorePlayer2(), "turnInfo" => "Player1"]);
                $response->setStatusCode(405);
                return $response;
            }
            $response = $this->render('game/bottomGameInfo.html.twig',
                ["score1" => $this->getScorePlayer1(), "score2" => $this->getScorePlayer2(), "turnInfo" => "Player2"]);
            $response->setStatusCode(405);
            return $response;
        }
    }

    /**
     * Diese Methode gibt False zurück, falls der Spielzug unzulässig ist und True andersfalls
     * @param array $gameState
     * @param int $playedCellIndex
     * @param $player_id
     * @param $session
     * @return bool
     */
    private function handleCellPlayed(array $gameState, int $playedCellIndex, $player_id, $session): bool
    {
        if (empty($gameState[$playedCellIndex])) {
            $gameState[$playedCellIndex] = $player_id;
            $this->gameState = $gameState;

            // Info in der Session speichern
            $sessionInfo = $session->get('info');
            $sessionInfo['gameState'] = $gameState;
            $session->set('info', $sessionInfo);

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
    private function getAllTypedCellFromPlayer(array $gameState, $playerId)
    {
        $playerIndexes = array();

        for ($i = 0; $i < count($gameState); $i++) {
            if ($gameState[$i] === $playerId) {
                $playerIndexes[] = $i;
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
    private function checkForWiningCondition($winingCondition, $playerIndexes): bool
    {
        foreach ($winingCondition as $item) {
            if ($item === $playerIndexes) {
                return true;
            }
        }
        return false;
    }

    /**
     * Die Methode zählt alle Zellen, die von Spielern getippt wurde
     * @param array $gameState
     * @return int
     */
    private function countTypedCells(array $gameState): int
    {
        $counter = 0;
        foreach ($gameState as $stateItem) {
            if (!empty($stateItem)) {
                $counter++;
            }
        }
        return $counter;
    }
}
