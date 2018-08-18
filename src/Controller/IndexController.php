<?php
/**
 * Created by PhpStorm.
 * User: mmarkiewicz
 * Date: 28.01.18
 * Time: 12:27
 */

namespace App\Controller;


use Github\Client;
use SensioLabs\Security\Crawler\CrawlerInterface;
use SensioLabs\Security\SecurityChecker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{
    /**
     * @Route(path="/", name="index")
     */
    public function indexAction(): Response
    {
        $client = $this->get(Client::class);
        $applications = $client->organization()->repositories("gogcom");

        return $this->render("index/applications.html.twig",
            ["applications" => $applications]);
    }
}
