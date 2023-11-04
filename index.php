<?php

include 'vendor/autoload.php';

$one = new class {
    #[\Signal\Mapping\Db\Column(name: 'a')]
    private int $a = 1;
};

$two = new class {
    #[\Signal\Mapping\Db\Column(name: 'a')]
    private ?int $a = null;
    public function getA()
    {
        return $this->a;
    }
};

$mapper = \Signal\Mapping\Db\TableRowMapper::withTable('test');

$mapper->mapProperties($two, $one);
echo $two->getA();