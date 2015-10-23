
[Source](http://orazionelson.github.io/v3/ "Permalink to A triple validation contact form in jQuery+PHP for Bootstrap")

# A triple validation contact form in jQuery+PHP for Bootstrap

## How it works.

v3, or vCube, is a script that builds a mail form and secures it by three levels of validation:

* Javascript Validation
* PHP Anti spam tests
* PHP Validation

Morevoer, the script filters the data before sending them.

To do this v3 merges the funcionalities of a javascript validator: [Bootstrap Validator][1], and of a PHP validator: [GUMP][2]. Adding some fuel with its own class: [Vcube][3].

Everything is ruled by a configuration array at the beginning of the vcube class, where the _keys_ are the attribute `name` of the form fields and the values are arrays with:

* The data to build the form
* The validation parameters for Js and PHP
* The sanitization rules for each field

Once the array is configured and the form is built, the data are validated on typing by an ajax call, if this validation passes (or javascript is disabled) the scripts validates the data via PHP.

The validation in PHP is made in two steps:

* Anti spam tests
* Validation

The Anti spam tests to pass are three:

* is not a bot,
* hidden form field value,
* the form is compiled in a time range (2-3600 seconds).

If tests are ok, the script makes validation on the value of any single field using the valudation rules setted in the configuration array.

See the [GUMP page in github][2] to understand better the validation rules.

Moreover v3 adds to GUMP two validation rules.

* blacklist: validate against a list of words
* captcha: to validate the captcha

## Configure

At the beginning of vcube.class.php set the email variables.

    // Mail configuration
    protected static $mail_cnf = "to@mymail.com";
    protected static $site = "My site";
    protected static $thanksPage = '';

Then configure your form fields in the field_map array. Here's a sample of how a field can be configured.

    'name'=&gt;array(
    	//Validation and Filtering options
    	'validation_rules'=&gt;'required|max_len,65|min_len,3|valid_name|blacklist',
    	'filter_rules'=&gt;'trim|sanitize_string',
    	//Field options
    	//If label is omitted the default is the field name with first letter capitalized.
    	//If the field is 'required' the script will show an * after the label
    	'label'=&gt;'Name',
    	//type: input/text' is the default value, it can be omitted
    	'type'=&gt;'input/text',
    	//placeholder, has a default value
    	'placeholder'=&gt;'Your name',
    	//class, has a default value and can be omitted
    	'class'=&gt;'form-control',
    	//Specific attributes for Bootstrap Validator
    	'data-minlength-error'=&gt;'The field needs to be 3 or longer in length.',
    	'data-remote'=&gt;'inc/verify.php',
    	'data-remote-error'=&gt;'The Name field is invalid',
    )

How to write the `validation_rules` and the `filter_rules` is described in [GUMP (the class used to make PHP validation)][2].

Edit the `type` key to define the input field value in html, do in this way: input/text, input/email, input/url, input/number or textarea

Set the `placeholder` and `class` as in simple HTML

Add `data-minlength-error` key if you used `min_len` in `validation_rules`

Add `'data-remote'=&gt;'inc/verify.php'` (and `'data-remote-error'=&gt;'Error message...'`) if you want to use GUMP filter in Javascript Validation, for example: `alpha_numeric`, `blacklist` or `captcha`.

[1]: http://1000hz.github.io/bootstrap-validator/
[2]: https://github.com/Wixel/GUMP
[3]: https://github.com/orazionelson/v3/blob/master/inc/vcube.class.php
  
