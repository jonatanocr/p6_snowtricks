<?php

namespace App\Controller\Security;


use App\Entity\User;
use App\Form\AccountInformationType;
use App\Form\SettingsFormType;
use App\Service\Notification\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SettingsController extends AbstractController
{
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    #[Route('/settings', name: 'settings')]
    public function updateUser(Request $request): Response
    {
        $user = new User();
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
        $form = $this->createForm(SettingsFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->redirectToRoute('settings');
        }

        return $this->render('settings/settings.html.twig', [
            'settingsForm' => $form->createView(),
        ]);

    }
}