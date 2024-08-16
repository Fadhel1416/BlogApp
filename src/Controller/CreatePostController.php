<?php

namespace App\Controller;

use App\Attribut\RateLimiting;
use App\Dto\CreatePostInput;
use App\Entity\Post;
use App\Exception\RequestException;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;


#[Route('/api', name: 'api_')]
class CreatePostController extends AbstractController
{

    #[RateLimiting('entity_create')]
    #[Route('/posts', name: 'create_post', methods: ['POST'])]
    #[OA\Post(
      path: '/api/posts',
      summary: 'Register a new post',
      tags: ['Post'],
      requestBody: new OA\RequestBody(
          required: true,
          content: [
              'application/json' => new OA\JsonContent(
                  required: ['title', 'content'],
                  properties: [
                      new OA\Property(property: 'title', type: 'string', format: 'title', example: 'Post title'),
                      new OA\Property(property: 'content', type: 'string', example: 'Post Content'),
                  ]
              )
          ]
      ),
      responses: [
          new OA\Response(
              response: '201',
              description: 'Post successfully registered',
              content: [
                  'application/json' => new OA\JsonContent(
                      properties: [
                          new OA\Property(property: 'id', type: 'integer', example: 1),
                          new OA\Property(property: 'title', type: 'string', example: 'Post Ttile'),
                          new OA\Property(property: 'content', type: 'string', example: 'Post Content'),
                          new OA\Property(property: 'author', type: 'object'),
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
    public function createPost(Request $request,ValidatorInterface $validator, EntityManagerInterface $entityManager,PostRepository $postRepository): Response
    {
        $postData = $postRepository->hydrate(
            json_decode($request->getContent(), true),
            new CreatePostInput()
          );
       
        $post = new Post();
        $post->setTitle($postData->title);
        $post->setContent($postData->content);
        $post->setAuthor($this->getUser());
        $post->updatedTimestamps();
        $errors = $validator->validate($post);
        if (!empty($errors) && '' != $errors) {
            throw new RequestException((string) $errors[0]);
        }
        $entityManager->persist($post);
        $entityManager->flush();

          return $this->Json($post, Response::HTTP_CREATED,
           ['content-type' => 'application/json'],
            ['groups' => 'post:get','read:resourceEntity:item']);
    }

}
