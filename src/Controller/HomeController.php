<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route(path="/", name="home")
     */
    public function __invoke()
    {
        return $this->render('home.html.twig');
    }
}
