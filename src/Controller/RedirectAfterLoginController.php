<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RedirectAfterLoginController extends AbstractController
{
    #[Route('/redirect', name: 'app_redirect_after_login')]
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Admin => Dashboard admin
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // Client => Page d'accueil
        return $this->redirectToRoute('app_home');
    }
}
