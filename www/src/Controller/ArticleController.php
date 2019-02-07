<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ArticleController
{
    /**
    * @Route("/homepage")
    */
    public function homepage()
    {
        return new Response(
            '  <html>
                <h1>Siema elo</h1>
                <body>stronka domowa</body> 
               </html>'
        );
    }
}