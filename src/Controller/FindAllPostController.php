<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


#[Route('/api', name: 'api_')]
class FindAllPostController extends AbstractController
{


    #[Route('/posts', name: 'find_allpost', methods: ['GET'])]

    #[OA\Get(
      path: '/api/posts',
      summary: 'Find All  posts',
      tags: ['Post'],
      responses: [
        new OA\Response(
            response: '200',
            description: 'Find All Post',
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
  public function searchPosts(Request $request,PostRepository $postRepository): Response
  {
        $posts = $postRepository->findAll();
        return $this->Json($posts, Response::HTTP_OK,
        ['content-type' => 'application/json'],
        ['groups' => 'post:get','read:resourceEntity:item']);
    }
}
