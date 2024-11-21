<?php

class Setting extends core
{
    public $ID;
    public $type;
    public $name;
    public $value;

    static function option($name)
    {
        $setting = new self();
        $setting->set(name: $name);
        return $setting->value;
    }
}
