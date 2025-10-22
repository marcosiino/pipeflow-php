<?php
namespace Marcosiino\Pipeflow\Interfaces;

use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Exceptions\PipelineExecutionException;

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