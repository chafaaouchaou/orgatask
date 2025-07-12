<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    /**
     * Envoie une notification de création de tâche aux utilisateurs assignés
     */
    public function sendTaskCreationNotification(Task $task): void
    {
        $assignedUsers = $task->getAssignedUsers();
        
        if ($assignedUsers->isEmpty()) {
            $this->logger->info('Aucun utilisateur assigné pour la tâche', ['task_id' => $task->getId()]);
            return;
        }

        foreach ($assignedUsers as $user) {
            try {
                $this->sendTaskNotificationToUser($task, $user, 'création');
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de l\'envoi de mail à ' . $user->getEmail(), [
                    'task_id' => $task->getId(),
                    'user_id' => $user->getId(),
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Envoie une notification à un utilisateur spécifique
     */
    private function sendTaskNotificationToUser(Task $task, User $user, string $action): void
    {
        $email = (new Email())
            ->from('orgatask.app@gmail.com')
            ->to($user->getEmail())
            ->subject('OrgaTask - Nouvelle tâche assignée : ' . $task->getTitle())
            ->html($this->buildTaskNotificationHtml($task, $user, $action));

        $this->mailer->send($email);
        
        $this->logger->info('Mail envoyé avec succès', [
            'task_id' => $task->getId(),
            'user_email' => $user->getEmail(),
            'action' => $action
        ]);
    }

    /**
     * Construit le contenu HTML de l'email
     */
    private function buildTaskNotificationHtml(Task $task, User $user, string $action): string
    {
        $statusLabels = [
            'todo' => 'À faire',
            'in_progress' => 'En cours',
            'done' => 'Terminé'
        ];

        $status = $statusLabels[$task->getStatus()] ?? $task->getStatus();
        $dueDate = $task->getDueDate() ? $task->getDueDate()->format('d/m/Y') : 'Non définie';
        $createdBy = $task->getCreatedBy() ? $task->getCreatedBy()->getName() : 'Inconnu';
        $description = $task->getDescription() ?: 'Aucune description';

        return sprintf('
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>OrgaTask - Notification</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .content { background-color: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
                    .task-info { background-color: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                    .task-title { font-size: 1.2em; font-weight: bold; color: #007bff; margin-bottom: 10px; }
                    .info-row { margin: 8px 0; }
                    .label { font-weight: bold; color: #495057; }
                    .status { padding: 4px 8px; border-radius: 4px; font-size: 0.9em; }
                    .status-todo { background-color: #6c757d; color: white; }
                    .status-in_progress { background-color: #ffc107; color: #212529; }
                    .status-done { background-color: #28a745; color: white; }
                    .footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #6c757d; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>📋 OrgaTask</h1>
                        <p>Notification de tâche</p>
                    </div>
                    
                    <div class="content">
                        <h2>Bonjour %s,</h2>
                        
                        <p>Une nouvelle tâche vous a été assignée dans OrgaTask :</p>
                        
                        <div class="task-info">
                            <div class="task-title">%s</div>
                            
                            <div class="info-row">
                                <span class="label">Description :</span> %s
                            </div>
                            
                            <div class="info-row">
                                <span class="label">Statut :</span> 
                                <span class="status status-%s">%s</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="label">Date d\'échéance :</span> %s
                            </div>
                            
                            <div class="info-row">
                                <span class="label">Créée par :</span> %s
                            </div>
                        </div>
                        
                        <p>Vous pouvez consulter cette tâche et la gérer dans votre tableau de bord OrgaTask.</p>
                        
                        <div class="footer">
                            <p>Cet email a été envoyé automatiquement par OrgaTask.</p>
                            <p>Si vous avez des questions, contactez votre administrateur.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ', 
            $user->getName(),
            $task->getTitle(),
            $description,
            $task->getStatus(),
            $status,
            $dueDate,
            $createdBy
        );
    }
}
