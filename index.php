<?php include("inc/header.php");?>

<?php

$rcm='~M601 S1\r\n';
$rim='~M115\r\n';
$rtm='~M105\r\n';
$rhm='~G28\r\n';
$progres='~M27\r\n';
$status='~M119\r\n';
$l_on='~M146 r255 g255 b255\r\n';
$cal='~M650\r\n';
$pause='~M25\r\n';
$resume='~M24\r\n';
$cancel='~M26\r\n';
$home='~G28\r\n';

$buf='';

if (($socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) and (socket_connect($socket, $address, $port))){
  $text="Connection successful on IP $address, port $port";
  socket_send($socket,utf8_encode($rcm),strlen($rcm),0);
  socket_recv($socket, $bufi, 1024, 0);

  if(isset($_POST['LED']) && $_POST['LED']=='SWITCH'){
    socket_send($socket,utf8_encode($l_on),strlen($l_on),0);
    socket_recv($socket, $buf, 1024, 0);
  }

  if(isset($_POST['PAUSE']) && $_POST['PAUSE']=='ON'){
    socket_send($socket,utf8_encode($pause),strlen($pause),0);
    socket_recv($socket, $buf, 1024, 0);
  }

  if(isset($_POST['RESUME']) && $_POST['RESUME']=='ON'){
    socket_send($socket,utf8_encode($resume),strlen($resume),0);
    socket_recv($socket, $buf, 1024, 0);
  }

  if(isset($_POST['STOP']) && $_POST['STOP']=='ON'){
    socket_send($socket,utf8_encode($cancel),strlen($cancel),0);
    socket_recv($socket, $buf, 1024, 0);
  }

  if(isset($_POST['HOME']) && $_POST['HOME']=='ON'){
    socket_send($socket,utf8_encode($home),strlen($home),0);
    socket_recv($socket, $buf, 1024, 0);
  }

  socket_send($socket,utf8_encode($rtm),strlen($rtm),0);
  socket_recv($socket, $buft, 1024, 0);

  socket_send($socket,utf8_encode($progres),strlen($progres),0);
  while(!socket_recv($socket, $bufp, 1024, 0));

  socket_send($socket,utf8_encode($status),strlen($status),0);
  while(!socket_recv($socket, $bufs, 1024, 0));

  // socket_recv($socket, $led, 1024, 0);
  socket_close($socket);

  $buft=explode('T0:',$buft);
  $buft=explode('T1:',$buft[1]);
  $temp=explode('/',$buft[0]);
  $temp_he=$temp[0];
  $temp_hes=$temp[1];
  $buft=explode('B:',$buft[1]);
  $buft=explode('ok',$buft[1]);
  $temp=explode('/',$buft[0]);
  $temp_bed=$temp[0];
  $temp_beds=$temp[1];
  //uprava informacii o progrese.
  $bufp=explode('byte',$bufp);
  $bufpp=explode('/',$bufp[1]);
  $hotovo=$bufpp[0];
  $layer=explode('Layer:',$bufp[1]);
  $layer=explode('ok',$layer[1]);
  $layer=$layer[0];
  //uprava informacii o subore
  $file=explode('CurrentFile:',$bufs);
  $files=explode('ok',$file[1]);
  $file=$files[0];
  //Uprava informacii o stave
  $stav=explode('MoveMode:',$bufs);
  $stavs=explode('Status: S:',$stav[1]);
  $stav=$stavs[0];
}
else

$text="Unable to connect<pre>".socket_strerror(socket_last_error())."</pre>";

?>

