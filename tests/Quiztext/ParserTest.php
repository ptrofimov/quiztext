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
