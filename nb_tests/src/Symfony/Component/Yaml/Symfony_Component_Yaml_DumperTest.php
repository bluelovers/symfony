<?php

require_once dirname(__FILE__) . '/../../../../../src/Symfony/Component/Yaml/Autoloader.php';

Symfony_Component_Yaml_Autoloader::register();

/**
 * Test class for Symfony_Component_Yaml_Dumper.
 * Generated by PHPUnit on 2012-02-24 at 07:03:43.
 */
class Symfony_Component_Yaml_DumperTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Symfony_Component_Yaml_Parser
	 */
	protected $parser;
	/**
	 * @var Symfony_Component_Yaml_Dumper
	 */
    protected $dumper;
    protected $path;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->parser = new Symfony_Component_Yaml_Parser();
        $this->dumper = new Symfony_Component_Yaml_Dumper();
        $this->path = __DIR__.'/Fixtures';
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
    {
        $this->parser = null;
        $this->dumper = null;
        $this->path = null;
    }

    public function testSpecifications()
    {
        $files = $this->parser->parse(file_get_contents($this->path.'/index.yml'));
        foreach ($files as $file) {
            $yamls = file_get_contents($this->path.'/'.$file.'.yml');

            // split YAMLs documents
            foreach (preg_split('/^---( %YAML\:1\.0)?/m', $yamls) as $yaml) {
                if (!$yaml) {
                    continue;
                }

                $test = $this->parser->parse($yaml);
                if (isset($test['dump_skip']) && $test['dump_skip']) {
                    continue;
                } elseif (isset($test['todo']) && $test['todo']) {
                    // TODO
                } else {
                    $expected = eval('return '.trim($test['php']).';');

                    $this->assertEquals($expected, $this->parser->parse($this->dumper->dump($expected, 10)), $test['test']);
                }
            }
        }
    }

    public function testInlineLevel()
    {
        // inline level
        $array = array(
            '' => 'bar',
            'foo' => '#bar',
            'foo\'bar' => array(),
            'bar' => array(1, 'foo'),
            'foobar' => array(
                'foo' => 'bar',
                'bar' => array(1, 'foo'),
                'foobar' => array(
                    'foo' => 'bar',
                    'bar' => array(1, 'foo'),
                ),
            ),
        );

        $expected = <<<EOF
{ '': bar, foo: '#bar', 'foo''bar': {  }, bar: [1, foo], foobar: { foo: bar, bar: [1, foo], foobar: { foo: bar, bar: [1, foo] } } }
EOF;
$this->assertEquals($expected, $this->dumper->dump($array, -10), '->dump() takes an inline level argument');
$this->assertEquals($expected, $this->dumper->dump($array, 0), '->dump() takes an inline level argument');

$expected = <<<EOF
'': bar
foo: '#bar'
'foo''bar': {  }
bar: [1, foo]
foobar: { foo: bar, bar: [1, foo], foobar: { foo: bar, bar: [1, foo] } }

EOF;
        $this->assertEquals($expected, $this->dumper->dump($array, 1), '->dump() takes an inline level argument');

        $expected = <<<EOF
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
    - 1
    - foo
foobar:
    foo: bar
    bar: [1, foo]
    foobar: { foo: bar, bar: [1, foo] }

EOF;
        $this->assertEquals($expected, $this->dumper->dump($array, 2), '->dump() takes an inline level argument');

        $expected = <<<EOF
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
    - 1
    - foo
foobar:
    foo: bar
    bar:
        - 1
        - foo
    foobar:
        foo: bar
        bar: [1, foo]

EOF;
        $this->assertEquals($expected, $this->dumper->dump($array, 3), '->dump() takes an inline level argument');

        $expected = <<<EOF
'': bar
foo: '#bar'
'foo''bar': {  }
bar:
    - 1
    - foo
foobar:
    foo: bar
    bar:
        - 1
        - foo
    foobar:
        foo: bar
        bar:
            - 1
            - foo

EOF;
        $this->assertEquals($expected, $this->dumper->dump($array, 4), '->dump() takes an inline level argument');
        $this->assertEquals($expected, $this->dumper->dump($array, 10), '->dump() takes an inline level argument');
    }

    public function testObjectsSupport()
    {
        $a = array('foo' => new Symfony_Component_Yaml_DumperTest_A(), 'bar' => 1);

        $this->assertEquals('{ foo: !!php/object:O:35:"Symfony_Component_Yaml_DumperTest_A":1:{s:1:"a";s:3:"foo";}, bar: 1 }', $this->dumper->dump($a), '->dump() is able to dump objects');
    }
}

class Symfony_Component_Yaml_DumperTest_A
{
    public $a = 'foo';
}

?>
