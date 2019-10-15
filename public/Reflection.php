<?php

class A {
    private $var = 'class A';

    public function getVar() {
        return $this->var;
    }

    public function getCl() {
        return function () {
            $this->getVar();
        };
    }
}

class B {
    private $var = 'class B';
}

$a = new A();
$b = new B();

print $a->getVar() . PHP_EOL;

$reflection = new ReflectionClass(get_class($a));
$method  = $reflection->getMethod('getVar');

$closure = $method->getClosure($a);
var_dump($closure);
//$get_var_b  = $closure->bindTo($b, $b);

//print $get_var_b() . PHP_EOL;