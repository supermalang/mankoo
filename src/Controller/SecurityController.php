<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        return $this->render(
            '@EasyAdmin/page/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'translation_domain' => 'admin',
                'page_title' => 'Mankoo',
                'csrf_token_intention' => 'authenticate',
                'target_path' => $this->generateUrl('admin'),
                'username_label' => 'Your username',
                'password_label' => 'Your password',
                'sign_in_label' => 'Log in',
                'username_parameter' => 'username',
                'password_parameter' => 'password',
                'forgot_password_enabled' => false,
                // 'forgot_password_path' => $this->generateUrl('...', ['...' => '...']),
                // 'forgot_password_label' => 'Forgot your password?',
                'remember_me_enabled' => false,
                // 'remember_me_parameter' => 'custom_remember_me_param',
                // 'remember_me_checked' => true,
                // 'remember_me_label' => 'Remember me',
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
