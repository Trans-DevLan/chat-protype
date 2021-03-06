<?php 
session_start();
include('header.php');

?>

<?php
    include('config.php');
     $chat_id = $_GET['chat'];
if (isset($_POST['chat_msg'])) {
	$sender_userid = $_POST['sender_userid'];
  $reciever_userid = $_POST['reciever_userid'];
	$message = $_POST['message'];
	$status = 1;

    $stmt = $conn->prepare("INSERT INTO chat (chat_receiver,chat_sender,chat_content,chat_status) VALUES (?, ?, ?,?)");
    $stmt->bind_param("sssi", $reciever_userid ,$sender_userid,$message,$status);
    $stmt->execute();
    if ($stmt == TRUE) {
	    echo "<audio src = 'sound/134332-facebook-chat-sound.mp3' hidden = 'true' autoplay = 'true' /></audio>";
      header("refresh: 4");
      
     
   
    } else {
       echo"Message not sent";
    }
	
}
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from robust.bootlab.io/demo-chat.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 05 Aug 2019 16:50:12 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>WCF</title>

  <link href="dist/css/robust.css" rel="stylesheet">
</head>
<body>

  <nav class="navbar navbar-lg navbar-expand-lg navbar-dark bg-info">
    <div class="container">
      <a class="navbar-brand" href="index-2.html">WCF</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index-2.html">Overview</a>
          </li>
          <li class="nav-item dropdown active">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Pages
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="pages-landing.html">Landing</a>
              <a class="dropdown-item" href="pages-dashboard.html">Dashboard</a>
              <a class="dropdown-item" href="pages-general.html">General</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Components
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="components-bootstrap.html">Bootstrap</a>
              <a class="dropdown-item" href="components-robust.html">Robust</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Docs
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="getting-started.html">Introduction</a>
              <a class="dropdown-item" href="getting-started.html#quick-start">Quick start</a>
              <a class="dropdown-item" href="getting-started.html#build-tools">Build tools</a>
              <a class="dropdown-item" href="getting-started.html#contents">Contents</a>
              <a class="dropdown-item" href="getting-started.html#changelog">Changelog</a>
            </div>
          </li>
        </ul>
        <div class="dropdown">
          <button class="btn btn-outline-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            My account
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Profile</a>
            <a class="dropdown-item" href="#">Analytics</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Settings & Privacy</a>
            <a class="dropdown-item" href="#">Help</a>
            <a class="dropdown-item" href="#">Sign out</a>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id']) { ?> 
  <main class="main" role="main">
    <div class="py-5 bg-light">

      <div class="container">
        <div class="row card flex-row">
          <div class="col-auto px-0">
            <form class="card-header d-none d-lg-block">
              <input class="form-control my-1" type="search" placeholder="Search or start a new chat" />
            </form>
            <div class="list-group list-group-flush">
            <?php include('sidelist.php'); ?>
            </div>

          <div class="col px-0">
          <?php
             // $chat_id = $_GET['chat'];
              if (isset($_GET['chat'])) {
                $chat_id = $_GET['chat'];
                $user_id = $_SESSION['user_id'];
                $chat_status ='0';
               $query = "UPDATE chat  SET chat_status =? WHERE chat_sender = ? AND chat_receiver = ?";
                    $stmt = $conn->prepare($query);
                    $rc = $stmt->bind_param('iss',  $chat_status, $chat_id, $user_id);
                    $stmt->execute();
                    
                }
                
                $ret = "SELECT * FROM  user WHERE user_id = ? LIMIT 1";
                $stmt = $conn->prepare($ret);
                $stmt->bind_param('s', $chat_id);
                $stmt->execute(); //ok
                $res = $stmt->get_result();
                while ($row = $res->fetch_object()) {
                ?>
				
          <div class="card-header d-flex justify-content-between align-items-center">
              <div class="media align-items-center">
                <img alt="Image" src="img/<?php echo $row->user_image ?>" class="img-fluid rounded-circle m-0" width="46" height="46" />
                <div class="media-body ml-2">
                  <h6 class="mb-0 d-block">
                  <?php echo $row->user_fname ?> <?php echo $row->user_mname ?> <?php echo $row->user_lname ?>
                  </h6>
                  <span class="text-muted text-small">Online</span>
                </div>
              </div>
              
            </div>
            
             <?php
             
             
                $ret = "SELECT * FROM  chat WHERE (chat_sender = ?  AND chat_receiver=?) OR (chat_receiver = ? AND  chat_sender=? ) ORDER BY chat_timestamp ASC";
                $stmt = $conn->prepare($ret);
                $stmt->bind_param('ssss', $_SESSION['user_id'], $chat_id, $_SESSION['user_id'], $chat_id);
                $stmt->execute(); //ok
                $res = $stmt->get_result();
                while ($chat = $res->fetch_object()) {
                  //get timestamp
                  $time = date("D M Y g:i", strtotime($chat->chat_timestamp));

                  if ($chat->chat_sender == $_SESSION['user_id']) {
                   echo"<div class='row justify-content-end text-right my-2'>
                   <div class='col-auto'>
                     <div class='card bg-info text-white'>
                       <div class='card-body p-2'>
                         <p class='mb-0'>
               $chat->chat_content
                         </p>
                         <div>
                           <i class='icon-check text-small'></i>
                           <small>$time</small>
                         </div>
                       </div>
                     </div>
                   </div>
           </div>";
                  } else {
                 echo "
                 <div class='row justify-content-start my-2'>
                <div class='col-auto'>
                  <div class='card bg-light'>
                    <div class='card-body p-2'>
                      <p class='mb-0'>
                      $chat->chat_content
                      </p>
                      <div>
                        <small>$time</small>
                      </div>
                    </div>
                  </div>
                </div>
			  </div>";  
                  }}
              ?>
            
            <div class="card-footer bg-light">
      
            <form method="POST"  class="d-flex align-items-center">
                <div class="input-group">
               <input class="form-control" type="hidden" value="<?php echo $_SESSION['user_id']; ?>" name="sender_userid" />
                <input class="form-control" type="hidden" value="<?php echo $chat_id; ?>" name="reciever_userid" />
                  <input class="form-control"  type="text" placeholder="Type a message" name="message" />
                  <div class="input-group-append">
                    <button class="btn btn-secondary" name="chat_msg" type="submit">
                      <i class="fa fa-paper-plane"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
                <?php } ?>
  <footer role="contentinfo" class="py-6 lh-1 bg-white">
    <div class="container">
      <div class="row">
        <div class="col-md-2">
          <h3 class="h4 mb-4">Robust.</h3>
        </div>
        <div class="col-md-10">
          <div class="row">
            <div class="col-md-3 col-sm-6">
              <h4 class="h6">Address</h4>
              <address>
                <ul class="list-unstyled">
                  <li>
                    City Hall<br>
                    212  Street<br>
                    Lawoma<br>
                    735<br>
                  </li>
                </ul>
              </address>
            </div>
            <div class="col-md-3 col-sm-6">
              <h4 class="h6">Popular Services</h4>
              <ul class="list-unstyled">
                <li><a href="#">Payment Center</a></li>
                <li><a href="#">Contact Directory</a></li>
                <li><a href="#">Forms</a></li>
                <li><a href="#">News and Updates</a></li>
                <li><a href="#">FAQs</a></li>
              </ul>
            </div>
            <div class="col-md-3 col-sm-6">
              <h4 class="h6">Website Information</h4>
              <ul class="list-unstyled">
                <li><a href="#">Website Tutorial</a></li>
                <li><a href="#">Accessibility</a></li>
                <li><a href="#">Disclaimer</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Webmaster</a></li>
              </ul>
            </div>
            <div class="col-md-3 col-sm-6">
              <h4 class="h6">Company</h4>
              <ul class="list-unstyled">
                <li><a href="#">Our team</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="https://themes.getbootstrap.com/product/robust-ui-kit-dashboard-landing/" target="_blank">Purchase</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-12 text-center text-sm">
          <p class="mb-0">&copy; 2018 - <a href="index-2.html">Robust UI Kit</a>.</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="dist/js/bundle.js"></script>

  <script>
    const chatWindow = $('.chat-window')
    chatWindow.scrollTop(chatWindow.prop('scrollHeight'));
  </script>
   <script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location);
}
</script>
</body>

<!-- Mirrored from robust.bootlab.io/demo-chat.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 05 Aug 2019 16:50:12 GMT -->
</html>
<?php } ?>