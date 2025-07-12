<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Trouve les tâches en retard (date d'échéance passée et pas terminées)
     */
    public function findOverdueTasks(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.dueDate < :now')
            ->andWhere('t.status != :done')
            ->setParameter('now', new \DateTime())
            ->setParameter('done', 'done')
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les tâches urgentes (échéance dans les 3 prochains jours et pas terminées)
     */
    public function findUrgentTasks(): array
    {
        $now = new \DateTime();
        $urgentDate = (clone $now)->modify('+3 days');

        return $this->createQueryBuilder('t')
            ->andWhere('t.dueDate BETWEEN :now AND :urgent_date')
            ->andWhere('t.status != :done')
            ->setParameter('now', $now)
            ->setParameter('urgent_date', $urgentDate)
            ->setParameter('done', 'done')
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les tâches sans date d'échéance
     */
    public function findTasksWithoutDueDate(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.dueDate IS NULL')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les tâches dues aujourd'hui
     */
    public function findTasksDueToday(): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0);
        $endOfDay = (clone $today)->setTime(23, 59, 59);

        return $this->createQueryBuilder('t')
            ->andWhere('t.dueDate BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les tâches dues cette semaine
     */
    public function findTasksDueThisWeek(): array
    {
        $now = new \DateTime();
        $endOfWeek = (clone $now)->modify('next Sunday')->setTime(23, 59, 59);

        return $this->createQueryBuilder('t')
            ->andWhere('t.dueDate BETWEEN :now AND :end_of_week')
            ->setParameter('now', $now)
            ->setParameter('end_of_week', $endOfWeek)
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les tâches par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