<main>
  <!-- STREAM BOX LIVE -->
  <div class="" id="">
    <section class="container-fluid">
      <div class="row justify-content-center">
        <div class="col p-0">
          <div id="streamBox" class="justify-content-center align-items-center mx-auto" style="display: flex; justify-content: center;">
            <!-- STREAM CAMERA HERE -->
            <div class="ratio ratio-4x3">
              <img align="center" src="http://<?php echo $address;?>:8080/?action=stream" title="Camera Stream"></img>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- INFORMATIONS PRINT -->
  <div class="album pt-4 pb-2 bg-body-tertiary" id="">
    <section class="container mb-3">
      <div class="row justify-content-md-center">
        <div class="col-md-1"></div>
        <div class="col-lg text-start">
          <p class="fs-3 mb-3 fw-lighter">
            <span class="text-secondary">File:</span> <?php echo $file;?>
            <span class="text-light fw-lighter float-end fs-6 pt-2">state : <i class="bi bi-circle-fill" data-bs-state="<?php echo $stav;?>" title="<?php echo $stav;?>"></i></span>
          </p>
          <p class="fs-2 text-primary fw-lighter mb-0"><?php echo $hotovo;?>%<span class="float-end fs-5 text-success pt-2">
            <!-- <span class="text-light fw-lighter">state : </span><i class="bi bi-circle-fill"></i></span> -->
            <span class="float-end fs-3 text-light pt-1"><svg class="bi my-1 theme-icon-active align-top" width="1.7em" height="1.7em"><use href="#filament"></use></svg><?php echo $layer;?></span>
          </p>
          <h6 class="text-secondary">Printing time</h6>
          <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="<?php echo $hotovo;?>%" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar"></div>
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
    </section>
  </div>

  <!-- CONTROL PRINT -->
  <div class="album py-0 bg-body-tertiary" id="">
    <section class="container text-center">
      <div class="row justify-content-md-center">
        <div class="col-md-1"></div>
        <div class="col-lg-10">
          <form action="index.php?ip=<?php echo $address;?>" method="post">
            <div class="btn-group my-gr" role="group" aria-label="Control">
              <button type="submit" id="pause-btn" class="btn btn-lg btn-outline-light" name="PAUSE" value="ON"><i class="bi bi-pause-circle"></i> pause print</button>
              <button type="submit" id="cancel-btn" class="btn btn-lg btn-outline-light" name="STOP" value="ON"><i class="bi bi-x-circle"></i> cancel print</button>
            </div>
          </form>
        </div>
        <div class="col-md-1"></div>
      </div>
    </section>
  </div>

  <!-- TEMPERATURE PRINT -->
  <div class="album py-3 bg-body-tertiary" id="">
    <section class="container text-center">
      <div class="row justify-content-md-center">
        <div class="col-md-1"></div>
        <div class="col-lg-10">
          <div class="btn-group my-gr" role="group" aria-label="control printer">
            <button type="button" id="hotend-btn" class="btn btn-lg btn-outline-light py-4" style="width:33.3333%">
              <svg class="bi theme-icon-active" width="2.2em" height="2.2em"><use href="#hotend"></use></svg>
              <div class="dynText">
                <span class="tempLive"><?php echo $temp_hes;?></span><span class="tempSet" id="hotendTemp"> / <?php echo $temp_he;?>°C</span>
              </div>
            </button>
            <button type="button" id="plate-btn" class="btn btn-lg btn-outline-light py-4" style="width:33.3333%">
              <svg class="bi theme-icon-active" width="2.2em" height="2.2em"><use href="#plate"></use></svg>
              <div class="dynText">
                <span class="tempLive"><?php echo $temp_beds;?></span><span class="tempSet" id="plateTemp"> / <?php echo $temp_bed;?>°C</span>
              </div>
            </button>
            <button type="button" id="air-btn" class="btn btn-lg btn-outline-light py-4" style="width:33.3333%">
              <svg class="bi theme-icon-active" width="2.2em" height="2.2em"><use href="#chamber"></use></svg>
              <div class="dynText">
                <span class="tempLive">0</span><span class="tempSet" id="chamberTemp"> / 0°C</span>
              </div>
            </button>
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
    </section>
  </div>

  <!-- CONTROL PRINTER -->
  <div class="album pb-3 bg-body-tertiary">
    <section class="container text-center">
      <div class="row justify-content-md-center">
        <div class="col-md-1"></div>
        <div class="col-lg-10">
          <form action="index.php?ip=<?php echo $address;?>" method="post">
            <div class="btn-group my-gr" role="group" aria-label="First group">
              <button type="button" id="file-btn" class="btn btn-lg btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <svg class="bi my-1 theme-icon-active" width="2em" height="2em"><use href="#file"></use></svg>
              </button>
              <button type="submit" id="light-btn" class="btn btn-lg btn-outline-light" name="LED" value="SWITCH">
                <svg class="bi my-1 theme-icon-active" width="2em" height="2em"><use href="#lightoff"></use></svg>
              </button>
              <button type="button" id="air-btn" class="btn btn-lg btn-outline-light">
                <svg class="bi my-1 theme-icon-active" width="2em" height="2em"><use href="#air"></use></svg>
              </button>
            </div>
          </form>
        </div>
        <div class="col-md-1"></div>
      </div>
    </section>
  </div>

  <nav class="navbar fixed-bottom" data-menu-height="460" style="">
    <div class="container">
      <div class="offcanvas offcanvas-bottom" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="row justify-content-center">
          <div class="col-sm-11 col-lg-8 col-11">
            <div class="offcanvas-header my-4 p-0">
              <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">File Informations</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
          </div>
          <div class="offcanvas-body container">
            <div class="row justify-content-center">
              <div class="col-sm-6 col-lg-4 col-6">
                <div class="mb-4"><svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#material"></use></svg>PETG</div>
                <div class="mb-4"><svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#speed"></use></svg>0mm/s</div>
              </div>
              <div class="col-sm-5 col-lg-4 col-5">
                <div class="mb-4"><svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#layers"></use></svg><?php echo $layer;?></div>
                <div class="mb-4"><svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#infill"></use></svg>0%</div>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-sm-6 col-lg-4 col-6">
                <div class="mb-4">
                  <div class="input-group">
                    <svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#speedset"></use></svg>
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-number"  data-type="minus" data-field="speedPrint">
                        <i class="bi bi-dash-circle-fill fs-4"></i>
                      </button>
                    </span>
                    <input type="text" name="speedPrint" class="form-control input-number text-center" value="100%" min="1" max="100">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-number" data-type="plus" data-field="speedPrint">
                        <i class="bi bi-plus-circle-fill fs-4"></i>
                      </button>
                    </span>
                  </div>
                </div>
                <div class="mb-4">
                  <div class="input-group">
                    <svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#zoffset"></use></svg>
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-number" data-type="minus" data-field="zOff">
                        <i class="bi bi-dash-circle-fill fs-4"></i>
                      </button>
                    </span>
                    <input type="text" name="zOff" class="form-control input-number text-center" value="1" min="1" max="5">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-number" data-type="plus" data-field="zOff">
                        <i class="bi bi-plus-circle-fill fs-4"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-5 col-lg-4 col-5">
                <div class="mb-4">
                  <div class="input-group">
                    <svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#extruder"></use></svg>
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-number"  data-type="minus" data-field="setExtruder">
                        <i class="bi bi-dash-circle-fill fs-4"></i>
                      </button>
                    </span>
                    <input type="text" name="setExtruder" class="form-control input-number text-center" value="0%" min="0" max="100">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-number" data-type="plus" data-field="setExtruder">
                        <i class="bi bi-plus-circle-fill fs-4"></i>
                      </button>
                    </span>
                  </div>
                </div>
                <div class="mb-4">
                  <div class="input-group">
                    <svg class="bi my-1 theme-icon-active me-4" width="2em" height="2em"><use href="#cooling"></use></svg>
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-number"  data-type="minus" data-field="setCooling">
                        <i class="bi bi-dash-circle-fill fs-4"></i>
                      </button>
                    </span>
                    <input type="text" name="setCooling" class="form-control input-number text-center" value="0%" min="0" max="100">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-number" data-type="plus" data-field="setCooling">
                        <i class="bi bi-plus-circle-fill fs-4"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </nav>

</main>

<?php include("inc/footer.php");?>
