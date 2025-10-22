<?php
namespace Marcosiino\Pipeflow\Stages\Delay;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;

class DelayStageFactory implements AbstractStageFactory
{
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        return new DelayStage($configuration);
    }

    public function getStageDescriptor(): StageDescriptor
    {
        $setup = array(
            'milliseconds' => 'Delay duration in milliseconds'
        );
        $inputs = array();
        $outputs = array(
            'delay_last_ms' => 'The milliseconds value used for the last delay'
        );
        return new StageDescriptor('Delay', 'Pauses the pipeline for a given number of milliseconds (useful for testing long-running pipelines).', $setup, $inputs, $outputs);
    }
}
