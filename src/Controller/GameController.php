<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/', name: 'Tic Tac Toe')]
    public function homePage(): Response {
        return $this->render('game/homepage.html.twig');
    }
    #[Route('/play', name: 'init_game')]
    public function initGame(): Response {
        return $this->render('game/playingfield.html.twig', ["score1" => 0, "score2" => 0, "turnInfo" => "Player1"]);
    }
}
