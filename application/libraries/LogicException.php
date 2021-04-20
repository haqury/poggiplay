<?php
namespace Library;

class LogicException extends \Exception
{
    public function getError()
    {
        return \App::get_ci()->response_error($this->getMessage());
    }
}