<?php

/**
 * Author:  Xolmat Ravshanov
 */


namespace zetsoft\service\calls;


use phpseclib\Net\SFTP;
use zetsoft\system\kernels\ZFrame;

class Fwconsole extends ZFrame
{

    #region  Vars
    public $sftp;

    public const ip = [
        '41' => '10.10.3.41',
        '30' => '10.10.3.30',
        '31' => '10.10.3.31',
        '60' => '10.10.3.60',
    ];


    private $credentials = [
        'username' => 'root',
        'password' => 'Formula1'
    ];
    /**
     * @var string[]
     * list of existing commands
     */
    public $command = [
        'zulu' => 'zulu',
        'vqplus' => 'vqplus',
        'util' => 'util',
        'userman' => 'userman',
        'unlock' => 'unlock',
        'trunks' => 'trunks',
        'systemupdate' => 'systemupdate',
        'sysadmin' => 'sysadmin',
        'sound' => 'sound',
        'setting' => 'setting',
        'pm2' => 'pm2',
        'pagingpro' => 'pagingpro',
        'reload' => 'paginreloadgpro',
        'qxact' => 'qxact',
        'modulesystemmanager' => 'modulesystemmanager',
        'notification' => 'notification',
        'mysql' => 'mysql',
        'moduleadmin' => 'moduleadmin',
        'localization' => 'localization',
        'list' => 'list',
        'help' => 'help',
        'firewall' => 'firewall',
        'extip' => 'extip',
        'epm' => 'epm',
        'dbug' => 'dbug',
        'doctrine' => 'doctrine',
        'context' => 'context',
        'bulkimport' => 'bulkimport',
        'certificates' => 'certificates',
        'calendar' => 'calendar',
    ];

    public $ip = self::ip['41'];

    #endregion

    public function init()
    {
        parent::init();
        $this->sftp = new SFTP($this->ip);
        if (!$this->sftp->login($this->credentials['username'], $this->credentials['password'])) {
            exit('bad login');
        }
    }


    public function test()
    {
        vdd($this->run('userman'));
    }

    /**
     * @param $command string command name
     * @return mixed
     */
    public function run($command)
    {
        return $this->sftp->exec("fwconsole $command");
    }

}
