<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    #[Route('/admin/comments', name: 'admin_comments_index')]
    public function index(CommentRepository $repo): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll()
        ]);
    }

    /**
     * Permet d'éditer les commentaires (pas déontologique)
     *
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/comments/{id}/edit", name:"admin_comments_edit")]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire n°".$comment->getId()." a été modifié"
            );
        }

        return $this->render("admin/comment/edit.html.twig",[
            'comment' => $comment,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/comments/{id}/delete", name:"admin_comments_delete")]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            "success",
            "Le commentaire n°".$comment->getId()." a bien été supprimé"
        );

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute("admin_comments_index");
    }
}
