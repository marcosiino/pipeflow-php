<?php

namespace Marcosiino\Pipeflow\Stages\Delay;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;

class DelayStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        // Read milliseconds setting (default 1000ms)
        $ms = intval($this->stageConfiguration->getSettingValue('milliseconds', $context, false) ?? 1000);
        if ($ms > 0) {
            // Convert milliseconds to microseconds
            usleep($ms * 1000);
        }
        // Optionally, write a context parameter to indicate the delay completed
        $context->setParameter('delay_last_ms', $ms);
        return $context;
    }
}
