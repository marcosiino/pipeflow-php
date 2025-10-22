<?php
namespace marcosiino\pipeflow\Core;

/**
 * Represents the description of a specific pipeline stage type, with its identifier, inputs and outputs descriptions
 */
class StageDescriptor
{
    /**
     * The stage identifier
     * @var string
     */
    private string $identifier;

    /**
     * The stage description
     * @var string
     */
    private string $stageDescription;

    /**
     * The stage setup parameters
     * @var array
     */
    private array $setupParameters;

    /**
     * The parameters that the stage uses from the context
     * @var array
     */
    private array $contextInputs;

    /**
     * The parameters that the stage outputs to the context
     * @var array
     */
    private array $contextOutputs;

    /**
     * Constructor for StageDescriptor.
     * @param string $identifier An identifier for the stage.
     * @param string $stageDescription The description of the stage.
     * @param array $contextInputs The parameters that the stage uses from the context and their description. An associative array of parameterName => description
     * @param array $contextOutputs The parameters that the stage outputs to the context and their description. An associative array of parameterName => description
     */
    public function __construct(string $identifier, string $stageDescription, array $setupParameters, array $contextInputs = array(), array $contextOutputs = array()) {
        $this->identifier = $identifier;
        $this->stageDescription = $stageDescription;
        $this->setSetupParameters($setupParameters);
        $this->setContextInputs($contextInputs);
        $this->setContextOutputs($contextOutputs);
    }

    /**
     * The stage identifier
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }

    /**
     * The stage identifier
     * @return string
     */
    public function getStageDescription(): string {
        return $this->stageDescription;
    }

    /**
     * Returns the stage inputs
     * @return array associative array of string (param name) => string (description)
     */
    public function getContextInputs(): array {
        return $this->contextInputs;
    }

    /**
     * Returns the outputs of the stage
     * @return array associative array of string (param name) => string (description)
     */
    public function getContextOutputs(): array {
        return $this->contextOutputs;
    }


    /**
     * Generates an HTML description of this StageDescriptor
     * @return string
     */
    public function getStageHTMLDescription(): string {
        $out = "";

        $out .= "<div class='stage' style='background-color: #e5e5ff; border: solid 1px; border-color: #c5c5dd; padding: 1em; margin: 1em 0;'>";

        $out .= "<p class='stage-identifier' style='font-size: 1.3em;'><strong>Stage Identifier:<br /></strong><span style='text-decoration: underline;'>$this->identifier</span></p>";
        $out .= "<p></p><strong>Description:<br/></strong><em>$this->stageDescription</em></p>";

        $out .= $this->getParamsArrayHTMLDescription($this->setupParameters, "Setup Parameters:", "stage-descriptor-setup-parameters", "This stage has not any setup parameters");
        $out .= $this->getParamsArrayHTMLDescription($this->contextInputs, "Context Inputs:", "stage-descriptor-context-inputs", "This stage has not any context inputs");
        $out .= $this->getParamsArrayHTMLDescription($this->contextOutputs, "Context Outputs:", "stage-descriptor-context-outputs", "This stage has not any context outputs");

        $out .= "</div>";
        return $out;
    }

    private function validateDictionary(array $dictionary): bool {
        foreach ($dictionary as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                return false;
            }
        }
        return true;
    }

    private function setSetupParameters(array $params): void {
        if ($this->validateDictionary($params)) {
            $this->setupParameters = $params;
        } else {
            throw new InvalidArgumentException("Setup parameters must be an associative array of string => string.");
        }
    }

    private function setContextInputs(array $inputs): void {
        if ($this->validateDictionary($inputs)) {
            $this->contextInputs = $inputs;
        } else {
            throw new InvalidArgumentException("Context Inputs must be an associative array of string => string.");
        }
    }

    private function setContextOutputs(array $outputs): void {
        if ($this->validateDictionary($outputs)) {
            $this->contextOutputs = $outputs;
        } else {
            throw new InvalidArgumentException("Context Outputs must be an associative array of string => string.");
        }
    }

    /// Returns an html description for the parameters array provided, wrapping it in a div with the provided divId and title, with empty text if the array is empty
    private function getParamsArrayHTMLDescription(array $paramsArray, string $title, string $divClass, string $emptyText): string {
        $out = "";

        $out .= "<div class='$divClass parameters'>";
        $out .= "<strong class='title'>$title</strong><br />";
        $out .= "<div style='padding-left: 2em;'>";
        if (count($paramsArray) > 0) {
            $out .= "<ul>";
            foreach ($paramsArray as $key => $value) {
                if ($key != "") {
                    $out .= "<li><strong>" . $key . ":</strong> $value</li>";
                }
                else {
                    $out .= "<li>$value</li>";
                }

            }
            $out .= "</ul>";
        }
        else {
            $out .= "<p><em>$emptyText</em></p>";
        }
        $out .= "</div>";
        $out .= "</div>";

        return $out;
    }
}