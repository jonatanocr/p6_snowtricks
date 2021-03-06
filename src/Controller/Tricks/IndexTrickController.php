<?php

namespace App\Controller\Tricks;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\Comments\NewCommentFormType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
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
    public function index()
    {
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findHeight();
        foreach ($tricks as $trick) {
            $trick->setFirstPicture();
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
            ->findHeight($trickMin);
        $data = array();
        foreach ($tricksMore as $trick) {
            $trick->setFirstPicture();
            $data[] = $this->renderView('tricks/card.html.twig', [
                'id' => $trick->getId(),
                'name' => $trick->getName(),
                'description' => $trick->getDescription(),
                'firstPicture' => $trick->getFirstPicture(),
                'mainPicture' => $trick->getMainPicture()
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
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);
        if (!$trick) {
            throw $this->createNotFoundException('No trick found');
        }
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findByTrick(['trick' => $id]);
        foreach ($comments as $comment) {
            $commentUser = $userRepository->find($comment->getAuthor());
            if (!empty($commentUser->getAvatarFilename())) {
                $comment->setAuthorIconPAth($commentUser->getAvatarFilename());
            }
        }
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
            foreach ($comments as $comment) {
                $commentUser = $userRepository->find($comment->getAuthor());
                if (!empty($commentUser->getAvatarFilename())) {
                    $comment->setAuthorIconPAth($commentUser->getAvatarFilename());
                }
            }
            return new JsonResponse(json_encode($comments));
        }
        return $this->render('tricks/one.html.twig', [
            'trick' => $trick,
            'addCommentForm' => $addComment->createView(),
            'comments' => $comments
        ]);
    }
}
