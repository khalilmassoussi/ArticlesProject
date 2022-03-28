<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        if (!$this->getUser()){
        return $this->render('homepage/login.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
        } else {
            return $this->redirectToRoute('app_homepage');
        }
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function index(): Response
    {
        $articles = $this->doctrine->getRepository('App:Article')->findAll();
        return $this->render('homepage/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
