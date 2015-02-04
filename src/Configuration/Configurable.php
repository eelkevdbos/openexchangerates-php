<?php namespace EvdB\OpenExchangeRates\Configuration;


trait Configurable
{

    protected $configuration;

    public function setDefaultOptions($options)
    {
        $this->configuration = array_replace_recursive($options, $this->configuration);
    }

    public function setOptions($options, $merge = true)
    {
        $this->configuration = $merge === true ? array_replace_recursive($this->configuration, $options) : $options;
    }

    public function getOptions(array $only = [])
    {
        return count($only) === 0 ? $this->configuration : static::only($this->configuration, $only);
    }

    public function setOption($key, $value)
    {
        $this->configuration[ $key ] = $value;
    }

    public function getOption($key, $defaultValue = null)
    {
        return isset($this->configuration[ $key ]) === true ? $this->configuration[ $key ] : $defaultValue;
    }

    public static function only($array, $includedKeys)
    {
        $filtered = [];
        foreach($array as $key => $value) {
            if (in_array($key, $includedKeys)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

}