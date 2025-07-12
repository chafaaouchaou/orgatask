<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\UserRepository;
use App\Service\UserSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taskRepository = $entityManager->getRepository(Task::class);
        $userRepository = $entityManager->getRepository(User::class);
        
        // Récupérer les paramètres de filtrage
        $filter = $request->query->get('filter', 'all');
        $status = $request->query->get('status', 'all');
        $createdBy = $request->query->get('created_by', '');
        $assignedTo = $request->query->get('assigned_to', '');
        $sortOrder = $request->query->get('order', 'asc');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 7;
        $offset = ($page - 1) * $limit;
        
        // Construire la requête de base
        $queryBuilder = $taskRepository->createQueryBuilder('t')
            ->leftJoin('t.assignedUsers', 'u');
        
        // Appliquer les filtres de date d'échéance
        switch ($filter) {
            case 'overdue':
                $queryBuilder->andWhere('t.dueDate < :now')
                    ->andWhere('t.status != :done')
                    ->setParameter('now', new \DateTime())
                    ->setParameter('done', 'done');
                break;
            case 'urgent':
                $now = new \DateTime();
                $urgentDate = (clone $now)->modify('+3 days');
                $queryBuilder->andWhere('t.dueDate BETWEEN :now AND :urgent_date')
                    ->andWhere('t.status != :done')
                    ->setParameter('now', $now)
                    ->setParameter('urgent_date', $urgentDate)
                    ->setParameter('done', 'done');
                break;
            case 'due_today':
                $today = new \DateTime();
                $startOfDay = (clone $today)->setTime(0, 0, 0);
                $endOfDay = (clone $today)->setTime(23, 59, 59);
                $queryBuilder->andWhere('t.dueDate BETWEEN :start AND :end')
                    ->setParameter('start', $startOfDay)
                    ->setParameter('end', $endOfDay);
                break;
            case 'due_this_week':
                $now = new \DateTime();
                $endOfWeek = (clone $now)->modify('next Sunday')->setTime(23, 59, 59);
                $queryBuilder->andWhere('t.dueDate BETWEEN :now AND :end_of_week')
                    ->setParameter('now', $now)
                    ->setParameter('end_of_week', $endOfWeek);
                break;
            case 'no_due_date':
                $queryBuilder->andWhere('t.dueDate IS NULL');
                break;
        }
        
        // Filtre par statut
        if ($status !== 'all') {
            $queryBuilder->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }
        
        // Filtre par créateur
        if (!empty($createdBy) && $createdBy !== '-1') {
            $queryBuilder->andWhere('t.createdBy = :createdBy')
                ->setParameter('createdBy', $createdBy);
        }
        
        // Filtre par utilisateur assigné
        if (!empty($assignedTo) && $assignedTo !== '-1') {
            $queryBuilder->andWhere('u.id = :assignedTo')
                ->setParameter('assignedTo', $assignedTo);
        }
        
        // Tri uniquement par date d'échéance
        $queryBuilder->orderBy('t.dueDate', $sortOrder === 'desc' ? 'DESC' : 'ASC');
        $queryBuilder->addOrderBy('t.id', 'ASC'); // Tri secondaire pour la cohérence
        
        // Compter le total pour la pagination
        $totalQuery = clone $queryBuilder;
        $totalQuery->select('COUNT(DISTINCT t.id)');
        $total = $totalQuery->getQuery()->getSingleScalarResult();
        
        // Appliquer la pagination
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit)
            ->distinct();
        
        $tasks = $queryBuilder->getQuery()->getResult();
        
        // Calculer les infos de pagination
        $totalPages = ceil($total / $limit);
        $hasNextPage = $page < $totalPages;
        $hasPreviousPage = $page > 1;
        
        // Récupérer tous les utilisateurs pour le filtre
        $users = $userRepository->findBy([], ['name' => 'ASC']);
        
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'current_filter' => $filter,
            'current_status' => $status,
            'current_created_by' => $createdBy,
            'current_assigned_to' => $assignedTo,
            'current_order' => $sortOrder,
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_next' => $hasNextPage,
                'has_previous' => $hasPreviousPage,
                'total_items' => $total,
                'per_page' => $limit
            ]
        ]);
    }

    #[Route('/task/new', name: 'app_task_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir le créateur de la tâche comme l'utilisateur connecté
            $task->setCreatedBy($this->getUser());
            
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée avec succès !');
            return $this->redirectToRoute('app_task');
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
