<?php

require __DIR__ . '/vendor/autoload.php';

use function Krak\Fn \{
  head,
  filter,
  filterKeys,
  toArray,
  map,
  flatMap,
  chunkBy,
  fromPairs,
  toPairs
};

class Foo
{
  public $bar = "asd";

  function __construct($bar)
  {
    $this->bar = $bar;
  }
}

$a = [new Foo("3/18/2019 12:00:00 AM"), new Foo("3/18/2019 12:00:00 AM"), new Foo("3/20/2019 12:00:00 AM")];

print_r(toArray(
  chunkBy(function ($item) {
    return $item->bar;
  }, $a)
));

