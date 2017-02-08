<?php

  // is_blank('abcd')
  function is_blank($value='') {
    return !isset($value) || trim($value) == '';
  }

  // has_length('abcd', ['min' => 3, 'max' => 5])
  function has_length($value, $options=array()) {
    $length = strlen($value);

    // longer than max
    if (isset($options['max']) && $length>$options['max']) {
      return false;
    }

    // shorter than min
    elseif (isset($options['min']) && $length<$options['min']) {
      return false;
    }

    // not exactly same as exact length
    elseif (isset($options['exact']) && $length != $options['exact']) {
      return false;
    }

    else {
      return true;
    }
  }

  // has_valid_email_format('test@test.com')
  function has_valid_email_format($value) {
    if (isset($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
      return true;
    }
    else {
      return false;
    }
  }

?>
