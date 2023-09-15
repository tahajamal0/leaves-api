<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UserOptionsResolver extends OptionsResolver
{
  public function configureFirstName(bool $isRequired = true): self
  {
    $this->setDefined("firstName")->setAllowedTypes("firstName", "string");

    if($isRequired) {
      $this->setRequired("firstName");
    }

    return $this;
  }

  public function configureLastName(bool $isRequired = true): self
  {
    $this->setDefined("lastName")->setAllowedTypes("lastName", "string");

    if($isRequired) {
      $this->setRequired("lastName");
    }

    return $this;
  }

  public function configureEmail(bool $isRequired = true): self
  {
    $this->setDefined("email")->setAllowedTypes("email", "string");

    if($isRequired) {
      $this->setRequired("email");
    }

    return $this;
  }

  public function configurePassword(bool $isRequired = true): self
  {
    $this->setDefined("password")->setAllowedTypes("password", "string");

    if($isRequired) {
      $this->setRequired("password");
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