<?php
namespace App\Shared\Exceptions;

abstract class BaseException extends \Exception {

    public abstract function getStatusCode() : int;

}