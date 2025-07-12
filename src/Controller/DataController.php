<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DataController extends AbstractController
{
    #[Route('/data', name: 'app_data')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $tasks = $entityManager->getRepository(Task::class)->findAll();

        return $this->render('data/index.html.twig', [
            'users' => $users,
            'tasks' => $tasks,
        ]);
    }

}



