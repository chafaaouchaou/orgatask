<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer des utilisateurs
        $user1 = new User();
        $user1->setEmail('john@example.com');
        $user1->setPassword('password123');
        $user1->setName('John Doe');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('jane@example.com');
        $user2->setPassword('password456');
        $user2->setName('Jane Smith');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('bob@example.com');
        $user3->setPassword('password789');
        $user3->setName('Bob Johnson');
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail('alice@example.com');
        $user4->setPassword('passwordabc');
        $user4->setName('Alice Brown');
        $manager->persist($user4);

        // Créer des tâches sans utilisateur assigné
        $task1 = new Task();
        $task1->setTitle('Tâche non assignée 1');
        $task1->setDescription('Cette tâche n\'est assignée à personne');
        $task1->setStatus('todo');
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Tâche non assignée 2');
        $task2->setDescription('Autre tâche sans assignation');
        $task2->setStatus('todo');
        $manager->persist($task2);

        // Créer des tâches avec un seul utilisateur
        $task3 = new Task();
        $task3->setTitle('Développer la page d\'accueil');
        $task3->setDescription('Créer une page d\'accueil responsive');
        $task3->setStatus('todo');
        $task3->addAssignedUser($user1);
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setTitle('Corriger les bugs');
        $task4->setDescription('Résoudre les problèmes signalés');
        $task4->setStatus('in_progress');
        $task4->addAssignedUser($user2);
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setTitle('Tester l\'application');
        $task5->setDescription('Effectuer les tests unitaires');
        $task5->setStatus('done');
        $task5->addAssignedUser($user3);
        $manager->persist($task5);

        // Créer des tâches avec 2 utilisateurs
        $task6 = new Task();
        $task6->setTitle('Conception base de données');
        $task6->setDescription('Définir la structure de la base de données');
        $task6->setStatus('todo');
        $task6->addAssignedUser($user1);
        $task6->addAssignedUser($user2);
        $manager->persist($task6);

        $task7 = new Task();
        $task7->setTitle('Intégration API');
        $task7->setDescription('Intégrer les services externes');
        $task7->setStatus('in_progress');
        $task7->addAssignedUser($user2);
        $task7->addAssignedUser($user3);
        $manager->persist($task7);

        // Créer des tâches avec 3 utilisateurs
        $task8 = new Task();
        $task8->setTitle('Réunion projet');
        $task8->setDescription('Organiser la réunion de suivi du projet');
        $task8->setStatus('todo');
        $task8->addAssignedUser($user1);
        $task8->addAssignedUser($user2);
        $task8->addAssignedUser($user3);
        $manager->persist($task8);

        $task9 = new Task();
        $task9->setTitle('Documentation technique');
        $task9->setDescription('Rédiger la documentation complète');
        $task9->setStatus('in_progress');
        $task9->addAssignedUser($user1);
        $task9->addAssignedUser($user3);
        $task9->addAssignedUser($user4);
        $manager->persist($task9);

        $task10 = new Task();
        $task10->setTitle('Formation équipe');
        $task10->setDescription('Former l\'équipe aux nouvelles technologies');
        $task10->setStatus('todo');
        $task10->addAssignedUser($user1);
        $task10->addAssignedUser($user2);
        $task10->addAssignedUser($user4);
        $manager->persist($task10);

        $manager->flush();
    }
}
