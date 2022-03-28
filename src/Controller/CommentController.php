<?php

namespace App\Controller;

use App\Security\SpamChecker;
use App\Service\CommentService;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerBuilder;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    /**
     * @Route("/", name="app_comment", methods={"GET"})
     */
    public function index(): Response
    {
        $comments = $this->doctrine->getRepository('App:Comment')->findAll();
        $serializer = SerializerBuilder::create()->build();
        $serializer->serialize($comments, 'json');
        return new Response($serializer->serialize($comments, 'json'));
    }

    /**
     * @Route("/", name="app_comment_create", methods={"POST"}, options={"expose"=true})
     * @throws TransportExceptionInterface
     */
    public function create(Request $request, CommentService $commentService, SpamChecker $spamChecker): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $article = $entityManager->getRepository('App:Article')->find($request->get('article_id'));
        $comment = $commentService->create($request->get('text'), $request->get('parent_id'), $request->get('user_id'), $article);
        $entityManager->persist($comment);
        $context = [
            'user_ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('user-agent'),
            'referrer' => $request->headers->get('referer'),
            'permalink' => $request->getUri(),
        ];
        if (2 === $spamChecker->getSpamScore($comment, $context)) {
            throw new RuntimeException('Spam, go away!');
        }
        $entityManager->flush();

        return $this->json('Success');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/article/{id}", name="app_comment_by_article", methods={"GET"}, options={"expose"=true})
     */
    public function commentByArticle(Request $request, $id)
    {
        $comments = $this->doctrine->getRepository('App:Comment')->findBy(array('article' => $id, 'parentComment' => null));
        $serializer = SerializerBuilder::create()->build();
        $serializer->serialize($comments, 'json');
        return new Response($serializer->serialize($comments, 'json'));
    }
}
