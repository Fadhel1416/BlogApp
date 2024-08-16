<?php

namespace App\Attribut;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RateLimiting
{
  public function __construct(
    public string $configuration,
  ) {
  }
}