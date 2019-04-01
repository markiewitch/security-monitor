<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrmPackagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/packages")
 */
class PackagesController extends Controller
{
    /**
     * @Route(path="/", name="packages_list")
     */
    public function list(OrmPackagesRepository $packages, Request $request): Response
    {
        $params = [
            'packages' => $packages->fetchAll(
                $request->query->getAlnum('search', null),
                $request->query->getInt('page', 1)
            ),
        ];

        return $this->render('packages/list.html.twig', $params);
    }

    /**
     * @Route(path="/{id}", requirements={"id": "\d+"}, name="package_view")
     */
    public function package(OrmPackagesRepository $packages, int $id)
    {
        $params = [
            'package' => $packages->findOne($id),
        ];

        return $this->render('packages/view.html.twig', $params);
    }

}
