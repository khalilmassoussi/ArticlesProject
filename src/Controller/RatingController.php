<?php

namespace App\Controller;

use App\Service\RatingService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    private $ratingService;
    private $doctrine;

    /**
     * @param RatingService $ratingService
     * @param ManagerRegistry $doctrine
     */
    public function __construct(RatingService $ratingService, ManagerRegistry $doctrine)
    {
        $this->ratingService = $ratingService;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/rating", name="app_rating")
     */
    public function index(): Response
    {
        return $this->render('rating/index.html.twig', [
            'controller_name' => 'RatingController',
        ]);
    }

    /**
     * @Route("/rating/add", name="add_rate", options={"expose"=true})
     */
    public function rate(Request $request): JsonResponse
    {
        $rating = $this->ratingService->createRating($request->get('comment_id'), $request->get('rate'), $request->get('user_id'));
        if ($rating) {
            $this->doctrine->getManager()->persist($rating);
            $this->doctrine->getManager()->flush();
            return $this->json('Success');
        } else {
            return $this->json('Error', 401);
        }
    }
}
