<?php namespace EvdB\OpenExchangeRates\Configuration;


trait Configurable
{

    protected $configuration = [];

    /**
     * @return void
     */
    public function setDefaultOptions($options)
    {
        $this->configuration = array_replace_recursive($options, $this->configuration);
    }

    /**
     * @return void
     */
    public function setOptions($options, $merge = true)
    {
        $this->configuration = $merge === true ? array_replace_recursive($this->configuration, $options) : $options;
    }

    /**
     * @return array
     */
    public function getOptions(array $only = [])
    {
        return count($only) === 0 ? $this->configuration : static::only($this->configuration, $only);
    }

    /**
     * @return void
     */
    public function setOption($key, $value)
    {
        $this->configuration[ $key ] = $value;
    }

    /**
     * @return mixed
     */
    public function getOption($key, $defaultValue = null)
    {
        return isset($this->configuration[ $key ]) === true ? $this->configuration[ $key ] : $defaultValue;
    }

    /**
     * @return array
     */
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
