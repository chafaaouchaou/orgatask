<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer des utilisateurs avec mots de passe hachés
        $user1 = new User();
        $user1->setEmail('john@example.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password123'));
        $user1->setName('John Doe');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('jane@example.com');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password456'));
        $user2->setName('Jane Smith');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('bob@example.com');
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'password789'));
        $user3->setName('Bob Johnson');
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail('alice@example.com');
        $user4->setPassword($this->passwordHasher->hashPassword($user4, 'passwordabc'));
        $user4->setName('Alice Brown');
        $manager->persist($user4);

        // ...existing code...

        // Créer des tâches sans utilisateur assigné
        $task1 = new Task();
        $task1->setTitle('Tâche non assignée 1');
        $task1->setDescription('Cette tâche n\'est assignée à personne');
        $task1->setStatus('todo');
        $task1->setDueDate(new \DateTime('+7 days'));
        $task1->setCreatedBy($user1);
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Tâche non assignée 2');
        $task2->setDescription('Autre tâche sans assignation');
        $task2->setStatus('todo');
        $task2->setDueDate(new \DateTime('+14 days'));
        $task2->setCreatedBy($user2);
        $manager->persist($task2);

        // Créer des tâches avec un seul utilisateur
        $task3 = new Task();
        $task3->setTitle('Développer la page d\'accueil');
        $task3->setDescription('Créer une page d\'accueil responsive');
        $task3->setStatus('todo');
        $task3->setDueDate(new \DateTime('+3 days'));
        $task3->setCreatedBy($user1);
        $task3->addAssignedUser($user1);
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setTitle('Corriger les bugs');
        $task4->setDescription('Résoudre les problèmes signalés');
        $task4->setStatus('in_progress');
        $task4->setDueDate(new \DateTime('+1 day'));
        $task4->setCreatedBy($user2);
        $task4->addAssignedUser($user2);
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setTitle('Tester l\'application');
        $task5->setDescription('Effectuer les tests unitaires');
        $task5->setStatus('done');
        $task5->setDueDate(new \DateTime('-2 days')); // Tâche terminée en retard
        $task5->setCreatedBy($user3);
        $task5->addAssignedUser($user3);
        $manager->persist($task5);

        // Créer des tâches avec 2 utilisateurs
        $task6 = new Task();
        $task6->setTitle('Conception base de données');
        $task6->setDescription('Définir la structure de la base de données');
        $task6->setStatus('todo');
        $task6->setDueDate(new \DateTime('+10 days'));
        $task6->setCreatedBy($user1);
        $task6->addAssignedUser($user1);
        $task6->addAssignedUser($user2);
        $manager->persist($task6);

        $task7 = new Task();
        $task7->setTitle('Intégration API');
        $task7->setDescription('Intégrer les services externes');
        $task7->setStatus('in_progress');
        $task7->setDueDate(new \DateTime('+5 days'));
        $task7->setCreatedBy($user2);
        $task7->addAssignedUser($user2);
        $task7->addAssignedUser($user3);
        $manager->persist($task7);

        // Créer des tâches avec 3 utilisateurs
        $task8 = new Task();
        $task8->setTitle('Réunion projet');
        $task8->setDescription('Organiser la réunion de suivi du projet');
        $task8->setStatus('todo');
        $task8->setDueDate(new \DateTime('+2 days'));
        $task8->setCreatedBy($user1);
        $task8->addAssignedUser($user1);
        $task8->addAssignedUser($user2);
        $task8->addAssignedUser($user3);
        $manager->persist($task8);

        $task9 = new Task();
        $task9->setTitle('Documentation technique');
        $task9->setDescription('Rédiger la documentation complète');
        $task9->setStatus('in_progress');
        $task9->setDueDate(new \DateTime('+21 days'));
        $task9->setCreatedBy($user3);
        $task9->addAssignedUser($user1);
        $task9->addAssignedUser($user3);
        $task9->addAssignedUser($user4);
        $manager->persist($task9);

        $task10 = new Task();
        $task10->setTitle('Formation équipe');
        $task10->setDescription('Former l\'équipe aux nouvelles technologies');
        $task10->setStatus('todo');
        $task10->setDueDate(new \DateTime('+30 days'));
        $task10->setCreatedBy($user4);
        $task10->addAssignedUser($user1);
        $task10->addAssignedUser($user2);
        $task10->addAssignedUser($user4);
        $manager->persist($task10);

        // Tâche urgente en retard
        $task11 = new Task();
        $task11->setTitle('Correction urgente sécurité');
        $task11->setDescription('Corriger la faille de sécurité détectée');
        $task11->setStatus('todo');
        $task11->setDueDate(new \DateTime('-1 day')); // En retard
        $task11->setCreatedBy($user1);
        $task11->addAssignedUser($user1);
        $task11->addAssignedUser($user2);
        $manager->persist($task11);

        // Tâche sans date d'échéance
        $task12 = new Task();
        $task12->setTitle('Amélioration continue');
        $task12->setDescription('Optimiser les performances générales');
        $task12->setStatus('todo');
        // Pas de date d'échéance
        $task12->setCreatedBy($user4);
        $task12->addAssignedUser($user4);
        $manager->persist($task12);

        $manager->flush();
    }
}
