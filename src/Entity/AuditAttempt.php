<?php

namespace App\Entity;

use App\Repository\AuditAttemptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditAttemptRepository::class)]
class AuditAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $participantName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'attempts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Audit $audit = null;

    #[ORM\OneToMany(mappedBy: 'attempt', targetEntity: QuestionResponse::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $responses;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->responses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipantName(): ?string
    {
        return $this->participantName;
    }

    public function setParticipantName(string $participantName): static
    {
        $this->participantName = $participantName;

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

    public function getAudit(): ?Audit
    {
        return $this->audit;
    }

    public function setAudit(?Audit $audit): static
    {
        $this->audit = $audit;

        return $this;
    }

    /**
     * @return Collection<int, QuestionResponse>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(QuestionResponse $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setAttempt($this);
        }

        return $this;
    }

    public function removeResponse(QuestionResponse $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getAttempt() === $this) {
                $response->setAttempt(null);
            }
        }

        return $this;
    }
}
