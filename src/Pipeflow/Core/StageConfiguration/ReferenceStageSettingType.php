<?php

/**
 * Represents the type of reference setting
 */
enum ReferenceStageSettingType: string {
    /*
     * A plain reference to the value of a referenced context parameter.
     */
    case plain = "plain";

    /*
     * An indexed reference to the value of the element at a specific key/index of a referenced context parameter which has an array as its value.
     */
    case keypath = "keypath";

    /*
     * An reference to the value of the last element of a referenced context parameter which has an array as its value.
     */
    case last = "last";
}