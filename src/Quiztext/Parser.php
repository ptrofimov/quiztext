<?php
namespace Quiztext;

class Parser
{
    const CHAR_OPTION = '-';
    const CHAR_CORRECT = '+';
    const CHAR_STRING = '=';
    const CHAR_MULTI = '*';

    const TYPE_SINGLE = 'single';
    const TYPE_MULTI = 'multi';
    const TYPE_STRING = 'string';

    /** @var array */
    private $lines;
    private $index;
    /** @var string|null */
    private $title;
    private $questions;
    private $options;
    private $text;
    private $specialChars;
    private $answer;
    private $type;

    public function __construct($text)
    {
        $this->lines = explode(PHP_EOL, $text);
        $this->specialChars = array(self::CHAR_OPTION, self::CHAR_CORRECT, self::CHAR_STRING, self::CHAR_MULTI);
    }

    private function flushQuestion()
    {
        if ($this->text) {
            if ($this->type != self::TYPE_STRING && !$this->options) {
                if (!$this->questions) {
                    $this->title = $this->text;
                    $this->resetQuestion();
                    return;
                }
                throw new \Exception('Question without options');
            } elseif (!$this->answer) {
                throw new \Exception('Question without answer');
            }
            if ($this->type == self::TYPE_SINGLE && count($this->answer) == 1) {
                $this->answer = reset($this->answer);
            }
            if ($this->type == self::TYPE_SINGLE && count($this->answer) > 1) {
                $this->type = self::TYPE_MULTI;
            }
            $this->questions[] = array(
                'title' => $this->text,
                'type' => $this->type,
                'options' => $this->options,
                'answer' => $this->answer,
            );
        }
        $this->resetQuestion();
    }

    private function resetQuestion()
    {
        $this->text = '';
        $this->type = self::TYPE_SINGLE;
        $this->options = array();
        $this->answer = array();
    }

    public function parse()
    {
        $this->index = 0;
        $this->title = null;
        $this->questions = array();
        $this->resetQuestion();
        while ($line = $this->getLine()) {
            if ($line[0] == self::CHAR_OPTION) {
                $this->options[] = trim($line, self::CHAR_OPTION . ' ');
            } elseif ($line[0] == self::CHAR_CORRECT) {
                $this->options[] = trim($line, self::CHAR_CORRECT . ' ');
                $this->answer[] = count($this->options) - 1;
            } elseif ($line[0] == self::CHAR_STRING) {
                $this->answer = trim($line, self::CHAR_STRING . ' ');
                $this->type = self::TYPE_STRING;
            } elseif ($line[0] == self::CHAR_MULTI) {
                $this->options[] = trim($line, self::CHAR_MULTI . ' ');
                $this->type = self::TYPE_MULTI;
            } else {
                $this->flushQuestion();
                $this->text = $line;
            }
        }
        $this->flushQuestion();

        return $this->questions;
    }

    /** @return string|null */
    public function getTitle()
    {
        return $this->title;
    }

    private function getLine()
    {
        for ($out = ''; $this->index < count($this->lines); $this->index++) {
            $line = str_replace(array('  ', "\t"), array(' ', ' '), trim($this->lines[$this->index]));
            $line = str_replace(
                array('[]', '[ ]', '[x]'),
                array(self::CHAR_MULTI, self::CHAR_MULTI, self::CHAR_CORRECT),
                $line
            );
            $line = str_replace(
                array('()', '( )', '(x)'),
                array(self::CHAR_OPTION, self::CHAR_OPTION, self::CHAR_CORRECT),
                $line
            );
            if ($out && (!isset($line[0]) || in_array($line[0], $this->specialChars))) {
                return trim($out);
            } else {
                $out .= $line . ' ';
            }
        }

        return trim($out);
    }
}
