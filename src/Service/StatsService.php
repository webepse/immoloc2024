<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class  StatsService{

    public function __construct(private EntityManagerInterface $manager)
    {}

    /**
     * Permet de récup le nombre d'utilisateur enregistré sur mon site
     *
     * @return integer|null
     */
    public function getUsersCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u")->getSingleScalarResult();
    }

    /**
     * Permet de récup le nombre d'annonce
     *
     * @return integer|null
     */
    public function getAdsCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(a) FROM App\Entity\Ad a")->getSingleScalarResult();
    }

    /**
     * Permet de récup le nombre de réservation
     *
     * @return integer|null
     */
    public function getBookingsCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(b) FROM App\Entity\Booking b")->getSingleScalarResult();
    }

    /**
     * Permet de récup le nombre de commentaire
     *
     * @return integer|null
     */
    public function getCommentsCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(c) FROM App\Entity\Comment c")->getSingleScalarResult();
    }

    /**
     * Permet de récup les meilleurs ou pires annonces
     *
     * @param string $direction
     * @return array|null
     */
    public function getAdsStats(string $direction): ?array
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
             FROM App\Entity\Comment c 
             JOIN c.ad a
             JOIN a.author u
             GROUP BY a 
             ORDER BY note '.$direction
        )
        ->setMaxResults(5)
        ->getResult();
    }



}

