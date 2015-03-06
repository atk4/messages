/**
 * Created by vadym on 06/03/15.
 */
/**
 * Created by vadym on 03/11/14.
 */

(function($){

    $.atk4_messages=function(){
        return $.atk4_messages;
    }

    $.fn.extend({atk4_messages:function(){
        var u=new $.atk4_messages;
        u.jquery=this;
        return u;
    }});


    $.atk4_messages._import=function(name,fn){
        $.atk4_messages[name]=function(){
            var ret=fn.apply($.atk4_messages,arguments);
            return ret?ret:$.atk4_messages;
        }
    }

    $.each({

        whois: function() {
            alert('atk4_messages');
        },
        changeAutocompleteURL: function( dd_field, ac_field, ac_other_field, type_field_name ) {

            // get current autocomplete URL
            var current_autoload_source = $( "#" + ac_other_field ).autocomplete( "option", "source" );

            var new_autoload_source = "";

            // replace if already added
            if ( current_autoload_source.indexOf("&" + type_field_name + "=") >= 0 ) {
                // replace
                var patt = new RegExp( "&" + type_field_name + "=[_a-zA-Z0-9]+",'g');
                var new_autoload_source = current_autoload_source.replace(patt, "&" + type_field_name + "=" + $( "#" + dd_field ).find(":selected").attr("value"));

                //console.log( '+++------------------------------>' + type_field_name );
                //console.log( '+++------------------------------>' + patt.toString() );
            }

            // add to the end of the URL
            else {
                new_autoload_source = current_autoload_source + "&" + type_field_name + "=" + $( "#" + dd_field ).find(":selected").attr("value");
            }


            //console.log('*****------------------------------>' + current_autoload_source);
            //console.log('*****------------------------------>' + new_autoload_source);

            // set new autocomplete URL
            $( "#" + ac_other_field ).autocomplete( "option", "source", new_autoload_source );

            // clear previous values in autoload field.
            $( "#" + ac_field).val('');
            $( "#" + ac_other_field).val('');


        }



    },$.atk4_messages._import);

})(jQuery);