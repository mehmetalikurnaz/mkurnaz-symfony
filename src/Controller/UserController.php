<?php

namespace App\Controller;

use App\Entity\User;
use App\Formatter\ApiResponseFormatter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ApiResponseFormatter $apiResponseFormatter,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_user_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $transformedUsers = array_map(fn(User $user) => $user->toArray(), $users);

        return $this->apiResponseFormatter
            ->withData($transformedUsers)
            ->response();
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->apiResponseFormatter
                ->withError('User not found', Response::HTTP_NOT_FOUND)
                ->response();
        }

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->response();
    }

    #[Route('', name: 'create_user', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->apiResponseFormatter
                ->withError('Email and password are required', Response::HTTP_BAD_REQUEST)
                ->response();
        }

        $user = new User($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->apiResponseFormatter
                ->withError((string) $errors, Response::HTTP_BAD_REQUEST)
                ->response();
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->withMessage('User created successfully')
            ->response(Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_user', methods: ['PATCH'])]
    public function update(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->apiResponseFormatter
                ->withError('User not found', Response::HTTP_NOT_FOUND)
                ->response();
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->apiResponseFormatter
                ->withError((string) $errors, Response::HTTP_BAD_REQUEST)
                ->response();
        }

        $this->entityManager->flush();

        return $this->apiResponseFormatter
            ->withData($user->toArray())
            ->withMessage('User updated successfully')
            ->response();
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->apiResponseFormatter
                ->withError('User not found', Response::HTTP_NOT_FOUND)
                ->response();
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->apiResponseFormatter
            ->withMessage('User deleted successfully')
            ->response();
    }
}
