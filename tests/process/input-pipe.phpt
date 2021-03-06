--TEST--
Process provides STDIN as writable pipe.
--SKIPIF--
<?php require __DIR__ . '/skipif.inc'; ?>
--FILE--
<?php

namespace Concurrent\Process;

$builder = new ProcessBuilder(PHP_BINARY);
$builder = $builder->withStdinPipe();
$builder = $builder->withStdoutInherited();

$process = $builder->start(__DIR__ . '/assets/stdin-dump.php');

var_dump('START');

$stdin = $process->getStdin();

var_dump($stdin instanceof \Concurrent\Stream\WritableStream);

try {
    $stdin->write("Hello\n");
    
    (new \Concurrent\Timer(100))->awaitTimeout();
    
    $stdin->write('World :)');
} finally {
    $stdin->close();
}

var_dump($process->join());
var_dump('FINISHED');

--EXPECT--
string(5) "START"
bool(true)
string(5) "Hello"
string(8) "World :)"
string(12) "STDIN CLOSED"
int(0)
string(8) "FINISHED"
