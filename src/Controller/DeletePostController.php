<?php

namespace App\Controller;

use App\Entity\Post;
use App\Exception\ForbiddenException;
use App\Exception\RequestException;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


#[Route('/api', name: 'api_')]
class DeletePostController extends AbstractController
{


    #[Route('/posts/{id}', name: 'delete_post', methods: ['DELETE'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the post to be deleted',
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Delete(
      path: '/api/posts/{id}',
      summary: 'Delete  Post',
      tags: ['Post'],
      responses: [
     
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
  public function deletePost(Request $request, EntityManagerInterface $entityManager,PostRepository $postRepository): Response
  {
        /**
         * @var Post|null $post
         */
        $post = $postRepository->findOneBy(['id'=>$request->get('id')]);
        if($post === null) {
            throw new RequestException('Post not found');
        }
        if($post->getAuthor() !== $this->getUser()) {
            throw new ForbiddenException('Unauthorized');
        }
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->Json('Post was deleted', Response::HTTP_NO_CONTENT,
        ['content-type' => 'application/json'],
        ['groups' => 'post:get','read:resourceEntity:item']);
    }
}
