<?php
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PlaceholderProcessor.php";
class StageConfiguration
{
    private array $configuration = array();

    public function addSetting(StageSetting|ReferenceStageSetting $setting): void
    {
        $this->configuration[$setting->getName()] = $setting;
    }

    /**
     * @throws PipelineExecutionException
     */
    public function getSettingValue(string $name, PipelineContext $context, bool $required = false, mixed $defaultValue = null) {
        if(!array_key_exists($name, $this->configuration)) {
            if($required === true) {
                throw new PipelineExecutionException("Stage configuration setting `$name` is required");
            }
            else {
                return $defaultValue;
            }
        }

        $setting = $this->configuration[$name];
        if($setting instanceof StageSetting) {
            $value = $setting->getValue();
            return $this->processPlaceholders($value, $context);
        }
        else if($setting instanceof ReferenceStageSetting) {
            $value = $setting->getValue($context);
            if(is_null($value)) {
                if($required === true) {
                    throw new PipelineExecutionException("Stage configuration setting `$name` is required, but the referenced context param named '" . $setting->getReferencedContextParam() . "' does not exist");
                }
                else {
                    return $defaultValue;
                }
            }
            else {
                return $this->processPlaceholders($value, $context);
            }
        }

        return $defaultValue;
    }

    /**
     *
     * Replaces the placeholders (if any) with context parameters, but only if the passed value is a string, or an array (in this case only the array items which are strings are processed for placeholders)
     *
     * @param mixed $value
     * @return mixed
     */
    private function processPlaceholders(mixed $value, $context)
    {
        if (is_string($value)) { //If string, replace the placeholders (if any) with referenced context parameters
            $processor = new PlaceholderProcessor($context);
            return $processor->process($value);
        }
        else { //For other types the value is not processed
            return $value;
        }
    }
}
