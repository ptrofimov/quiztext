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

class Parser2
{
    const CHAR_OPTION = '-';

    /** @var array */
    private $lines;
    private $index;
    private $questions;
    private $options;
    private $title;

    public function __construct($text)
    {
        $this->lines = explode(PHP_EOL, $text);
    }

    private function flushQuestion()
    {
        if ($this->title && !$this->options) {
            throw new Exception('Question without options');
        }
        if ($this->title && $this->options) {
            $this->questions[] = array('title' => $this->title, 'options' => $this->options);
        }
        $this->options = array();
        $this->title = '';
    }

    public function parse()
    {
        $this->index = 0;
        $this->questions = array();
        $this->flushQuestion();
        while ($line = $this->getLine()) {
            if ($line[0] == self::CHAR_OPTION) {
                $this->options[] = trim($line, '- ');
            } else {
                $this->flushQuestion();
                $this->title = $line;
            }
        }
        $this->flushQuestion();

        return $this->questions;
    }

    private function getLine()
    {
        for ($out = ''; $this->index < count($this->lines); $this->index++) {
            $line = str_replace(array('  ', "\t"), array(' ', ' '), trim($this->lines[$this->index]));
            if ($out && ($line[0] == self::CHAR_OPTION || !$line[0])) {
                return trim($out);
            } else {
                $out .= $line . ' ';
            }
        }

        return trim($out);
    }
}

//$parser = new Parser();
//var_dump($parser->parse($text));

$parser = new Parser2($text);
var_dump($parser->parse());
