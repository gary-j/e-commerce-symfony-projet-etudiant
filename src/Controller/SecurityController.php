<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {

        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);
        // $utils->getLastUsername() me permet de renseigner le champ email, avec le meme e-mail après une auth échouée

        $error = $utils->getLastAuthenticationError();

        dump($error, $utils->getLastUsername());

        return $this->render('security/login.html.twig', [
            'formView' => $form->createView(),
            'errors' => $error
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
}
