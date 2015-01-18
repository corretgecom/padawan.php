<?php

namespace Command;

class GenerateCommand extends AbstractCommand{
    public function run(array $arguments = []){
        $time = microtime(true);
        $verbose = $this->isVerbose($arguments);
        $this->addPlugins($arguments);
        $generator = $this->get("IndexGenerator");

        $index = $generator->generateIndex($this->get("Index"));
        printf("Parsed: %d, time: %f\n", count($index->getClassMap()), microtime(true) - $time);
        $index = $this->prepareIndex($index);
        printf("time: %f\n", microtime(true) - $time);
        return;
        $indexWriter = $this->get('IndexWriter');

        $indexWriter->writeIndex($index);
        $indexWriter->writeReport($generator->getInvalidClasses());
    }
    protected function prepareIndex($index){
        $jsonIndex = json_encode($index->toArray());
        $lastJsonError = json_last_error();
        if($lastJsonError != JSON_ERROR_NONE) {
            $this->printJsonError($lastJsonError);
            exit;
        }
        return $jsonIndex;
    }
    protected function printJsonError($errorCode)
    {
        switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
            break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            echo ' - Unknown error';
            break;
        }
        echo "\n";
    }
}
