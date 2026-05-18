<?php

namespace App\Models;

class Client
{
    private ?int $id = null;
    private string $documentNumber;
    private string $businessName;
    private ?string $email = null;
    private ?string $address = null;
    private int $status;
    private ?int $deletedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(string $documentNumber): void
    {
        $this->documentNumber = $documentNumber;
    }

    public function getBusinessName(): string
    {
        return $this->businessName;
    }

    public function setBusinessName(string $businessName): void
    {
        $this->businessName = $businessName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?int $deletedBy): void
    {
        $this->deletedBy = $deletedBy;
    }
}
