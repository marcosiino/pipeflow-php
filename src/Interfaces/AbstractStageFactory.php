<?php
namespace Marcosiino\Pipeflow\Interfaces;

use Marcosiino\Pipeflow\Core\Pipeline\StageDescriptor;
use Marcosiino\Pipeflow\Core\Pipeline\StageConfiguration;

/**
 * Represents an abstract StageFactory
 */
interface AbstractStageFactory
{
    /**
     * Instantiates a pipeline stage with the provided configuration
     *
     * @param StageConfiguration $configuration - A configuration object which contains the input settings for the stage to instantiate
     * @return AbstractPipelineStage
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage;

    /**
     * Returns the pipeline stage's description for the type of PipelineStages that a concrete StageFactory instantiates
     *
     * @return StageDescriptor
     */
    public function getStageDescriptor(): StageDescriptor;
}