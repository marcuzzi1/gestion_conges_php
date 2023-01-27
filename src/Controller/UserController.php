<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV1;

#[Route('/api/employee', name: 'api_employee_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(UserRepository $repo): JsonResponse
    {
        $usersList = $repo->findAll();
        $users = [];
        foreach ($usersList as $user) {
            $users[] = [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'vacationDays' => $user->getVacationDays(),
                'compensatoryTimeDays' => $user->getCompensatoryTimeDays(),
            ];
        }
        return $this->json([
            'users' => $users,
        ]);
    }

    #[Route('/save', name: 'create', methods: ['POST'])]
    public function create(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setVacationDays($data['vacationDays']);
        $user->setCompensatoryTimeDays($data['compensatoryTimeDays']);
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User created',
        ]);
    }

    #[Route('/save/{id}', name: 'update_or_create', methods: ['PUT'])]
    public function updateOrCreate(int $id, UserRepository $repo, Request $req, EntityManagerInterface $em) : JsonResponse
    {
        $user = $repo->findOneBy(['id' => $id]);
        if ($user === null) {
            // Si l'utilisateur n'existe pas, on le crée
            $data = json_decode($req->getContent(), true);
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setVacationDays($data['vacationDays']);
            $user->setCompensatoryTimeDays($data['compensatoryTimeDays']);
            $em->persist($user);
            $em->flush();

            return $this->json([
                'message' => 'User created',
            ]);
        }

        // Si l'utilisateur existe, on le met à jour
        $data = json_decode($req->getContent(), true);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setVacationDays($data['vacationDays']);
        $user->setCompensatoryTimeDays($data['compensatoryTimeDays']);
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User updated',
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, UserRepository $repo, EntityManagerInterface $em) : JsonResponse
    {
        $user = $repo->findOneBy(['id' => $id]);

        if ($user === null) {
            return $this->json([
                'message' => 'User not found',
            ]);
        }

        $em->remove($user);
        $em->flush();

        return $this->json([
            'message' => 'User deleted',
        ]);
    }
}
