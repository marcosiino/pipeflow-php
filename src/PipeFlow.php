<?php
namespace Marcosiino\Pipeflow;

use Marcosiino\Pipeflow\Core\StageFactory;

class PipeFlow
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {
        StageFactory::registerFactory(new Stages\SetValue\SetValueStageFactory());
        StageFactory::registerFactory(new Stages\Delay\DelayStageFactory());
    }
}