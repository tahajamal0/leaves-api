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

  public function configureOwner(bool $isRequired = true): self
  {
    $this->setDefined("owner")->setAllowedTypes("owner", "string");

    if($isRequired) {
      $this->setRequired("owner");
    }

    return $this;
  }
}