<?php

$text = <<<TEXT

question1
sdf
- option1
sdf
+ option2
- option3

question2
+ option1
- option2
+ option3
long options

free-input question
= PHP
TEXT;

class Parser
{
    const CHAR_OPTION = '-';
    const CHAR_RIGHT = '+';
    const CHAR_STRING = '=';

    const TYPE_SINGLE = 'single';
    const TYPE_MULTI = 'multi';
    const TYPE_STRING = 'string';

    /** @var array */
    private $lines;
    private $index;
    private $questions;
    private $options;
    private $title;
    private $specialChars;
    private $answer;
    private $type;

    public function __construct($text)
    {
        $this->lines = explode(PHP_EOL, $text);
        $this->specialChars = array(self::CHAR_OPTION, self::CHAR_RIGHT, self::CHAR_STRING);
    }

    private function flushQuestion()
    {
        if ($this->title) {
            if ($this->type != self::TYPE_STRING && !$this->options) {
                throw new Exception('Question without options');
            } elseif (!$this->answer) {
                throw new Exception('Question without answer');
            }
            if ($this->type == self::TYPE_MULTI && count($this->answer) == 1) {
                $this->type = self::TYPE_SINGLE;
                $this->answer = reset($this->answer);
            }
            $this->questions[] = array(
                'title' => $this->title,
                'type' => $this->type,
                'options' => $this->options,
                'answer' => $this->answer,
            );
        }
        $this->title = '';
        $this->type = self::TYPE_MULTI;
        $this->options = array();
        $this->answer = array();
    }

    public function parse()
    {
        $this->index = 0;
        $this->questions = array();
        $this->flushQuestion();
        while ($line = $this->getLine()) {
            if ($line[0] == self::CHAR_OPTION) {
                $this->options[] = trim($line, '- ');
            } elseif ($line[0] == self::CHAR_RIGHT) {
                $this->options[] = trim($line, '+ ');
                $this->answer[] = count($this->options) - 1;
            } elseif ($line[0] == self::CHAR_STRING) {
                $this->answer = trim($line, '= ');
                $this->type = self::TYPE_STRING;
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
            if ($out && (in_array($line[0], $this->specialChars) || !$line[0])) {
                return trim($out);
            } else {
                $out .= $line . ' ';
            }
        }

        return trim($out);
    }
}

$parser = new Parser($text);
var_dump($parser->parse());


class OldParser
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