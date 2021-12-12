<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{

    /**
     * @Route("/purchase", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez vous connecter pour acceder à vos commande")
     */
    public function index()
    {
        // 1. S'assurer que la personne est connectée sinon redirection accueil/login
        // - Security et génération d'url en fonction de nom d'une route
        // 2. Nous voulons savoir qui est connecté

        /** @var User */
        $user = $this->getUser();

        // 3. Nous voulons passé l'utilisateur connecté à Twig pour afficher ses commandes à Twig
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
