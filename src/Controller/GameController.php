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

    public function initGame(): Response {
        return new Response("New game");
    }
}
