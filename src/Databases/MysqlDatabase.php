<?php namespace BigName\BackupManager\Databases;

/**
 * Class MysqlDatabase
 * @package BigName\BackupManager\Databases
 */
class MysqlDatabase implements Database
{
    /**
     * Contains mapping of PHP config options to MySQL command line arguments
     *
     * @var array
     */
    private static $PARAM_MAP = [
        'file' => 'defaults-extra-file',
        'host' => 'host',
        'port' => 'port',
        'user' => 'user',
        'pass' => 'password',
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * @param $type
     * @return bool
     */
    public function handles($type)
    {
        return strtolower($type) == 'mysql';
    }

    /**
     * @param array $config
     * @return null
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param $outputPath
     * @return string
     */
    public function getDumpCommandLine($outputPath)
    {
        return sprintf('mysqldump %s --routines %s > %s',
            $this->getConfigParams(),
            escapeshellarg($this->config['database']),
            escapeshellarg($outputPath)
        );
    }

    /**
     * @param $inputPath
     * @return string
     */
    public function getRestoreCommandLine($inputPath)
    {
        return sprintf('mysql %s %s -e "source %s;"',
            $this->getConfigParams(),
            escapeshellarg($this->config['database']),
            $inputPath
        );
    }

    /**
     * @return string
     */
    protected function getConfigParams()
    {
        $params = [];
        foreach (static::$PARAM_MAP as $config => $param) {
            if (isset($this->config[$config])) {
                $params[]= sprintf('--%s=%s', $param, escapeshellarg($this->config[$config]));
            }
        }

        return implode(' ', $params);
    }
}
