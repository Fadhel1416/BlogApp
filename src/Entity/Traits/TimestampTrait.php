<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\HasLifecycleCallbacks]
trait TimestampTrait
{
    #[ORM\Column(type: "datetimetz", nullable: false)]
    #[Groups(['read:resourceEntity:item','post:get'])]
    #[Assert\NotBlank]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetimetz", nullable: false)]
    #[Groups(['read:resourceEntity:item','post:get'])]
    #[Assert\NotBlank]
    protected \DateTimeInterface $updatedAt;

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if (!isset($this->createdAt)) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }
}