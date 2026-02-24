<?php

namespace App\Entity;

use App\Repository\ReportVersionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportVersionRepository::class)]
#[ORM\Table(name: 'report_version')]
class ReportVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Report $report = null;

    #[ORM\Column(length: 10)]
    private string $action = 'update';

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $data = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $loggedAt = null;

    #[ORM\Column]
    private int $version = 1;

    public function getId(): ?int { return $this->id; }
    public function getReport(): ?Report { return $this->report; }
    public function setReport(?Report $report): static { $this->report = $report; return $this; }
    public function getAction(): string { return $this->action; }
    public function setAction(string $action): static { $this->action = $action; return $this; }
    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): static { $this->username = $username; return $this; }
    public function getData(): ?array { return $this->data; }
    public function setData(?array $data): static { $this->data = $data; return $this; }
    public function getLoggedAt(): ?\DateTimeImmutable { return $this->loggedAt; }
    public function setLoggedAt(\DateTimeImmutable $loggedAt): static { $this->loggedAt = $loggedAt; return $this; }
    public function getVersion(): int { return $this->version; }
    public function setVersion(int $version): static { $this->version = $version; return $this; }
}
