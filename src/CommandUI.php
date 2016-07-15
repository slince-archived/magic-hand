<?php
namespace Slince\MagicHand;

use Symfony\Component\Console\Application;

class CommandUI
{
    /**
     * 默认的应用
     * @var string
     */
    const DEFAULT_COMMAND_NAME = 'welcome';

    /**
     * 创建command
     * @return array
     */
    static function createCommands()
    {
        return [
            new Command(),
        ];
    }

    /**
     * command应用主入口
     * @throws \Exception
     */
    static function main()
    {
        $application = new Application();
        $application->addCommands(self::createCommands());
        $application->setDefaultCommand(self::DEFAULT_COMMAND_NAME);
        $application->run();
    }
}