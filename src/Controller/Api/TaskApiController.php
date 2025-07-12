<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Service\TaskService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tasks', name: 'api_tasks_')]
class TaskApiController extends AbstractController
{
    public function __construct(
        private TaskService $taskService,
        private MailService $mailService,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $filters = [
            'filter' => $request->query->get('filter', 'all'),
            'status' => $request->query->get('status', 'all'),
            'created_by' => $request->query->get('created_by', ''),
            'assigned_to' => $request->query->get('assigned_to', ''),
            'order' => $request->query->get('order', 'asc'),
        ];
        
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, min(100, $request->query->getInt('limit', 10))); // Limite entre 1 et 100
        
        $result = $this->taskService->getTasks($filters, $page, $limit);
        
        return new JsonResponse([
            'data' => json_decode($this->serializer->serialize($result['tasks'], 'json', ['groups' => ['task:read']]), true),
            'pagination' => $result['pagination']
        ]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        
        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], Response::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse([
            'data' => json_decode($this->serializer->serialize($task, 'json', ['groups' => ['task:read']]), true)
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['message' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $task = new Task();
            
            // Désérialiser les données
            $task = $this->serializer->deserialize(
                $request->getContent(),
                Task::class,
                'json',
                ['groups' => ['task:write']]
            );
            
            // Valider la tâche
            $errors = $validator->validate($task);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                
                return new JsonResponse([
                    'message' => 'Erreurs de validation',
                    'errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Créer la tâche
            $createdTask = $this->taskService->createTask($task, $this->getUser());
            
            // Envoyer les notifications
            $this->mailService->sendTaskCreationNotification($createdTask);
            
            return new JsonResponse([
                'message' => 'Tâche créée avec succès',
                'data' => json_decode($this->serializer->serialize($createdTask, 'json', ['groups' => ['task:read']]), true)
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de la tâche',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $task = $entityManager->getRepository(Task::class)->find($id);
        
        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], Response::HTTP_NOT_FOUND);
        }
        
        try {
            // Désérialiser les données dans la tâche existante
            $this->serializer->deserialize(
                $request->getContent(),
                Task::class,
                'json',
                ['object_to_populate' => $task, 'groups' => ['task:write']]
            );
            
            // Valider la tâche
            $errors = $validator->validate($task);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                
                return new JsonResponse([
                    'message' => 'Erreurs de validation',
                    'errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }
            
            $entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Tâche mise à jour avec succès',
                'data' => json_decode($this->serializer->serialize($task, 'json', ['groups' => ['task:read']]), true)
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour de la tâche',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        
        if (!$task) {
            return new JsonResponse(['message' => 'Tâche non trouvée'], Response::HTTP_NOT_FOUND);
        }
        
        try {
            $entityManager->remove($task);
            $entityManager->flush();
            
            return new JsonResponse(['message' => 'Tâche supprimée avec succès']);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression de la tâche',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
