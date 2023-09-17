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

  public function configureManager(bool $isRequired = true): self
  {
    $this->setDefined("manager")->setAllowedTypes("manager", "string");

    if($isRequired) {
      $this->setRequired("manager");
    }

    return $this;
  }

  public function configureTeam(bool $isRequired = true): self
  {
    $this->setDefined("team")->setAllowedTypes("team", "string");

    if($isRequired) {
      $this->setRequired("team");
    }

    return $this;
  }

  
}