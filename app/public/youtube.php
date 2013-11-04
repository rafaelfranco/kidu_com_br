<?php 
  
  include 'Zend/Gdata/YouTube.php';
  include 'Zend/Gdata/ClientLogin.php';
  
  
  $authenticationURL= 'https://www.google.com/accounts/ClientLogin';
  $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
              $username = 'rafaelphp@gmail.com',
              $password = 'mariogalaxy01',
              $service = 'youtube',
              $client = null,
              $source = 'Kidu', // a short string identifying your application
              $loginToken = null,
              $loginCaptcha = null,
              $authenticationURL);


  $developerKey = 'AI39si494VNAUgM0l2nziccttjVPhpqxg1RDmpQnh4i5K95_ezNax-KfYtSf5UQcThldcxKYs0Qe2w06NtaDi_zZXCkiy7rcWw';
  $applicationId = 'Kidu';
  $clientId = 'Kidu v1';

  $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);


  // create a new VideoEntry object
  $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

  // create a new Zend_Gdata_App_MediaFileSource object
  $filesource = $yt->newMediaFileSource('tmp/teste.mov');
  $filesource->setContentType('video/quicktime');
  // set slug header
  $filesource->setSlug('teste.mov');

  // add the filesource to the video entry
  $myVideoEntry->setMediaSource($filesource);

  $myVideoEntry->setVideoTitle('My Test Movie');
  $myVideoEntry->setVideoDescription('My Test Movie');
  // The category must be a valid YouTube category!
  $myVideoEntry->setVideoCategory('Autos');

  // Set keywords. Please note that this must be a comma-separated string
  // and that individual keywords cannot contain whitespace
  $myVideoEntry->SetVideoTags('cars, funny');

  // set some developer tags -- this is optional
  // (see Searching by Developer Tags for more details)
  $myVideoEntry->setVideoDeveloperTags(array('mydevtag', 'anotherdevtag'));

  // set the video's location -- this is also optional
  $yt->registerPackage('Zend_Gdata_Geo');
  $yt->registerPackage('Zend_Gdata_Geo_Extension');
  $where = $yt->newGeoRssWhere();
  $position = $yt->newGmlPos('37.0 -122.0');
  $where->point = $yt->newGmlPoint($position);
  $myVideoEntry->setWhere($where);

  // upload URI for the currently authenticated user
  $uploadUrl = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

  // try to upload the video, catching a Zend_Gdata_App_HttpException, 
  // if available, or just a regular Zend_Gdata_App_Exception otherwise
  try {
    $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
    $id = $newEntry->getVideoId(); 
  } catch (Zend_Gdata_App_HttpException $httpException) {
    echo $httpException->getRawResponseBody();
  } catch (Zend_Gdata_App_Exception $e) {
      echo $e->getMessage();
  }


?>