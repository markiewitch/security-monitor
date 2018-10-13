<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\VcsConnectionInfo;
use App\Form\VcsConnectionType;
use App\Query\FetchVcsConnections;
use App\Repository\OrmConnectionsRepository;
use App\Service\System;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/connections")
 */
class ConnectionsController extends Controller
{
    /**
     * @Route(path="/", name="connections_list")
     */
    public function listAction(System $system): Response
    {
        $user        = $this->getUser();
        $connections = $system->query(FetchVcsConnections::forUser($user->getId()));

        return $this->render('connections/list.html.twig',
                             ['connections' => $connections]);
    }

    /**
     * @Route(path="/create", name="connections_create")
     */
    public function createAction(Request $request, OrmConnectionsRepository $repository)
    {
        $form = $this->getCreateConnectionForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $connectionInfo = $form->getData();
            $repository->persist($connectionInfo);

            return $this->redirectToRoute('connections_list');
        }

        return $this->render("connections/create.html.twig",
                             ['form' => $form->createView()]);
    }

    private function getCreateConnectionForm(): FormInterface
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException("You need to be logged in to add connections");
        }

        return $this->createForm(VcsConnectionType::class, new VcsConnectionInfo($user))
            ->add('submit', SubmitType::class);
    }
}
