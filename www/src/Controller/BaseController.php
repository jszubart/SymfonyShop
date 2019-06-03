<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Category;

class BaseController extends AbstractController
{

    public function showCategoryList()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('categorylist.html.twig', [
            'categories' => $categories,
        ]);
    }

}