<?php

namespace App\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)] final class Route {

    public function __construct(private string $path, private string $name = '', private string|array $method = 'GET') {}

}
