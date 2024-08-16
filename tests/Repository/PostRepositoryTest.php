<?php

declare(strict_types=1);

namespace App\tests\Repository;

use App\DataFixtures\PostFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Post;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * test Post repository.
 */
class PostRepositoryTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        /** charger les données de l'advp dans la base de donnée */
        $this->databaseTool->loadFixtures([PostFixtures::class]);
    }
    /**
     * teardown the database 
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
        $this->databaseTool = null;
        gc_collect_cycles();
    }
    
    /**
     * test find post
     *
     * @return void
     */
    public function testFindPost()
    {
        $postRep = $this->entityManager->getRepository(Post::class);
        $post = $postRep->findOneBy(['id' => 1]);
        $this->assertSame('title', $post->getTitle());
        $this->assertSame('content', $post->getContent());
        $this->assertNotEmpty( $post->getCreatedAt());
        $this->assertNotEmpty( $post->getUpdatedAt());
    }
    public function testSearchPosts()
    {
        $postRep = $this->entityManager->getRepository(Post::class);
        $posts = $postRep->searchPosts('title');
        $this->assertCount(1, $posts);
    }
}