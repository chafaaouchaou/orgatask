<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserSearchService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function searchUsers(string $query = '', int $limit = 10): array
    {
        if (empty($query)) {
            // Retourner les premiers utilisateurs si pas de recherche
            $users = $this->userRepository->findBy([], ['name' => 'ASC'], $limit);
        } else {
            // Rechercher par nom ou email
            $users = $this->userRepository->findByNameOrEmail($query, $limit);
        }

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'display' => $user->getName() . ' (' . $user->getEmail() . ')'
            ];
        }

        return $data;
    }

    public function searchUsersJsonResponse(string $query = '', int $limit = 10): JsonResponse
    {
        $data = $this->searchUsers($query, $limit);
        return new JsonResponse($data);
    }
}
