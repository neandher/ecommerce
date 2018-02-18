<?php

namespace App\Ecommerce\Shop\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="shop_index")
     */
    public function index()
    {
        return $this->render('shop/index.html.twig');
    }
}