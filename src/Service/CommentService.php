<?php

namespace App\Service;

use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;

class CommentService
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function create($text, $parentId, $user, $article): Comment
    {
        $comment = new Comment();
        $comment->setText($text);
        if ($parentId) {
            $parentComment = $this->doctrine->getRepository('App:Comment')->find($parentId);
            if ($parentComment) {
                $comment->setParentComment($parentComment);
            }
        }
        $user = $this->doctrine->getRepository('App:User')->find($user);
        if ($user) $comment->setUser($user);
        $comment->setArticle($article);
        return $comment;
    }

    public function transformArray($articles)
    {
        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'text' => $article->getText(),
            ];
        }


        return $this->json($data);
    }

}