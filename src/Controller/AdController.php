<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdController extends AbstractController
{

    /**
     * Permet d'afficher l'ensemble des annonces du site
     *
     * @param AdRepository $repo
     * @return Response
     */
    #[Route('/ads', name: 'ads_index')]
    public function index(AdRepository $repo): Response
    {

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }


    /**
     * Permet d'ajouter une annonce à la bdd 
     *
     * @return Response
     */
    #[Route("/ads/new", name:"ads_create")]
    public function create(): Response
    {
        // $ad = new Ad();
        // $form = $this->createFormBuilder($ad)
        //             ->add('title')
        //             ->add('introduction')
        //             ->add('content')
        //             ->add('rooms')
        //             ->add('price')
        //             ->add('save', SubmitType::class, [
        //                 'label' => "Créer le nouvelle annonce",
        //                 'attr' => [
        //                     'class' => "btn btn-primary"
        //                 ]
        //             ])
        //             ->getform();

        $ad = new Ad();
        $form = $this->createFormBuilder($ad)
                    ->add('title')
                    ->add('introduction')
                    ->add('content')
                    ->add('rooms')
                    ->add('price')
                    ->getform();

        return $this->render("ad/new.html.twig",[
            'form' => $form->createView()
        ]);

    }


    /**
     * Permet d'afficher une annonce 
     * 
     * @param string $slug
     * @param Ad $ad
     * @return Response
     */
    #[Route("/ads/{slug}", name:"ads_show")]
    public function show(string $slug, Ad $ad): Response
    {
        // $ad = $repo->findby(["slug"=>$slug])

        // dump($ad);

        return $this->render("ad/show.html.twig", [
            'ad' => $ad
        ]);
    }


}
