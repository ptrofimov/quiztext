<?php
namespace Quiztext;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testConstants()
    {
        $parserRerflection = new \ReflectionClass('Quiztext\Parser');
        $this->assertEquals(
            array(
                'CHAR_OPTION' => '-',
                'CHAR_RIGHT' => '+',
                'CHAR_STRING' => '=',
                'CHAR_MULTI' => '*',
                'TYPE_SINGLE' => 'single',
                'TYPE_MULTI' => 'multi',
                'TYPE_STRING' => 'string',
            ),
            $parserRerflection->getConstants()
        );
    }

    public function texts()
    {
        return array(
            array(
                'lines' => array(),
                'questions' => array(),
            ),
            array(
                'lines' => array(''),
                'questions' => array(),
            ),
            array(
                'lines' => array(
                    'question',
                    '- option1',
                    '+ option2',
                ),
                'questions' => array(
                    array(
                        'title' => 'question',
                        'type' => Parser::TYPE_SINGLE,
                        'options' => array(
                            'option1',
                            'option2',
                        ),
                        'answer' => 1,
                    ),
                ),
            ),
            'single alt syntax' => array(
                'lines' => array(
                    'question',
                    '() option1',
                    '( ) option2',
                    '(x) option3',
                ),
                'questions' => array(
                    array(
                        'title' => 'question',
                        'type' => Parser::TYPE_SINGLE,
                        'options' => array(
                            'option1',
                            'option2',
                            'option3',
                        ),
                        'answer' => 2,
                    ),
                ),
            ),
            array(
                'lines' => array(
                    '',
                    'question',
                    'next',
                    '- option1',
                    'next',
                    '+ option2',
                    'next',
                    '',
                ),
                'questions' => array(
                    array(
                        'title' => 'question next',
                        'type' => Parser::TYPE_SINGLE,
                        'options' => array(
                            'option1 next',
                            'option2 next',
                        ),
                        'answer' => 1,
                    ),
                ),
            ),
            'many questions' => array(
                'lines' => array(
                    'question1',
                    '- option11',
                    '+ option12',
                    '',
                    'question2',
                    '- option21',
                    '+ option22',
                ),
                'questions' => array(
                    array(
                        'title' => 'question1',
                        'type' => Parser::TYPE_SINGLE,
                        'options' => array(
                            'option11',
                            'option12',
                        ),
                        'answer' => 1,
                    ),
                    array(
                        'title' => 'question2',
                        'type' => Parser::TYPE_SINGLE,
                        'options' => array(
                            'option21',
                            'option22',
                        ),
                        'answer' => 1,
                    ),
                ),
            ),
            array(
                'lines' => array(
                    'multi',
                    '- option1',
                    '+ option2',
                    '+ option3',
                ),
                'questions' => array(
                    array(
                        'title' => 'multi',
                        'type' => Parser::TYPE_MULTI,
                        'options' => array(
                            'option1',
                            'option2',
                            'option3',
                        ),
                        'answer' => array(1, 2),
                    ),
                ),
            ),
            array(
                'lines' => array(
                    'multi with *',
                    '* option1',
                    '+ option2',
                ),
                'questions' => array(
                    array(
                        'title' => 'multi with *',
                        'type' => Parser::TYPE_MULTI,
                        'options' => array(
                            'option1',
                            'option2',
                        ),
                        'answer' => array(1),
                    ),
                ),
            ),
            'multi alt syntax' => array(
                'lines' => array(
                    'multi alt syntax',
                    '[] option1',
                    '[ ] option2',
                    '[x] option3',
                ),
                'questions' => array(
                    array(
                        'title' => 'multi alt syntax',
                        'type' => Parser::TYPE_MULTI,
                        'options' => array(
                            'option1',
                            'option2',
                            'option3',
                        ),
                        'answer' => array(2),
                    ),
                ),
            ),
            array(
                'lines' => array(
                    'string type',
                    '= answer',
                ),
                'questions' => array(
                    array(
                        'title' => 'string type',
                        'type' => Parser::TYPE_STRING,
                        'options' => array(),
                        'answer' => 'answer',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider texts
     */
    public function testParse(array $lines, array $questions)
    {
        $parser = new Parser(implode(PHP_EOL, $lines));
        $this->assertEquals($questions, $parser->parse());
    }
}
