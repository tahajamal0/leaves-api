<?php

namespace App\Controller;

use App\Entity\User;
use App\OptionsResolver\UserOptionsResolver;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\Repository\LeaveRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_', format: "json")]
class UserController extends AbstractController
{
    #[Route('/users', name: 'users', methods: ["GET"])]
    public function index(UserRepository $userRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        try {
            $queryParams = $request->query->all();
            $findUser = $queryParams["email"] ?? '';

            if($findUser !== '') {
                $users = $userRepository->findOneBy(['email' => $findUser]);
            }
            else{
                $users = $userRepository->findAll();
            }

            $data = $serializer->serialize($users, 'json', ['groups' => 'user']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            return new BadRequestHttpException($e->getMessage());   
        }
    }

    #[Route('/users/team/{id}', name: 'usersByTeam', methods: ["GET"])]
    public function getUsersByTeam(int $id, UserRepository $userRepository, SerializerInterface $serializer, Request $request, TeamRepository $teamRepository): JsonResponse
    {
        try {
            $teams = $teamRepository->findOneBy(['id' => $id]);
            $users = $teams ? $teams->getUsers() : [];

            $data = $serializer->serialize($users, 'json', ['groups' => 'user']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            return new BadRequestHttpException($e->getMessage());   
        }
    }

    #[Route('/users/{id}', name: 'get_user', methods: ["GET"])]
    public function getUserOne(User $user, SerializerInterface $serializer) : JsonResponse
    {
        $data = $serializer->serialize($user, 'json', ['groups' => 'user']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/users', name: "create_user", methods: ["POST"])]
    public function createUser(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserOptionsResolver $userOptionsResolver, UserPasswordHasherInterface $passwordHasher, TeamRepository $teamRepository) : JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            
            
            $fields = $userOptionsResolver
            ->configureFirstName(true)
            ->configureLastName(true)
            ->configureEmail(true)            
            ->configurePassword(true)     
            ->resolve($requestBody);
            
            $user = new User();
            $user->setFirstName($fields["firstName"]);
            $user->setLastName($fields["lastName"]);
            $user->setEmail($fields["email"]);
            $user->setPassword($passwordHasher->hashPassword($user, $fields["password"]));
            
            $errors = $validator->validate($user);
            if(count($errors) > 0){
                throw new InvalidArgumentException((string) $errors);
            }
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->json($user, status: Response::HTTP_CREATED);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/users/{id}', name: 'delete_user', methods: ["DELETE"])]
    public function deleteUser(int $id, User $user, EntityManagerInterface $entityManager, LeaveRepository $leaveRepository, TeamRepository $teamRepository) : JsonResponse
    {

        $team = $teamRepository->findOneBy(['manager' => $id]);

        $status = 'all';
        $order = 'ASC';
        $size = 100;
        $page = 1;

        $leaves = $leaveRepository->findByUserId($id, $status, $page, $size, $order);

        foreach ($leaves as $leave) {
            $entityManager->remove($leave);
            $entityManager->flush();
        }

        $team && $team->setManager();

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'update_user', methods: ["PATCH", "PUT"])]
    public function updateUser(User $user, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserOptionsResolver $userOptionsResolver, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, TeamRepository $teamRepository) : JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            
            $isPutMethod = $request->getMethod() === "PUT";
            
            $fields = $userOptionsResolver
            ->configureFirstName($isPutMethod)
            ->configureLastName($isPutMethod)         
            ->configurePassword($isPutMethod)
            ->configureEmail($isPutMethod)
            ->configurePassword($isPutMethod)
            ->configureTeam($isPutMethod)          
            ->resolve($requestBody);
            
            foreach ($fields as $field => $value) {
                switch($field){
                    case "firstName":
                        $user->setFirstName($fields["firstName"]);
                        break;
                    case "lastName":
                        $user->setLastName($fields["lastName"]);
                        break;
                    case "password":
                        $user->setPassword($passwordHasher->hashPassword($user, $fields["password"]));
                        break;
                    case "team":
                        $team = $teamRepository->findOneBy(['id' => $fields["team"]]);
                        $user->setTeam($team);
                        break;
                    case "email":
                        $user->setEmail($fields["email"]);
                        break;
                }
            }

            
            $errors = $validator->validate($user);
            if(count($errors) > 0){
                throw new InvalidArgumentException((string) $errors);
            }
            
            $entityManager->flush();
            
            $data = $serializer->serialize($user, 'json', ['groups' => 'user']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
