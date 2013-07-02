<?php

$text = <<<TEXT

question1
sdf
- option1
sdf
- option2
- option3

question2
- option1
- option2
- option3
long options
TEXT;

class Parser
{
    public function parse($text)
    {
        $mode = 'q';
        $questions = array();
        $lines = explode(PHP_EOL, $text);
        $question = array();
        foreach ($lines as $i => $line) {
            $line = str_replace(array('  ', "\t"), array(' ', ' '), trim($line));
            if ($mode == 'q') {
                if (!$line) {
                    continue;
                }
                if ($line[0] == '-') {
                    $mode = 'o';
                    $option = trim($line, '- ');
                    continue;
                } else {
                    $question['question'] .= $line . ' ';
                    $question['options'] = array();
                }
            }
            if ($mode == 'o') {
                if (!$line) {
                    $mode = 'q';
                    $question['options'][] = $option;
                    $question['question'] = trim($question['question']);
                    $questions[] = $question;
                    $question = array();
                    continue;
                }
                if ($line[0] == '-') {
                    $question['options'][] = $option;
                    $option = trim($line, '- ');
                } else {
                    $option .= ' ' . $line;
                }
            }
        }
        if ($option) {
            $question['options'][] = $option;
        }
        $question['question'] = trim($question['question']);
        $questions[] = $question;

        return $questions;
    }
}

$parser = new Parser();
var_dump($parser->parse($text));
