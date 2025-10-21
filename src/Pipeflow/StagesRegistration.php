<?php
namespace marcosiino\pipeflow;

use marcosiino\pipeflow\Core\StageFactory;
use marcosiino\pipeflow\Stages\SetValue\SetValueStageFactory;

class PipeFlow
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {
        StageFactory::registerFactory(new SetValueStageFactory());
    }
}