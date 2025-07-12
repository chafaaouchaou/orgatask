<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Service\TaskService;
use App\Service\MailService;
use App\Service\UserSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function __construct(
        private TaskService $taskService,
        private MailService $mailService
    ) {}

    #[Route('/task', name: 'app_task')]
    public function index(Request $request): Response
    {
        // Récupérer les paramètres de filtrage
        $filters = [
            'filter' => $request->query->get('filter', 'all'),
            'status' => $request->query->get('status', 'all'),
            'created_by' => $request->query->get('created_by', ''),
            'assigned_to' => $request->query->get('assigned_to', ''),
            'order' => $request->query->get('order', 'asc'),
        ];
        
        $page = max(1, $request->query->getInt('page', 1));
        
        // Utiliser le service pour récupérer les tâches
        $result = $this->taskService->getTasks($filters, $page);
        
        // Récupérer tous les utilisateurs pour les filtres
        $users = $this->taskService->getAllUsers();
        
        return $this->render('task/index.html.twig', [
            'tasks' => $result['tasks'],
            'current_filter' => $filters['filter'],
            'current_status' => $filters['status'],
            'current_created_by' => $filters['created_by'],
            'current_assigned_to' => $filters['assigned_to'],
            'current_order' => $filters['order'],
            'users' => $users,
            'pagination' => $result['pagination']
        ]);
    }

    #[Route('/task/new', name: 'app_task_new')]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Créer la tâche via le service
                $createdTask = $this->taskService->createTask($task, $this->getUser());
                
                // Envoyer les notifications par email
                $this->mailService->sendTaskCreationNotification($createdTask);
                
                $this->addFlash('success', 'Tâche créée avec succès ! Les utilisateurs assignés ont été notifiés par email.');
                return $this->redirectToRoute('app_task');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de la tâche : ' . $e->getMessage());
            }
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/users/search', name: 'api_users_search', methods: ['GET'])]
    public function searchUsers(Request $request, UserSearchService $userSearchService): JsonResponse
    {
        $query = $request->query->get('q', '');
        return $userSearchService->searchUsersJsonResponse($query, 10);
    }
}
