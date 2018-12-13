<?php
declare(strict_types=1);
namespace PlugInUnit\Validate;

interface Validate 
{
    /**
     * 检测参数
     */
    public function check() :bool;
}

