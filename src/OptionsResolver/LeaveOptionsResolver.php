<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class LeaveOptionsResolver extends OptionsResolver
{
  public function configureStartAt(bool $isRequired = true): self
  {
    $this->setDefined("startAt")->setAllowedTypes("startAt", "string");

    if($isRequired) {
      $this->setRequired("startAt");
    }

    return $this;
  }

  public function configureEndAt(bool $isRequired = true): self
  {
    $this->setDefined("endAt")->setAllowedTypes("endAt", "string");

    if($isRequired) {
      $this->setRequired("endAt");
    }

    return $this;
  }

  public function configureType(bool $isRequired = true): self
  {
    $this->setDefined("type")->setAllowedTypes("type", "string");

    if($isRequired) {
      $this->setRequired("type");
    }

    return $this;
  }

  public function configureStatus(bool $isRequired = true): self
  {
    $this->setDefined("status")->setAllowedTypes("status", "string");

    if($isRequired) {
      $this->setRequired("status");
    }

    return $this;
  }

  public function configureOwner(bool $isRequired = true): self
  {
    $this->setDefined("owner")->setAllowedTypes("owner", "string");

    if($isRequired) {
      $this->setRequired("owner");
    }

    return $this;
  }

  public function configureComment(bool $isRequired = true): self
  {
    $this->setDefined("comment")->setAllowedTypes("comment", "string");

    if($isRequired) {
      $this->setRequired("comment");
    }

    return $this;
  }
}