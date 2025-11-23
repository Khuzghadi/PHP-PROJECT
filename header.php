<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nazafat Admin Login</title>
  <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
  .hero-header {
    background: linear-gradient(45deg, #e4f2b4, #bde1d7);
    border-bottom: 2px solid rgb(0 96 51 / 40%);
  }
  div {
    overflow: hidden;
}
* {
    font-family: 'Rubik', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
.header-top {
    height: 135px;
    background-image: url("imgs/header-bg.png"); 
    background-image: url("imgs/header-top-bg.png");
    background-size: 35%;
    background-position: bottom;
    background-repeat: repeat-x;
}
@media only screen and (max-width: 1024px) {
    .header-top-inner {
        max-width: 98%;
    }
}
.header-top-inner
 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 72%;
    margin: 0 auto;
}
.header-image {
    width: 130px !important;;
}
.header-image1 {
    width: 445px !important;;
}
img, svg {
    vertical-align: middle;
}
.container, .container-fluid, .container-lg
 {
    width: 100%;
    padding-right: var(--bs-gutter-x, .75rem);
    padding-left: var(--bs-gutter-x, .75rem);
    margin-right: auto;
    margin-left: auto;
}
</style>
<body>
  <div class="hero-header homepage"> 
    <div class="header-top">
      <div class="header-top-inner">
        <div>
        <button onclick="toggleSidebar()" id="toggleBtn" class="btn btn-outline-dark mb-3">☰</button>
      </div>
        <div class="nazafat-logo">
          <a href="Dashboard.php">
            <img src="imgs/Logo_Nazafat_V2.png" class="header-image" alt="Nazafat Logo" />
          </a>
        </div>
        <div class="al-minal-logo">
          <img src="imgs/minal-iman-logo.png" class="header-image1" alt="Al Minal Logo" />
        </div>
        <div class="health-logo">
          <img src="imgs/health-logo.png" class="header-image" alt="Umoor-Sehat" />
        </div>
      </div>
    </div>
</body>
<script>
function toggleSidebar() {
  var doc = top.document;
  var fs = doc.getElementById('mainFrameset') || doc.getElementsByTagName('frameset')[1];
  if (!fs) return;
  var cols = (fs.getAttribute('cols') || '').replace(/\s+/g,'');
  var first = cols.split(',')[0];
  var n = parseFloat(first);
  var expanded = first.endsWith('%') ? n > 6 : n > 60;
  fs.setAttribute('cols', expanded ? '50px,*' : '13.5%,*');
}
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
