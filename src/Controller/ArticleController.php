<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article_show")
     */
    public function show(Article $article)
    {
        return $this->render('article.html.twig', [
            'article' => $article,
        ]);
    }
}