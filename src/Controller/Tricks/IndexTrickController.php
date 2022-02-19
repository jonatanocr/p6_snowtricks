<?php

namespace App\Controller\Tricks;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\Comments\NewCommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class IndexTrickController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(Request $request)
    {
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            //->findAll();
            ->findFour();
        if($request->request->get('trick_min')){
            $trickMin = $request->request->get('trick_min');
            $tricksMore = $this->getDoctrine()
                ->getRepository(Trick::class)
                ->findFour($trickMin);
            return new JsonResponse(json_encode($tricksMore));
            //return new JsonResponse(json_encode('aaaa'));
        }
        return $this->render('tricks/index.html.twig', ['tricks' => $tricks]);
    }

    /**
     * @Route("/loadmore", name="load_more_tricks")
     */
    public function loadMore(Request $request)
    {
        $trickMin = $request->request->get('trick_min');
        $tricksMore = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findFour($trickMin);
     //   return new JsonResponse(json_encode($tricksMore));
        $data = array();
        foreach ($tricksMore as $trick) {
            $data[] = $this->renderView('tricks/card.html.twig', [
                'id' => $trick->getId(),
                'name' => $trick->getName(),
                'description' => $trick->getDescription()
            ]);
        }
        return new JsonResponse(json_encode($data));
    }

    /**
     * @Route("/trick/{id}", name="trick_display")
     * @param Request $request
     * @param int $id
     * @param Security $security
     * @return Response
     */
    public function displayOne(Request $request, $id, Security $security): Response
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
        if($request->request->get('comment_min')){
            $comment_min = $request->request->get('comment_min');
            $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
            $comments = $commentRepository->findByTrick(['trick' => $id], $comment_min);
            return new JsonResponse(json_encode($comments));
        }
        return $this->render('tricks/one.html.twig', [
            'trick' => $trick,
            'addCommentForm' => $addComment->createView(),
            'comments' => $comments
        ]);
    }
}
