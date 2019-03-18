<?php

/* This file is was automatically generated. */
namespace Krak\Fn\Curried;

function method($name, ...$optionalArgs)
{
    return function ($data) use($name, $optionalArgs) {
        return $data->{$name}(...$optionalArgs);
    };
}
function prop(string $key, $else = null)
{
    return function ($data) use($key, $else) {
        return \property_exists($data, $key) ? $data->{$key} : $else;
    };
}
function index($key, $else = null)
{
    return function (array $data) use($key, $else) {
        return \array_key_exists($key, $data) ? $data[$key] : $else;
    };
}
function setProp(string $key)
{
    return function ($value) use($key) {
        return function ($data) use($value, $key) {
            $data->{$key} = $value;
            return $data;
        };
    };
}
function setIndex($key)
{
    return function ($value) use($key) {
        return function (array $data) use($value, $key) {
            $data[$key] = $value;
            return $data;
        };
    };
}
function setIndexIn(array $keys)
{
    return function ($value) use($keys) {
        return function (array $data) use($value, $keys) {
            return \Krak\Fn\updateIndexIn($keys, function () use($value) {
                return $value;
            }, $data);
        };
    };
}
function propIn(array $props, $else = null)
{
    return function ($obj) use($props, $else) {
        foreach ($props as $prop) {
            if (!\is_object($obj) || !\property_exists($obj, $prop)) {
                return $else;
            }
            $obj = $obj->{$prop};
        }
        return $obj;
    };
}
function indexIn(array $keys, $else = null)
{
    return function (array $data) use($keys, $else) {
        foreach ($keys as $part) {
            if (!\is_array($data) || !\array_key_exists($part, $data)) {
                return $else;
            }
            $data = $data[$part];
        }
        return $data;
    };
}
function hasIndexIn(array $keys)
{
    return function (array $data) use($keys) {
        foreach ($keys as $key) {
            if (!\is_array($data) || !\array_key_exists($key, $data)) {
                return false;
            }
            $data = $data[$key];
        }
        return true;
    };
}
function updateIndexIn(array $keys)
{
    return function (callable $update) use($keys) {
        return function (array $data) use($update, $keys) {
            $curData =& $data;
            foreach (\array_slice($keys, 0, -1) as $key) {
                if (!\array_key_exists($key, $curData)) {
                    throw new \RuntimeException('Could not updateIn because the keys ' . \implode(' -> ', $keys) . ' could not be found.');
                }
                $curData =& $curData[$key];
            }
            $lastKey = $keys[count($keys) - 1];
            $curData[$lastKey] = $update($curData[$lastKey] ?? null);
            return $data;
        };
    };
}
function assign($obj)
{
    return function (iterable $iter) use($obj) {
        foreach ($iter as $key => $value) {
            $obj->{$key} = $value;
        }
        return $obj;
    };
}
function join(string $sep)
{
    return function (iterable $iter) use($sep) {
        return \Krak\Fn\reduce(function ($acc, $v) use($sep) {
            return $acc ? $acc . $sep . $v : $v;
        }, $iter, "");
    };
}
function construct($className)
{
    return function (...$args) use($className) {
        return new $className(...$args);
    };
}
function spread(callable $fn)
{
    return function (array $data) use($fn) {
        return $fn(...$data);
    };
}
function dd(callable $dump = null, callable $die = null)
{
    return function ($value) use($dump, $die) {
        $dump = $dump ?: (function_exists('dump') ? 'dump' : 'var_dump');
        $dump($value);
        ($die ?? function () {
            die;
        })();
    };
}
function takeWhile(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $k => $v) {
            if ($predicate($v)) {
                (yield $k => $v);
            } else {
                return;
            }
        }
    };
}
function dropWhile(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        $stillDropping = true;
        foreach ($iter as $k => $v) {
            if ($stillDropping && $predicate($v)) {
                continue;
            } else {
                if ($stillDropping) {
                    $stillDropping = false;
                }
            }
            (yield $k => $v);
        }
    };
}
function take(int $num)
{
    return function (iterable $iter) use($num) {
        return \Krak\Fn\slice(0, $iter, $num);
    };
}
function drop(int $num)
{
    return function (iterable $iter) use($num) {
        return \Krak\Fn\slice($num, $iter);
    };
}
function slice(int $start, $length = INF)
{
    return function (iterable $iter) use($start, $length) {
        assert($start >= 0);
        $i = 0;
        $end = $start + $length - 1;
        foreach ($iter as $k => $v) {
            if ($start <= $i && $i <= $end) {
                (yield $k => $v);
            }
            $i += 1;
            if ($i > $end) {
                return;
            }
        }
    };
}
function chunk(int $size)
{
    return function (iterable $iter) use($size) {
        assert($size > 0);
        $chunk = [];
        foreach ($iter as $v) {
            $chunk[] = $v;
            if (\count($chunk) == $size) {
                (yield $chunk);
                $chunk = [];
            }
        }
        if ($chunk) {
            (yield $chunk);
        }
    };
}
function chunkBy(callable $fn, ?int $maxSize = null)
{
    return function (iterable $iter) use($fn, $maxSize) {
        assert($maxSize === null || $maxSize > 0);
        $group = [];
        $groupKey = null;
        foreach ($iter as $v) {
            $curGroupKey = $fn($v);
            $shouldYieldGroup = $groupKey !== null && $groupKey !== $curGroupKey || $maxSize !== null && \count($group) >= $maxSize;
            if ($shouldYieldGroup) {
                (yield $group);
                $group = [];
            }
            $group[] = $v;
            $groupKey = $curGroupKey;
        }
        if (\count($group)) {
            (yield $group);
        }
    };
}
function groupBy(callable $fn, ?int $maxSize = null)
{
    return function (iterable $iter) use($fn, $maxSize) {
        return \Krak\Fn\chunkBy($fn, $iter, $maxSize);
    };
}
function range($start, $step = null)
{
    return function ($end) use($start, $step) {
        if ($start == $end) {
            (yield $start);
        } else {
            if ($start < $end) {
                $step = $step ?: 1;
                if ($step <= 0) {
                    throw new \InvalidArgumentException('Step must be greater than 0.');
                }
                for ($i = $start; $i <= $end; $i += $step) {
                    (yield $i);
                }
            } else {
                $step = $step ?: -1;
                if ($step >= 0) {
                    throw new \InvalidArgumentException('Step must be less than 0.');
                }
                for ($i = $start; $i >= $end; $i += $step) {
                    (yield $i);
                }
            }
        }
    };
}
function op(string $op)
{
    return function ($b) use($op) {
        return function ($a) use($b, $op) {
            switch ($op) {
                case '==':
                case 'eq':
                    return $a == $b;
                case '!=':
                case 'neq':
                    return $a != $b;
                case '===':
                    return $a === $b;
                case '!==':
                    return $a !== $b;
                case '>':
                case 'gt':
                    return $a > $b;
                case '>=':
                case 'gte':
                    return $a >= $b;
                case '<':
                case 'lt':
                    return $a < $b;
                case '<=':
                case 'lte':
                    return $a <= $b;
                case '+':
                    return $a + $b;
                case '-':
                    return $a - $b;
                case '*':
                    return $a * $b;
                case '**':
                    return $a ** $b;
                case '/':
                    return $a / $b;
                case '%':
                    return $a % $b;
                case '.':
                    return $a . $b;
                default:
                    throw new \LogicException('Invalid operator ' . $op);
            }
        };
    };
}
function flatMap(callable $map)
{
    return function (iterable $iter) use($map) {
        foreach ($iter as $k => $v) {
            foreach ($map($v) as $k => $v) {
                (yield $k => $v);
            }
        }
    };
}
function flatten($levels = INF)
{
    return function (iterable $iter) use($levels) {
        if ($levels == 0) {
            return $iter;
        } else {
            if ($levels == 1) {
                foreach ($iter as $k => $v) {
                    if (\is_iterable($v)) {
                        foreach ($v as $k1 => $v1) {
                            (yield $k1 => $v1);
                        }
                    } else {
                        (yield $k => $v);
                    }
                }
            } else {
                foreach ($iter as $k => $v) {
                    if (\is_iterable($v)) {
                        foreach (flatten($v, $levels - 1) as $k1 => $v1) {
                            (yield $k1 => $v1);
                        }
                    } else {
                        (yield $k => $v);
                    }
                }
            }
        }
    };
}
function when(callable $if)
{
    return function (callable $then) use($if) {
        return function ($value) use($then, $if) {
            return $if($value) ? $then($value) : $value;
        };
    };
}
function within(array $fields)
{
    return function (iterable $iter) use($fields) {
        return \Krak\Fn\filterKeys(\Krak\Fn\Curried\inArray($fields), $iter);
    };
}
function without(array $fields)
{
    return function (iterable $iter) use($fields) {
        return \Krak\Fn\filterKeys(\Krak\Fn\Curried\not(\Krak\Fn\Curried\inArray($fields)), $iter);
    };
}
function pad(int $size, $padValue = null)
{
    return function (iterable $iter) use($size, $padValue) {
        $i = 0;
        foreach ($iter as $key => $value) {
            (yield $value);
            $i += 1;
        }
        if ($i >= $size) {
            return;
        }
        foreach (\Krak\Fn\range($i, $size - 1) as $index) {
            (yield $padValue);
        }
    };
}
function inArray(array $set)
{
    return function ($item) use($set) {
        return \in_array($item, $set);
    };
}
function arrayMap(callable $fn)
{
    return function (iterable $data) use($fn) {
        return \array_map($fn, \is_array($data) ? $data : \Krak\Fn\toArray($data));
    };
}
function arrayFilter(callable $fn)
{
    return function (iterable $data) use($fn) {
        return \array_filter(\is_array($data) ? $data : \Krak\Fn\toArray($data), $fn);
    };
}
function all(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            if (!$predicate($value)) {
                return false;
            }
        }
        return true;
    };
}
function any(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            if ($predicate($value)) {
                return true;
            }
        }
        return false;
    };
}
function search(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $value) {
            if ($predicate($value)) {
                return $value;
            }
        }
    };
}
function indexOf(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            if ($predicate($value)) {
                return $key;
            }
        }
    };
}
function trans(callable $trans)
{
    return function (callable $fn) use($trans) {
        return function ($data) use($fn, $trans) {
            return $fn($trans($data));
        };
    };
}
function not(callable $fn)
{
    return function (...$args) use($fn) {
        return !$fn(...$args);
    };
}
function isInstance($class)
{
    return function ($item) use($class) {
        return $item instanceof $class;
    };
}
function nullable(callable $fn)
{
    return function ($value) use($fn) {
        return $value === null ? $value : $fn($value);
    };
}
function partition(callable $partition, int $numParts = 2)
{
    return function (iterable $iter) use($partition, $numParts) {
        $parts = \array_fill(0, $numParts, []);
        foreach ($iter as $val) {
            $index = (int) $partition($val);
            $parts[$index][] = $val;
        }
        return $parts;
    };
}
function map(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            (yield $key => $predicate($value));
        }
    };
}
function mapKeys(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            (yield $predicate($key) => $value);
        }
    };
}
function mapKeyValue(callable $fn)
{
    return function (iterable $iter) use($fn) {
        foreach ($iter as $key => $value) {
            [$key, $value] = $fn([$key, $value]);
            (yield $key => $value);
        }
    };
}
function mapOn(array $maps)
{
    return function (iterable $iter) use($maps) {
        foreach ($iter as $key => $value) {
            if (isset($maps[$key])) {
                (yield $key => $maps[$key]($value));
            } else {
                (yield $key => $value);
            }
        }
    };
}
function mapAccum(callable $fn, $acc = null)
{
    return function (iterable $iter) use($fn, $acc) {
        $data = [];
        foreach ($iter as $key => $value) {
            [$acc, $value] = $fn($acc, $value);
            $data[] = $value;
        }
        return [$acc, $data];
    };
}
function withState($initialState = null)
{
    return function (callable $fn) use($initialState) {
        $state = $initialState;
        return function (...$args) use($fn, &$state) {
            [$state, $res] = $fn($state, ...$args);
            return $res;
        };
    };
}
function arrayReindex(callable $fn)
{
    return function (iterable $iter) use($fn) {
        $res = [];
        foreach ($iter as $key => $value) {
            $res[$fn($value)] = $value;
        }
        return $res;
    };
}
function reindex(callable $fn)
{
    return function (iterable $iter) use($fn) {
        foreach ($iter as $key => $value) {
            (yield $fn($value) => $value);
        }
    };
}
function reduce(callable $reduce, $acc = null)
{
    return function (iterable $iter) use($reduce, $acc) {
        foreach ($iter as $key => $value) {
            $acc = $reduce($acc, $value);
        }
        return $acc;
    };
}
function reduceKeyValue(callable $reduce, $acc = null)
{
    return function (iterable $iter) use($reduce, $acc) {
        foreach ($iter as $key => $value) {
            $acc = $reduce($acc, [$key, $value]);
        }
        return $acc;
    };
}
function filter(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            if ($predicate($value)) {
                (yield $key => $value);
            }
        }
    };
}
function filterKeys(callable $predicate)
{
    return function (iterable $iter) use($predicate) {
        foreach ($iter as $key => $value) {
            if ($predicate($key)) {
                (yield $key => $value);
            }
        }
    };
}
function partial(callable $fn)
{
    return function (...$appliedArgs) use($fn) {
        return function (...$args) use($fn, $appliedArgs) {
            list($appliedArgs, $args) = \array_reduce($appliedArgs, function ($acc, $arg) {
                list($appliedArgs, $args) = $acc;
                if ($arg === \Krak\Fn\placeholder()) {
                    $arg = array_shift($args);
                }
                $appliedArgs[] = $arg;
                return [$appliedArgs, $args];
            }, [[], $args]);
            return $fn(...$appliedArgs, ...$args);
        };
    };
}
function differenceWith(callable $cmp)
{
    return function (iterable $a) use($cmp) {
        return function (iterable $b) use($a, $cmp) {
            return \Krak\Fn\filter(function ($aItem) use($cmp, $b) {
                return \Krak\Fn\indexOf(\Krak\Fn\partial($cmp, $aItem), $b) === null;
            }, $a);
        };
    };
}
function sortFromArray(callable $fn)
{
    return function (array $orderedElements) use($fn) {
        return function (iterable $iter) use($orderedElements, $fn) {
            $data = [];
            $flippedElements = \array_flip($orderedElements);
            foreach ($iter as $value) {
                $key = $fn($value);
                if (!\array_key_exists($key, $flippedElements)) {
                    throw new \LogicException('Cannot sort element key ' . $key . ' because it does not exist in the ordered elements.');
                }
                $data[$flippedElements[$key]] = $value;
            }
            ksort($data);
            return $data;
        };
    };
}
function retry($shouldRetry = null)
{
    return function (callable $fn) use($shouldRetry) {
        if (\is_null($shouldRetry)) {
            $shouldRetry = function ($numRetries, \Throwable $t = null) {
                return true;
            };
        }
        if (\is_int($shouldRetry)) {
            $maxTries = $shouldRetry;
            if ($maxTries < 0) {
                throw new \LogicException("maxTries must be greater than or equal to 0");
            }
            $shouldRetry = function ($numRetries, \Throwable $t = null) use($maxTries) {
                return $numRetries <= $maxTries;
            };
        }
        if (!\is_callable($shouldRetry)) {
            throw new \InvalidArgumentException('shouldRetry must be an int or callable');
        }
        $numRetries = 0;
        do {
            try {
                return $fn($numRetries);
            } catch (\Throwable $t) {
            }
            $numRetries += 1;
        } while ($shouldRetry($numRetries, $t));
        throw $t;
    };
}
function stack(callable $last = null, callable $resolve = null)
{
    return function (array $funcs) use($last, $resolve) {
        return function (...$args) use($funcs, $resolve, $last) {
            return \Krak\Fn\reduce(function ($acc, $func) use($resolve) {
                return function (...$args) use($acc, $func, $resolve) {
                    $args[] = $acc;
                    $func = $resolve ? $resolve($func) : $func;
                    return $func(...$args);
                };
            }, $funcs, $last ?: function () {
                throw new \LogicException('No stack handler was able to capture this request');
            });
        };
    };
}
function each(callable $handle)
{
    return function (iterable $iter) use($handle) {
        foreach ($iter as $v) {
            $handle($v);
        }
    };
}
function onEach(callable $handle)
{
    return function (iterable $iter) use($handle) {
        foreach ($iter as $v) {
            $handle($v);
        }
    };
}