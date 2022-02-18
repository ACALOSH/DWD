<?php

  /////////////////////////////////////
 // index.php for SimpleExample app //
/////////////////////////////////////

// Create f3 object then set various global properties of it
// These are available to the routing code below, but also to any 
// classes defined in autoloaded definitions

$home = '/home/'.get_current_user();
$f3 = require($home.'/AboveWebRoot/fatfree-master/lib/base.php');

// autoload Controller class(es) and anything hidden above web root, e.g. DB stuff
$f3->set('AUTOLOAD','autoload/;../../../AboveWebRoot/autoload/');		

$db = DatabaseConnection::connect();		// defined as autoloaded class in AboveWebRoot/autoload/
$f3->set('DB', $db);

$f3->set('DEBUG',3);		// set maximum debug level
$f3->set('UI','ui/');		// folder for View templates


  /////////////////////////////////////////////
 // Simple Example URL application routings //
/////////////////////////////////////////////

//home page (index.html) -- actually just shows form entry page with a different title
$f3->route('GET /',
  function ($f3) {
    $f3->set('html_title','Simple Example Home');
    $f3->set('content','simpleHome.html');
    echo Template::instance()->render('layout.html');
  }
);

// When using GET, provide a form for the user to upload an image via the file input type
$f3->route('GET /simpleform',
  function($f3) {
    $f3->set('html_title','Simple Input Form');
    $f3->set('content','simpleform.html');
    echo template::instance()->render('layout.html');
  }
);

// When using POST (e.g.  form is submitted), invoke the controller, which will process
// any data then return info we want to display. We display
// the info here via the response.html template
$f3->route('POST /login',
  function($f3) {
	$formdata = array();			// array to pass on the entered data in
	$formdata["name"] = $f3->get('POST.name');			// whatever was called "name" on the form
	$formdata["colour"] = $f3->get('POST.colour');		// whatever was called "colour" on the form

  	$controller = new SimpleController;
    $isValidLogin = $controller->login($formdata);
    if ($isValidLogin){
        $f3->set('formData',$formdata); // set info in F3 variable for access in response template
        $f3->set('html_title','Simple Example Response');
        $f3->set('content','response.html');
        echo template::instance()->render('layout.html');
    }
	else{
        //$controller->putIntoDatabase($formdata);
        $message ="Your credentials weren't recognised, please sign up for an account down below";
        $f3-> set('message', $message);
        $f3->set('content','signup.html');
        echo template::instance()->render('layout.html');
    }
  });

$f3->route('POST /signup',
function($f3) {
    $formdata = array();			// array to pass on the entered data in
    $formdata["name"] = $f3->get('POST.name');			// whatever was called "name" on the form
    $formdata["colour"] = $f3->get('POST.colour');
    $controller = new SimpleController;
    $controller->putIntoDatabase($formdata);

    $f3->set('content','profile.html');
    $f3 ->set('username', $formdata["name"]);
    echo template::instance()->render('layout.html');

});
$f3->route('GET /profile',
    function($f3) {
        $f3->set('content','profile.html');

        echo template::instance()->render('layout.html');
    });
$f3->route('GET /signup',
    function($f3) {

        $f3->set('content','signup.html');
        $message ="Sign Up Below!";
        $f3-> set('message', $message);
        echo template::instance()->render('layout.html');
    });

$f3->route('GET /login',
function($f3) {
    $f3->set('content','login.html');

    echo template::instance()->render('layout.html');
});

$f3->route('GET /cardgame',
    function($f3) {
        $f3->set('content','cardgame.html');
        echo template::instance()->render('layout.html');
    });

$f3->route('GET /streetcat',
    function($f3) {
        $f3->set('content','streetcat.html');
        echo template::instance()->render('layout.html');
    });

$f3->route('GET /gallery',
    function($f3) {
        $f3->set('content','gallery.html');
        echo template::instance()->render('layout.html');
    });

$f3->route('GET /dataView',
  function($f3) {
  	$controller = new SimpleController;
    $alldata = $controller->getData();
    
    $f3->set("dbData", $alldata);
    $f3->set('html_title','Viewing the data');
    $f3->set('content','dataView.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('GET /editView',				// exactly the same as dataView, apart from the template used
  function($f3) {
  	$controller = new SimpleController;
    $alldata = $controller->getData();
    
    $f3->set("dbData", $alldata);
    $f3->set('html_title','Viewing the data');
    $f3->set('content','editView.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /editView',		// this is used when the form is submitted, i.e. method is POST
  function($f3) {
  	$controller = new SimpleController;
    $controller->deleteFromDatabase($f3->get('POST.toDelete'));		// in this case, delete selected data record

	$f3->reroute('/editView');  }		// will show edited data (GET route)
);



$f3->route('GET /simplequiz',
    function($f3) {
        $controller = new SimpleController;
        $alldata = $controller->getQuiz();
        $f3->set("dbData", $alldata);
        $f3->set('html_title','Simple Quiz');
        $f3->set('content','simpleQuiz.html');
        echo template::instance()->render('layout.html');
    }
);


$f3->route('GET /simplequizAdd',
    function($f3) {
        $f3->set('html_title','Add a Quiz Question!');
        $f3->set('content','simplequizAdd.html');
        echo template::instance()->render('layout.html');
    }
);


$f3->route('POST /simplequiz',
    function($f3) {
        $controller = new SimpleController;
        $alldata = $controller->getQuizVals();
        $markedAnswers = array();

        $counter = 0;
        $correctCounter = 0;

        foreach ($alldata as $rowvals) {        // values for each DB record as retrieved
            $counter++;
            if ($rowvals['correct'] == $f3->get('POST.q'.$counter)) {  // 'POST.q1'
                $markedAnswers[$counter] = "correct";
                $correctCounter++;
            }
            else {
                $markedAnswers[$counter] = "incorrect";
            }
        }

        $f3->set('markedAnswers', $markedAnswers);
        $f3->set('correctCounter', $correctCounter);
        $f3->set('totalCounter', $counter);

        $f3->set('html_title','Quiz Response');
        $f3->set('content','quizResponse.html');

        echo template::instance()->render('layout.html');
    }
);



  ////////////////////////
 // Run the F3 engine //
////////////////////////

$f3->run();

?>

