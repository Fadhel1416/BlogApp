<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        // Assume you have some users already in the database
        // You might need to load users first or create a user fixture for testing
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            'password'
        ));
        $user->updatedTimestamps();
        $manager->persist($user);
        $post = new Post();
        $post->setTitle('title');
        $post->setContent('content');
        $post->setAuthor($user);
        $post->updatedTimestamps();

        $manager->persist($post);
            
        $manager->flush();
    }
}
