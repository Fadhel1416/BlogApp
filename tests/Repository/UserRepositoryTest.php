<?php

declare(strict_types=1);

namespace App\tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * test User repository.
 */
class UserRepositoryTest extends WebTestCase
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
        $this->databaseTool->loadFixtures([UserFixtures::class]);
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
     * test find user
     *
     * @return void
     */
    public function testFindUser()
    {
        $userRep = $this->entityManager->getRepository(User::class);
        $user = $userRep->findOneBy(['id' => 1]);
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('testuser', $user->getUsername());
        $this->assertNotEmpty( $user->getCreatedAt());
        $this->assertNotEmpty( $user->getUpdatedAt());
    }
}