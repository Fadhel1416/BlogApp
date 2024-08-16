<?php

namespace App\Repository;

use App\Dto\CreatePostInput;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

      /**
     * 
     * @param array $content
     * @param \App\Dto\CreatePostInput $input
     * @return \App\Dto\CreatePostInput
     */
    public function hydrate(array $content, CreatePostInput $input): CreatePostInput
    {
      foreach($content as $key => $value) {
        if (property_exists($input, $key)) {
          $input->$key = $value;
        }
      }
      
      return $input;
    }
      /**
     * Search posts by title or content.
     *
     * @param string $query The search query.
     * @return Post[] Returns an array of Post objects.
     */
    public function searchPosts(string $query): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.title LIKE :query')
            ->orWhere('p.content LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery();

        return $qb->getResult();
    }
}
