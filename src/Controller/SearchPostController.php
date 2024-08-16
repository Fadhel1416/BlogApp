<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


#[Route('/api', name: 'api_')]
class SearchPostController extends AbstractController
{


    #[Route('/posts/search', name: 'search_post', methods: ['GET'])]

    #[OA\Get(
      path: '/api/posts/search',
      summary: 'Search  posts',
      tags: ['Post'],
      parameters: [
        new OA\Parameter(
            name: 'query',
            in: 'query',
            description: 'The content or title of posts to search for',
            required: true,
            schema: new OA\Schema(
                type: 'string'
            )
        )
    ],
      responses: [
        new OA\Response(
            response: '200',
            description: 'find Post',
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
        $posts = $postRepository->searchPosts($request->get('query'));
        return $this->Json($posts, Response::HTTP_OK,
        ['content-type' => 'application/json'],
        ['groups' => 'post:get','read:resourceEntity:item']);
    }
}
