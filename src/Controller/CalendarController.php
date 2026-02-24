<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig');
    }

    #[Route('/user/calendar', name: 'app_user_calendar')]
    public function userIndex(): Response
    {
        // Reuse the same template but the route name will trigger the user sidebar
        return $this->render('calendar/index.html.twig');
    }
}
