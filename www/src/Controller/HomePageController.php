<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class HomePageController extends AbstractController
{
    /**
     * @Route("/homepage", name = "homepage")
     */
    public function home()
    {
        return $this->render('homepage.html.twig');
    }
}