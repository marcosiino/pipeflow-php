<?php
namespace marcosiino\pipeflow\Exceptions;

class StageConfigurationException extends Exception {

    /**
     * Returns a StageConfigurationException with ExpectedFieldNotFound message and code
     * @param string $fieldName The missing field name
     * @return StageConfigurationException
     */
    public static function expectedFieldNotFound(string $fieldName): StageConfigurationException {
        $message = "Invalid json configuration provided: expected field \"$fieldName\" not found";
        $code = StageConfigurationExceptionCases::ExpectedFieldNotFound->value;
        return new StageConfigurationException($message, $code);
    }

    /**
     * Returns a StageConfigurationException with StageIdentifierNotSpecified message and code
     * @return StageConfigurationException
     */
    public static function stageIdentifierNotSpecified(): StageConfigurationException {
        $message = "Stage identifier is not specified in stage configuration. The configuration must include an \"identifier\" field.";
        $code = StageConfigurationExceptionCases::StageIdentifierNotSpecified->value;
        return new StageConfigurationException($message, $code);
    }

    /**
     * Returns a StageConfigurationException with InvalidStageTypeIdentifier message and code
     * @param string $identifier the invalid stage identifier
     * @return StageConfigurationException
     */
    public static function invalidStageTypeIdentifier(string $identifier): StageConfigurationException {
        $message = "Invalid stage type identifier provided: there isn't any factory registered for a stage with the following identifier: $identifier";
        $code = StageConfigurationExceptionCases::InvalidStageTypeIdentifier->value;
        return new StageConfigurationException($message, $code);
    }

    /**
     * Returns a StageConfigurationException with UnableToDecodeJSONConfiguration message and code
     * @return StageConfigurationException
     */
    public static function unableToDecodeJSONConfiguration(): StageConfigurationException {
        $message = "The provided json configuration is not a valid json and cannot be decoding.";
        $code = StageConfigurationExceptionCases::UnableToDecodeJSONConfiguration->value;
        return new StageConfigurationException($message, $code);
    }

    /**
     * Returns a StageConfigurationException with InvalidJSONConfiguration message and code
     * @return StageConfigurationException
     */
    public static function invalidJSONConfiguration(): StageConfigurationException {
        $message =  "Invalid json configuration provided: the json doesn't has the expected structure or fields.";
        $code = StageConfigurationExceptionCases::InvalidJSONConfiguration->value;
        return new StageConfigurationException($message, $code);
    }
}