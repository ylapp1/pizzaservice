<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib;

/**
 * Loads the PizzaService configuration from a configuration file.
 */
class ConfigLoader
{
    /**
     * The path to the config file
     *
     * @var String $configfilePath
     */
    private $configFilePath;

    /**
     * The decoded content of the config file
     *
     * @var array $configData
     */
    private $configData;


    /**
     * ConfigLoader constructor.
     *
     * @param String $_configFilePath The path to the config file
     */
    public function __construct(String $_configFilePath)
    {
        $this->configFilePath = $_configFilePath;
        $this->parseConfig();
    }

    /**
     * Returns the value of a specific array index from the configData array.
     *
     * @param String $_key The index name
     * @param mixed $_defaultValue The default value which is returned in case of the key not existing in the config data array
     *
     * @return mixed The content of the array index
     */
    public function getConfigValue(String $_key, $_defaultValue = null)
    {
        if (array_key_exists($_key, $this->configData)) return $this->configData[$_key];
        else return $_defaultValue;
    }

    /**
     * Reads and decodes the config file and writes the result to the configData array.
     */
    private function parseConfig()
    {
        if (! file_exists($this->configFilePath)) return;

        $json = file_get_contents($this->configFilePath);
        $this->configData = json_decode($json, true);
    }
}
