<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    #[Route('/ads/{slug}/book', name: 'booking_create')]
    #[IsGranted("ROLE_USER")]
    public function book(Ad $ad,Request $request, EntityManagerInterface $manager): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // traitement -> ajouter le user et l'annonce
            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);

            // si les dates ne sont pas disponbile, message d'erreur
            if(!$booking->isBookableDates())
            {
                $this->addFlash(
                    'warning',
                    'Les dates que vous avez choisie ne peuvent être réservées: elles sont déjà prises!'
                );
            }else{
                $this->addFlash(
                    'success',
                    "Merci pour votre réservation"
                );
    
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('ads_show',[
                    'slug'=>$ad->getSlug()
                ]);

            }

        }

        return $this->render('booking/book.html.twig', [
            'myForm' => $form->createView(),
            'ad' => $ad
        ]);
    }
}
