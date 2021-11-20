<?php

namespace App\Controller\Tricks;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\Comments\NewCommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class IndexTrickController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findAll();
        return $this->render('tricks/index.html.twig', ['tricks' => $tricks]);
    }

    /**
     * @Route("/trick/{id}", name="trick_display")
     * @param Request $request
     * @param int $id
     * @param Security $security
     * @return Response
     */
    public function displayOne(Request $request, int $id, Security $security): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);
        if (!$trick) {
            throw $this->createNotFoundException(
                'No trick found'
            );
        }

        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findByTrick(['trick' => $id]);

        $comment = new Comment();
        $addComment = $this->createForm(NewCommentFormType::class, $comment);
        $addComment->handleRequest($request);
        if ($addComment->isSubmitted() && $addComment->isValid()) {
            $user = $security->getUser();
            $comment->setAuthor($user);
            $comment->setCreatedDate(new \DateTime());
            $comment->setTrick($trick);
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment added');
            return $this->redirectToRoute('trick_display', ['id' => $id]);
        }
        return $this->render('tricks/one.html.twig', [
            'trick' => $trick,
            'addCommentForm' => $addComment->createView(),
            'comments' => $comments,
        ]);
    }
}
