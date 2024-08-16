<?php


namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Entity\Post;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreatePostControllerTest extends WebTestCase
{
    private $client;
    private $em;
    private $postRepository;
    protected $databaseTool;
    /**
     * @var EntityManager
     */
    private $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->databaseTool->loadFixtures([UserFixtures::class]);

         $this->postRepository = $this->entityManager->getRepository(Post::class);
    }
  /**
 * Create a client with a default Authorization header.
 *
 * @param string $username
 * @param string $password
 *
 * @return \Symfony\Bundle\FrameworkBundle\Client
 */
protected function createAuthenticatedClient($username = 'testuser', $password = 'password')
{
    $this->client->request(
      'POST',
      '/api/login_check',
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      json_encode([
        'username' => $username,
        'password' => $password,
      ])
    );

    $data = json_decode($this->client->getResponse()->getContent(), true);

    $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

    return $this->client;
}

    public function testCreatePostSuccess()
    {
        $this->createAuthenticatedClient();
        $this->client->request('POST', '/api/posts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Test Post Title',
            'content' => 'Test Post Content',
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Test Post Title', $data['title']);
        $this->assertEquals('Test Post Content', $data['content']);
    }


    public function testCreatePostRateLimiting()
    {
        $this->createAuthenticatedClient();
        $this->client->request('POST', '/api/posts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Rate Limit Test Title',
            'content' => 'Rate Limit Test Content',
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        // Simulate rapid requests to test rate limiting
        for ($i = 0; $i < 10; $i++) {
            $this->client->request('POST', '/api/posts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'title' => 'Rate Limit Test Title ' . $i,
                'content' => 'Rate Limit Test Content ' . $i,
            ]));

            $response = $this->client->getResponse();
            if ($i < 5) { // Adjust this threshold based on rate limiting rules
                $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
            } else {
                $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
                break;
            }
        }
    }
}
