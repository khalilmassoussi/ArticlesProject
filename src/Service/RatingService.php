<?php

namespace App\Service;

use App\Entity\Rating;
use Doctrine\Persistence\ManagerRegistry;

class RatingService
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function createRating($comment_id, $rateNumber, $user_id)
    {
        $user = $this->doctrine->getRepository('App:User')->find($user_id);
        $comment = $this->doctrine->getRepository('App:Comment')->find($comment_id);
        $rate = $this->doctrine->getRepository('App:Rating')->findBy(array('comment' => $comment->getId(), 'user' => $user->getId()));
        if (!$rate) {
            $rate = new Rating();
            $rate->setUser($user);
            $rate->setComment($comment);
            $rate->setRate($rateNumber);
            return $rate;
        } else return false;
    }
}