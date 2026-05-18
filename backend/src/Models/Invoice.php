<?php

namespace App\Models;

class Invoice
{
    private ?int $id = null;
    private string $number;
    private int $clientId;
    private string $issueDate;
    private string $dueDate;
    private ?string $paymentMethod = null;
    private float $subtotal;
    private float $taxAmount;
    private float $total;
    private ?string $notes = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getIssueDate(): string
    {
        return $this->issueDate;
    }

    public function setIssueDate(string $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    public function setDueDate(string $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(float $taxAmount): void
    {
        $this->taxAmount = $taxAmount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
