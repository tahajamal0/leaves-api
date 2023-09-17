<?php

namespace App\Controller;

use App\Entity\Team;
use App\OptionsResolver\TeamOptionsResolver;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_', format: "json")]
class TeamController extends AbstractController
{
    #[Route('/teams', name: 'teams', methods: ["GET"])]
    public function index(TeamRepository $teamRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $teams = $teamRepository->findAll();
            $data = $serializer->serialize($teams, 'json', ['groups' => 'team']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            return new BadRequestHttpException($e->getMessage());   
        }
    }

    #[Route('/teams/{id}', name: 'get_team', methods: ["GET"])]
    public function getUserOne(Team $team, SerializerInterface $serializer) : JsonResponse
    {
        $data = $serializer->serialize($team, 'json', ['groups' => 'team']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/teams', name: "create_team", methods: ["POST"])]
    public function createTeam(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, TeamOptionsResolver $userOptionsResolver, SerializerInterface $serializer, UserRepository $userRepository) : JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            
            
            $fields = $userOptionsResolver
            ->configureName(true)
            ->configureDescription(true)    
            ->configureManager(true)    
            ->resolve($requestBody);
            
            $manager = $userRepository->findOneBy(['id' => $fields["manager"]]);
            
            $team = new team();
            $team->setName($fields["name"]);
            $team->setDescription($fields["description"]);
            $manager->setTeam($team);
            $manager->setRoles(['ROLE_COLLABORATOR']);
            $team->setManager($manager);
            
            $errors = $validator->validate($team);
            if(count($errors) > 0){
                throw new InvalidArgumentException((string) $errors);
            }
            
            $entityManager->persist($team);
            $entityManager->flush();
            
            $data = $serializer->serialize($team, 'json', ['groups' => 'team']);

            return new JsonResponse($data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/teams/{id}', name: 'delete_team', methods: ["DELETE"])]
    public function deleteTeam(Team $team, EntityManagerInterface $entityManager, UserRepository $userRepository) : JsonResponse
    {
        $teamUsers = $userRepository->findBy(['team' => $team->getId()]);

        foreach ($teamUsers as $user) {
            $user->setTeam(null);
        }

        $entityManager->remove($team);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/teams/{id}', name: 'update_team', methods: ["PATCH", "PUT"])]
    public function updateTeam(Team $team, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, TeamOptionsResolver $userOptionsResolver, SerializerInterface $serializer, UserRepository $userRepository) : JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            
            $isPutMethod = $request->getMethod() === "PUT";
            
            $fields = $userOptionsResolver
            ->configureName($isPutMethod)
            ->configureDescription($isPutMethod)          
            ->configureManager($isPutMethod)          
            ->resolve($requestBody);
            
            foreach ($fields as $field => $value) {
                switch($field){
                    case "name":
                        $team->setName($fields["name"]);
                        break;
                    case "description":
                        $team->setDescription($fields["description"]);
                        break;
                    case "manager":
                        $manager = $userRepository->findOneBy(['id' => $fields["manager"]]);
                        $manager->setTeam($team);
                        $team->setManager($manager);
                        break;
                }
            }

            
            $errors = $validator->validate($team);
            if(count($errors) > 0){
                throw new InvalidArgumentException((string) $errors);
            }
            
            $entityManager->flush();
            $data = $serializer->serialize($team, 'json', ['groups' => 'team']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
