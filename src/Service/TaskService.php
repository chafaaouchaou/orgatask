<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class TaskService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Crée une nouvelle tâche
     */
    public function createTask(Task $task, User $creator): Task
    {
        $task->setCreatedBy($creator);
        
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        
        return $task;
    }

    /**
     * Construit une requête pour récupérer les tâches avec filtres
     */
    public function buildTaskQuery(array $filters = []): QueryBuilder
    {
        $taskRepository = $this->entityManager->getRepository(Task::class);
        
        $queryBuilder = $taskRepository->createQueryBuilder('t')
            ->leftJoin('t.assignedUsers', 'u');
        
        // Filtre par date d'échéance
        $this->applyDateFilter($queryBuilder, $filters['filter'] ?? 'all');
        
        // Filtre par statut
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $queryBuilder->andWhere('t.status = :status')
                ->setParameter('status', $filters['status']);
        }
        
        // Filtre par créateur
        if (!empty($filters['created_by']) && $filters['created_by'] !== '-1') {
            $queryBuilder->andWhere('t.createdBy = :createdBy')
                ->setParameter('createdBy', $filters['created_by']);
        }
        
        // Filtre par utilisateur assigné
        if (!empty($filters['assigned_to']) && $filters['assigned_to'] !== '-1') {
            $queryBuilder->andWhere('u.id = :assignedTo')
                ->setParameter('assignedTo', $filters['assigned_to']);
        }
        
        // Tri
        $sortOrder = $filters['order'] ?? 'asc';
        $queryBuilder->orderBy('t.dueDate', $sortOrder === 'desc' ? 'DESC' : 'ASC');
        $queryBuilder->addOrderBy('t.id', 'ASC');
        
        return $queryBuilder;
    }

    /**
     * Applique les filtres de date d'échéance
     */
    private function applyDateFilter(QueryBuilder $queryBuilder, string $filter): void
    {
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
    }

    /**
     * Récupère les tâches avec pagination
     */
    public function getTasks(array $filters = [], int $page = 1, int $limit = 7): array
    {
        $offset = ($page - 1) * $limit;
        
        $queryBuilder = $this->buildTaskQuery($filters);
        
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
        
        return [
            'tasks' => $tasks,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_next' => $hasNextPage,
                'has_previous' => $hasPreviousPage,
                'total_items' => $total,
                'per_page' => $limit
            ]
        ];
    }

    /**
     * Récupère tous les utilisateurs pour les filtres
     */
    public function getAllUsers(): array
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        return $userRepository->findBy([], ['name' => 'ASC']);
    }
}
