<?php

namespace App\Controller;

use App\Dto\UserCreateDtoInput;
use App\Entity\User;
use App\Exception\RequestException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;


#[Route('/api', name: 'api_')]
class UserRegisterController extends AbstractController
{


    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\Post(
      path: '/api/register',
      summary: 'Register a new user',
      tags: ['User'],
      requestBody: new OA\RequestBody(
          required: true,
          content: [
              'application/json' => new OA\JsonContent(
                  required: ['email', 'username', 'password'],
                  properties: [
                      new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                      new OA\Property(property: 'username', type: 'string', example: 'username'),
                      new OA\Property(property: 'password', type: 'string', format: 'password', example: 'securepassword')
                  ]
              )
          ]
      ),
      responses: [
          new OA\Response(
              response: '201',
              description: 'User successfully registered',
              content: [
                  'application/json' => new OA\JsonContent(
                      properties: [
                          new OA\Property(property: 'id', type: 'integer', example: 1),
                          new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                          new OA\Property(property: 'username', type: 'string', example: 'username'),
                          new OA\Property(property: 'createdAt', type: 'string', example: 'createdAt'),
                          new OA\Property(property: 'updatedAt', type: 'string', example: 'updatedAt')


                      ]
                  )
              ]
          ),
          new OA\Response(
              response: '400',
              description: 'Invalid input data',
              content: [
                  'application/json' => new OA\JsonContent(
                      properties: [
                          new OA\Property(property: 'error', type: 'string', example: 'Invalid JSON format or validation error')
                      ]
                  )
              ]
          ),
          new OA\Response(
              response: '500',
              description: 'Internal server error',
              content: [
                  'application/json' => new OA\JsonContent(
                      properties: [
                          new OA\Property(property: 'error', type: 'string', example: 'Serialization error')
                      ]
                  )
              ]
          )
      ]
  )]

    public function register(Request $request,UserRepository $userRepository,ValidatorInterface $validator,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $userData = $this->hydrate(
            json_decode($request->getContent(), true),
            new UserCreateDtoInput()
          );
       
          $user = new User();
          $user->setEmail($userData->email);
          $user->setUsername($userData->username);
          $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userData->password
        );
        $user->setPassword($hashedPassword);
        $user->updatedTimestamps();
        $errors = $validator->validate($user);
        if (!empty($errors) && '' != $errors) {
            throw new RequestException((string) $errors[0]);
        }
        $entityManager->persist($user);
        $entityManager->flush();

          return $this->Json($user, Response::HTTP_CREATED,
           ['content-type' => 'application/json'],
            ['groups' => 'user:get','read:resourceEntity:item']);

    }
    private function hydrate(array $content, UserCreateDtoInput $input): UserCreateDtoInput
    {
      foreach($content as $key => $value) {
        if (property_exists($input, $key)) {
          $input->$key = $value;
        }
      }
      
      return $input;
    }
}
