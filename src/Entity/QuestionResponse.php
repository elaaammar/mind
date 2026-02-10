<?php

namespace App\Entity;

use App\Repository\QuestionResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionResponseRepository::class)]
class QuestionResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $response = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionAudit $question = null;

    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AuditAttempt $attempt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): static
    {
        $this->response = $response;

        return $this;
    }

    public function getQuestion(): ?QuestionAudit
    {
        return $this->question;
    }

    public function setQuestion(?QuestionAudit $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAttempt(): ?AuditAttempt
    {
        return $this->attempt;
    }

    public function setAttempt(?AuditAttempt $attempt): static
    {
        $this->attempt = $attempt;

        return $this;
    }
}
