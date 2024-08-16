<?php

namespace App\Controller;

use App\Entity\Post;
use App\Exception\RequestException;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


#[Route('/api', name: 'api_')]
class FindPostController extends AbstractController
{


    #[Route('/posts/{id}', name: 'find_post', methods: ['GET'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the post to be updated',
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Get(
      path: '/api/posts/{id}',
      summary: 'Find a Post',
      tags: ['Post'],
      responses: [
          new OA\Response(
              response: '201',
              description: 'Post successfully modified',
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
  public function findPost(Request $request,PostRepository $postRepository): Response
  {
        /**
         * @var Post|null $post
         */
        $post = $postRepository->findOneBy(['id'=>$request->get('id')]);
        if($post === null) {
            throw new RequestException('Post not found');
        }
    

        return $this->Json($post, Response::HTTP_CREATED,
        ['content-type' => 'application/json'],
        ['groups' => 'post:get','read:resourceEntity:item']);
    }
}
