<?php include 'checklogin.php';  ?>
<!doctype html>
<html lang="en">
  <?php include 'adminheader.php'; ?>
  <body>
  <?php include 'adminnav.php'; ?>


  <div class="container-fluid">
  <div class="row">

    <?php include 'adminsidebarMenu.php'; ?>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
    <canvas hidden class="my-4 w-100" id="myChart" width="900" height="380"></canvas>

      <div id="load_contentadmin">

      </div>

    </main>
  </div>
  </div>



    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="../../js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="dashboard.js"></script>
  </body>
</html>
