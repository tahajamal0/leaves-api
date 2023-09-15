<?php

namespace App\Controller;

use App\Entity\Leave;
use App\OptionsResolver\LeaveOptionsResolver;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\Repository\LeaveRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
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
class LeaveController extends AbstractController
{
    #[Route('/leaves', name: 'todos', methods: ["GET"])]
    public function index(LeaveRepository $leaveRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $leaves = $leaveRepository->findAll();
            
            $data = $serializer->serialize($leaves, 'json', ['groups' => 'leave']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            return new BadRequestHttpException($e->getMessage());   
        }
    }

    #[Route('/leaves/{id}', name: 'get_leave', methods: ["GET"])]
    public function getLeave(Leave $leave, SerializerInterface $serializer) : JsonResponse
    {
        $data = $serializer->serialize($leave, 'json', ['groups' => 'leave']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/leaves', name: "create_leave", methods: ["POST"])]
    public function createLeave(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, LeaveOptionsResolver $leaveOptionsResolver, UserRepository $userRepository, SerializerInterface $serializer) : JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            
            
            $fields = $leaveOptionsResolver
            ->configureStartAt(true)
            ->configureEndAt(true)
            ->configureType(true)        
            ->configureOwner(true)        
            ->resolve($requestBody);

            $owner = $userRepository->findOneBy(['id' => $fields["owner"]]);
            
            $leave = new Leave();
            $leave->setStartAt(new DateTimeImmutable($fields["startAt"]));
            $leave->setEndAt(new DateTimeImmutable($fields["endAt"]));
            $leave->setType($fields["type"]);
            $leave->setOwner($owner);

            
            $errors = $validator->validate($leave);
            if(count($errors) > 0){
                throw new InvalidArgumentException((string) $errors);
            }
            
            $entityManager->persist($leave);
            $entityManager->flush();
            
            $data = $serializer->serialize($leave, 'json', ['groups' => 'leave']);

            return new JsonResponse($data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/leaves/{id}', name: 'delete_leave', methods: ["DELETE"])]
    public function deleteLeave(Leave $leave, EntityManagerInterface $entityManager) : JsonResponse
    {
        $entityManager->remove($leave);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/leaves/{id}', name: 'update_leave', methods: ["PATCH", "PUT"])]
    public function updateLeave(Leave $leave, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, LeaveOptionsResolver $leaveOptionsResolver, SerializerInterface $serializer) : JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            
            $isPutMethod = $request->getMethod() === "PUT";
            
            $fields = $leaveOptionsResolver
            ->configureStartAt($isPutMethod)
            ->configureEndAt($isPutMethod)
            ->configureType($isPutMethod)            
            ->resolve($requestBody);
            
            foreach ($fields as $field => $value) {
                switch($field){
                    case "startAt":
                        $leave->setStartAt(new DateTimeImmutable($fields["startAt"]));
                        break;
                    case "endAt":
                        $leave->setEndAt(new DateTimeImmutable($fields["endAt"]));
                        break;
                    case "type":
                        $leave->setType($fields["type"]);
                        break;
                }
            }

            
            $errors = $validator->validate($leave);
            if(count($errors) > 0){
                throw new InvalidArgumentException((string) $errors);
            }
            
            $entityManager->flush();
            
            $data = $serializer->serialize($leave, 'json', ['groups' => 'leave']);

            return new JsonResponse($data, 200, [], true);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
