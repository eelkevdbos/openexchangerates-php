<?php

class ConfigurableTraitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ConfigurableImplementation
     */
    protected $config;

    public function setUp()
    {
        $this->config = new ConfigurableImplementation();
    }

    public function testGetSetOption()
    {
        $this->assertEquals(null, $this->config->getOption('test'));
        $this->assertEquals('default', $this->config->getOption('test', 'default'));
        $this->config->setOption('get', 'set');
        $this->assertEquals('set', $this->config->getOption('get', 'set'));
    }

    public function testGetSetOptions()
    {
        $this->assertEquals([],$this->config->getOptions());

        $options = ['base' => 'base', 'extend' => 'extend'];
        $this->config->setOptions($options);
        $this->assertEquals($options, $this->config->getOptions());

        //test only filter
        $this->assertEquals(['extend' => 'extend'], $this->config->getOptions(['extend']));

        $this->config->setOptions(['base' => 'merged']);
        $this->assertEquals(['base' => 'merged'], $this->config->getOptions(['base']));

        $this->config->setOptions(['base' => 'only'], false);
        $this->assertEquals(['base' => 'only'], $this->config->getOptions());
    }

    public function testSetDefaultOptions()
    {
        $options = ['base' => 'base', 'extend' => 'extend'];
        $this->config->setOptions($options);
        $this->config->setDefaultOptions(['default' => true]);
        $this->assertEquals(array_merge(['default' => true], $options), $this->config->getOptions());
    }

}

class ConfigurableImplementation
{
    use \EvdB\OpenExchangeRates\Configuration\Configurable;
}