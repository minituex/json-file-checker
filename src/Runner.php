<?php

namespace JsonFileChecker;

use JsonFileChecker\jsonChecker;

class Runner
{
    public function runJsonChecker() {
        (new jsonChecker()) -> run();
    }
}