<?php
namespace Marcosiino\Pipeflow;

use Marcosiino\Pipeflow\Core\StageFactory;

class PipeFlow
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {
        StageFactory::registerFactory(new Stages\ArrayCount\ArrayCountStageFactory());
        StageFactory::registerFactory(new Stages\ArrayPath\ArrayPathStageFactory());
        StageFactory::registerFactory(new Stages\Delay\DelayStageFactory());
        StageFactory::registerFactory(new Stages\ExplodeString\ExplodeStringStageFactory());
        StageFactory::registerFactory(new Stages\ForEach\ForEachStageFactory());
        StageFactory::registerFactory(new Stages\JSONEncode\JSONEncodeStageFactory());
        StageFactory::registerFactory(new Stages\JSONDecode\JSONDecodeStageFactory());
        StageFactory::registerFactory(new Stages\If\IfStageFactory());
        StageFactory::registerFactory(new Stages\RandomArrayItem\RandomArrayItemStageFactory());
        StageFactory::registerFactory(new Stages\RandomValue\RandomValueStageFactory());
        StageFactory::registerFactory(new Stages\SetValue\SetValueStageFactory());
        StageFactory::registerFactory(new Stages\SumOperation\SumOperationStageFactory());

    }
}