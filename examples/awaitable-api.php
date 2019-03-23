<?php

namespace Concurrent;

$result = TaskScheduler::run(function () {
    $t = Task::async(function (): int {
        return max(123, Task::await(Deferred::value()));
    });
    
    printf("LAUNCHED TASK: [%s]\nin %s:%u\n\n", $t->status, $t->file, $t->line);
    
    print_r($t);
    
    try {
        Task::await(Deferred::error(new \Error('Fail!')));
    } catch (\Throwable $e) {
        var_dump($e->getMessage());
    }
    
    var_dump(2 * Task::await($t));
    
    return 777;
}, function (array $tasks) {
    print_r($tasks);
});

$timer = new Timer(500);
$timer->awaitTimeout();
var_dump($result);
