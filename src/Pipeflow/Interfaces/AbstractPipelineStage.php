<?php
namespace marcosiino\pipeflow\Interfaces;

use marcosiino\pipeflow\Core\PipelineContext;
use marcosiino\pipeflow\Exceptions\PipelineExecutionException;

/**
 * Represents an abstract PipelineStage
 */
abstract class AbstractPipelineStage
{
    /**
     * Executes the pipeline stage with the context passed as argument, and returns the output context
     * @param PipelineContext $context
     * @return PipelineContext
     * @throws PipelineExecutionException
     */
    abstract public function execute(PipelineContext $context): PipelineContext;
}