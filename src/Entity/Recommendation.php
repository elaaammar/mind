<?php

namespace App\Entity;

use App\Repository\RecommendationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecommendationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Recommendation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'recommendations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Report $report = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): static
    {
        $this->report = $report;

        return $this;
    }

    #[ORM\Column(options: ["default" => false])]
    private ?bool $readByUser = false;

    public function isReadByUser(): ?bool
    {
        return $this->readByUser;
    }

    public function setReadByUser(bool $readByUser): static
    {
        $this->readByUser = $readByUser;

        return $this;
    }

    #[ORM\Column(options: ["default" => false])]
    private bool $isApplied = false;

    public function isApplied(): bool
    {
        return $this->isApplied;
    }

    public function setIsApplied(bool $isApplied): static
    {
        $this->isApplied = $isApplied;
        return $this;
    }

    #[ORM\Column(nullable: true)]
    private ?int $potentialImpact = null;

    public function getPotentialImpact(): ?int
    {
        return $this->potentialImpact;
    }

    public function setPotentialImpact(?int $potentialImpact): static
    {
        $this->potentialImpact = $potentialImpact;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        if ($this->potentialImpact === null) {
            $this->potentialImpact = rand(5, 15); // Default simulated impact
        }
    }
}
