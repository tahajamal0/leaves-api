<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamOptionsResolver extends OptionsResolver
{
  public function configureName(bool $isRequired = true): self
  {
    $this->setDefined("name")->setAllowedTypes("name", "string");

    if($isRequired) {
      $this->setRequired("name");
    }

    return $this;
  }

  public function configureDescription(bool $isRequired = true): self
  {
    $this->setDefined("description")->setAllowedTypes("description", "string");

    if($isRequired) {
      $this->setRequired("description");
    }

    return $this;
  }
}