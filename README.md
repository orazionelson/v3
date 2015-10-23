# v3



## How it works.

v3, or vCube, is a script that builds a mail form and secures it by three levels of validation:

*   Javascript Validation
*   PHP Anti spam tests
*   PHP Validation

Morevoer, the script filters the data before sending them.

To do this v3 merges the funcionalities of a javascript validator: [Bootstrap Validator](http://1000hz.github.io/bootstrap-validator/), and of a PHP validator: [GUMP](https://github.com/Wixel/GUMP). Adding some fuel with its own class: [Vcube](https://github.com/orazionelson/v3/blob/master/inc/vcube.class.php).

Everything is ruled by a configuration array at the beginning of the <mark>vcube</mark> class, where the _keys_ are the attribute `name` of the form fields and the values are arrays with:

*   The data to build the form
*   The validation parameters for Js and PHP
*   The sanitization rules for each field

Once the array is configured and the form is built, the data are validated <mark>on typing</mark> by an ajax call, if this validation passes (or javascript is disabled) the scripts validates the data via PHP.

The validation in PHP is made in two steps:

*   Anti spam tests
*   Validation

The Anti spam tests to pass are three:

*   is not a bot,
*   hidden form field value,
*   the form is compiled in a time range (2-3600 seconds).

If tests are ok, the script makes validation on the value of any single field using the valudation rules setted in the configuration array.

See the [GUMP page in github](https://github.com/Wixel/GUMP) to understand better the validation rules.

Moreover <mark>v3</mark> adds to GUMP two validation rules.

*   <mark>blacklist</mark>: validate against a list of words
*   <mark>captcha</mark>: to validate the captcha



* * *



## Install

*   First you need to have Bootstrap installed
*   Copy all the content of <mark>inc/</mark> direcory in your <mark>libraries</mark> or <mark>include folder</mark>.
*   Copy <mark>js/vendor/validation.js</mark> in your js folder.



* * *



## Configure

At the beginning of <mark>vcube.class.php</mark> set the email variables.

    // Mail configuration
    protected static $mail_cnf = "to@mymail.com";
    protected static $site = "My site";
    protected static $thanksPage = '';

Then configure your form fields in the <mark>field_map</mark> array. Here's a sample of how a field can be configured.

    'name'=>array(
    	//Validation and Filtering options
    	'validation_rules'=>'required|max_len,65|min_len,3|valid_name|blacklist',
    	'filter_rules'=>'trim|sanitize_string',
    	//Field options
    	//If label is omitted the default is the field name with first letter capitalized.
    	//If the field is 'required' the script will show an * after the label
    	'label'=>'Name',
    	//type: input/text' is the default value, it can be omitted 
    	'type'=>'input/text', 
    	//placeholder, has a default value
    	'placeholder'=>'Your name',
    	//class, has a default value and can be omitted
    	'class'=>'form-control',
    	//Specific attributes for Bootstrap Validator
    	'data-minlength-error'=>'The field needs to be 3 or longer in length.',  
    	'data-remote'=>'inc/verify.php',
    	'data-remote-error'=>'The Name field is invalid',
    )

How to write the `validation_rules` and the `filter_rules` is described in [GUMP (the class used to make PHP validation)](https://github.com/Wixel/GUMP).

Edit the `type` key to define the input field value in html, do in this way: input/text, input/email, input/url, input/number or textarea

Set the `placeholder` and `class` as in simple HTML

Add `data-minlength-error` key if you used `min_len` in `validation_rules`

Add `'data-remote'=>'inc/verify.php'` (and `'data-remote-error'=>'Error message...'`) if you want to use GUMP filter in Javascript Validation, for example: `alpha_numeric`, `blacklist` or `captcha`.



* * *



## How to create a page with form

The [contact form template](https://github.com/orazionelson/v3/blob/master/index.php) in the package is your starting point to build the page.  
Basically:

1.  Include and run v3 at the beginning of your page.

        	require "inc/vcube.class.php";
        	$vcube = new Vcube();
        	$messages=$vcube->vcube_run();

2.  Build the form inside the page.

        	$vcube->build_form($messages); 					

3.  Do not forget to include the <mark>Bootstrap validator js</mark> in your script section, usually after Bootstrap.

        	<script src="js/vendor/validator.js"></script>					



* * *



## Dependencies

*   [Bootstrap](http://getbootstrap.com/)
*   [Bootstrap Validator](http://1000hz.github.io/bootstrap-validator/)
*   [GUMP PHP Validator](https://github.com/Wixel/GUMP)
*   [A PHP script for captcha](https://github.com/claviska/simple-php-captcha)



* * *



## Sources for code and inspiration

*   [A PHP script for mail form validation](https://github.com/jemjabella/PHP-Mail-Form/blob/master/mail_form_v2.txt)
*   [A PHP class for mail form testing](https://github.com/mccarthy/phpFormProtect/tree/master/phpfp)
*   [A PHP script for captcha](http://www.abeautifulsite.net/a-simple-php-captcha-script/ and https://github.com/claviska/simple-php-captcha)
*   [A post about hidden form field](http://www.sitepoint.com/easy-spam-prevention-using-hidden-form-fields/)
*   [A post about spam prevenction](http://nfriedly.com/techblog/2009/11/how-to-build-a-spam-free-contact-forms-without-captchas/)

