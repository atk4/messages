# ATK4 internal messaging system

##Description

 * Autocomplete field for to and from fields
 * Support of sending messages from different models based on different database tables
 * Messages from site admin
 * Messages to everybody (broadcast messages) or to certain group of users
 * Easy to extend

## Installation

composer.json

    "atk4/messages": "dev-master"


## Support for more "to" and "from" roles

By default add-on supports only few types of users


    protected static $from_types = ['admin'=>'Administrator'];
    protected static $to_types   = ['admin'=>'Administrator','broadcast'=>'Broadcast message'];


You can extend this list by creating your own model extended from <code>atk4/messages/Model_Message</code> and adding
same static properties to your newly created model.

    protected static $from_types = [ 'reader' => 'Reader', 'author' => 'Author' ];
    protected static $to_types   = [ 'reader' => 'Reader', 'author' => 'Author' ];

Also you have to let add-on know that it should use your custom model

    atk4\messages\Config::getInstance()->setMessageModelClassName('Model_MyMessage')

The original set of roles and your own will be mixed. Use <code>{{your_model_name}}::getFromTypes()</code>
and <code>{{your_model_name}}::getToTypes()</code> to get all available roles.

<b>Important!!! There are two separate lists of roles - "to" and "from" </b>


## Different models for different roles

After you added custom roles you must let add-on know what model to use for each role. Use atk4/messages/Config singleton
to make your add-on settings available globally.

    atk4\messages\Config::getInstance()
        ->setTypeModelClassName( 'reader', 'Model_Reader' )
        ->setTypeModelClassName( 'author', 'Model_Author' )
    ;



## Admin

Create page for message management


    class page_messages extends Page {
        function init(){
            parent::init();
            $this->title = 'Messages';

            $m = $this->add('atk4/messages/Model_Message');
            // $m = $this->add('Model_MyMessage'); // <~~~~~~~~~~~~ for custom model
            $m->setOrder('created_dts',true);

            $crud = $this->add('atk4/messages/CRUD_Messages');

            $crud->setModel($m);

            // optional paginator
            if ($crud->grid) {
                $crud->grid->addPaginator();
            }

        }
    }


Done! Now you have fully functional admin panel for message management.

## Admin view CSS

You can change some css class using atk4/messages/Config singleton.
List of available ... provided


    'message-from-class'  => 'atk-label atk-effect-warning atk-block',
    'message-to-class'    => 'atk-label atk-effect-warning atk-block',
    'message-text-class'  => 'atk-swatch-gray atk-box',


## Frontend

It is your responsibility to create user UI.