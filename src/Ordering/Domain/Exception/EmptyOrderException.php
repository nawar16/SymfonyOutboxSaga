<?php

namespace App\Ordering\Domain\Exception;

use DomainException;

final class EmptyOrderException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The order must contain at least one item');
    }
}