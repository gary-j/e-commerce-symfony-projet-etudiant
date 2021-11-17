<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{prenom?World}/{age?0}", name="hello")
     */
    public function hello($prenom, $age, Environment $twig)
    {
        // dd(get_class_methods($twig));

        $html = $twig->render('hello.html.twig', [
            'prenom' => $prenom,
            'age' => $age,
            'majeur' => 'Adulte, c\'est bien',
            'mineur' => 'Mineur; Où sont vos parents ?',
            'prenoms' => [
                'Gary', 'Binette', 'Enora', 'Edène', 'Oya'
            ],
            'ages' => [
                12,
                23,
                33,
                3,
                18
            ],
            'moi' => [
                'prenom' => 'Gary',
                'nom' => 'J.',
                'age' => 33
            ],
            'formateur1' => ['prenom' => 'Gary', 'nom' => 'Jes'],
            'formateur2' => ['prenom' => 'Baby', 'nom' => 'Douce']

        ]);
        return new Response($html);
    }

    /**
     * @Route("/example", name="example")
     */
    public function example()
    {
        return $this->render('example.html.twig');
    }
}
