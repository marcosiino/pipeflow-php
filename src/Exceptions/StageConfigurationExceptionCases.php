<?php
namespace Marcosiino\Pipeflow\Exceptions;

enum StageConfigurationExceptionCases: int
{
    case ExpectedFieldNotFound = 1001;
    case StageIdentifierNotSpecified = 1002;
    case InvalidStageTypeIdentifier = 1003;
    case UnableToDecodeJSONConfiguration = 1004;
    case InvalidJSONConfiguration = 1005;
}