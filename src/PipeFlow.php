<?php
namespace Marcosiino\Pipeflow;

use Marcosiino\Pipeflow\Core\StageFactory;
use Marcosiino\Pipeflow\Stages\SetValue\SetValueStageFactory;

class PipeFlow
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {
        StageFactory::registerFactory(new SetValueStageFactory());
    }
}