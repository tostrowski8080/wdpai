<?php

class Activity {
    private $id;
    private $userId;
    private $categoryId;
    private $title;
    private $startTime;
    private $endTime;
    private $isCompleted;
    private $isRecurring;
    private $recurrencePattern;

    public function __construct(
        string $title,
        string $startTime,
        string $endTime,
        int $userId
    ) {
        $this->title = $title;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->userId = $userId;
        
        $this->isCompleted = false;
        $this->isRecurring = false;
    }

    public function getId(): ?int 
    {
        return $this->id;
    }

    public function setId(int $id): void 
    {
        $this->id = $id;
    }

    public function getUserId(): int 
    {
        return $this->userId;
    }

    public function getCategoryId(): ?int 
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): void 
    {
        $this->categoryId = $categoryId;
    }

    public function getTitle(): string 
    {
        return $this->title;
    }

    public function setTitle(string $title): void 
    {
        $this->title = $title;
    }

    public function getStartTime(): string 
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): void 
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): string 
    {
        return $this->endTime;
    }

    public function setEndTime(string $endTime): void 
    {
        $this->endTime = $endTime;
    }

    public function isCompleted(): bool 
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): void 
    {
        $this->isCompleted = $isCompleted;
    }

    public function isRecurring(): bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(bool $isRecurring): void
    {
        $this->isRecurring = $isRecurring;
    }

    public function getRecurrencePattern(): ?string
    {
        return $this->recurrencePattern;
    }

    public function setRecurrencePattern(?string $pattern): void
    {
        $this->recurrencePattern = $pattern;
    }
}