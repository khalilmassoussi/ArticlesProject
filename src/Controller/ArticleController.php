<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    /**
     * @return Response
     * @Route("/article/{id}", name="app_show_article")
     */
    public function show($id){

        $article = $this->getDoctrine()->getRepository(Article::class);
        $article = $article->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'Aucun article pour l\'id: ' . $id
            );
        }

        return $this->render(
            'article/show.html.twig',
            array('article' => $article)
        );
    }
}
