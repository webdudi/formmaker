<?php

/**
 * Class to implement ezjscore methods
 * @author Piotr Szczygieł <piotr.szczygieł@makingwaves.pl>
 */
class formAjaxServerCallFunctions extends ezjscServerFunctions
{
    /**
     * Method validates single form element. Returns true in case of validation was OK, false in case of errors during exexution and string with html
     * in case when validation is not ok.
     * @param type $args
     * @return boolean|string
     */
    public static function validate ($args)
    {
        $http = eZHTTPTool::instance();
        
        if ($http->hasPostVariable('id') && $http->hasPostVariable('value'))
        {
            $id = $http->postVariable('id');
            $attribute = formAttributes::getAttribute($id);
            
            $validation = $attribute->validate($http->postVariable('value'));
            $errors = array();
            if (!empty($validation))
            {
                $errors[$id] = $validation;
                $tpl = eZTemplate::factory();
                $tpl->setVariable('attribute_id', $id);
                $tpl->setVariable('errors', $errors);
                
                // there are errors - returning string with html code do display
                return $tpl->fetch('design:form_error.tpl');                
            }
            
            // validated OK
            return true;
        }
        
        // incorrect validation
        return false;
    }
    
    /**
     * Method adds the field with selected type to a form
     * @return string
     * @throws Exception
     */
    public static function addField ()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasPostVariable( 'input_id' ) )
        {
            throw new Exception( 'Missing required parameter' );
        }
        
        $type_id = $http->postVariable( 'input_id' );
        $type = formTypes::fetchById( $type_id );
        $tpl = eZTemplate::factory();
        
        $tpl->setVariable( 'input', $type );
        $tpl->setVariable( 'input_id', uniqid() );
        $tpl->setVariable( 'data', formAttributes::createEmpty());
        $tpl->setVariable( 'validator_email_id', formValidators::EMAIL_ID );
        
        return $tpl->fetch( 'design:forms/types/' . $type->attribute( 'template' ) );
    }
    
    /**
     * Method adds new option to list
     * @return string
     * @throws Exception
     */
    public static function addAttributeOption()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasPostVariable( 'attribute_id' ) )
        {
            throw new Exception( 'Missing required parameter' );
        }        
        
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'input_id', $http->postVariable( 'attribute_id' ) );
        $tpl->setVariable( 'label', '' );
        $tpl->setVariable( 'option_id', uniqid() );
        
        return $tpl->fetch( 'design:forms/types/elements/option_line.tpl' );
    }
    
    /**
     * Method adds email receiver part 
     * @return string
     * @throws Exception
     */
    public static function addEmailReceiver()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasPostVariable( 'attribute_id' ) )
        {
            throw new Exception( 'Missing required parameter' );
        }
        
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'input_id', $http->postVariable( 'attribute_id' ) );
        $tpl->setVariable( 'enabled', 0 );
        
        return $tpl->fetch( 'design:forms/types/elements/email_receiver.tpl' );
    }
    
    /**
     * Method for checking form connected objects
     * @return boolean
     * @throws Exception
     */
    public static function getFormConnectedObjects()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasPostVariable( 'form_id' ) )
        {
            throw new Exception( 'Missing required parameter' );
        }
        
        $form_object = formDefinitions::getForm( $http->postVariable( 'form_id' ) );
        $connected_objects = $form_object->getConnectedObjects();
        if ( count( $connected_objects ) )
        {
            $error = ezpI18n::tr( 'extension/formmaker/admin', 'Cannot remove this form. There are some objects which uses it:' ) . "\n\n";
            foreach ($connected_objects as $node_id => $node_name)
            {
                $error .= $node_name . ' (' . ezpI18n::tr( 'extension/formmaker/admin', 'node ID: ' ) . $node_id . ")\n";
            }
            throw new Exception(  $error );
        }
        
        return true;
    }
}